<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class CompanionAiController extends Controller
{
    /**
     * Génère les messages de NEXIA pour une étape du parcours d'onboarding
     * (companion.blade.php / companion.js), en s'appuyant sur l'API Gemini
     * (même fournisseur que le chatbot NEXIA de la landing page) pour
     * personnaliser le ton en fonction du prénom, des centres d'intérêt et
     * des réponses déjà données par l'utilisateur.
     *
     * Si la clé API est absente ou que l'appel échoue, on retombe sur des
     * messages fixes (voir fallback()) pour ne jamais bloquer l'onboarding.
     */
    public function reply(Request $request)
    {
        $userId = Auth::id();

        // Anti-abus : max 20 appels par minute par utilisateur (5 étapes,
        // largement suffisant même en cas de rejeu de la page).
        if (RateLimiter::tooManyAttempts('companion-ai:' . $userId, 20)) {
            return response()->json([
                'messages' => $this->fallback($request->integer('step'), 'toi'),
            ], 429);
        }
        RateLimiter::hit('companion-ai:' . $userId, 60);

        $validated = $request->validate([
            'step' => 'required|integer|min:1|max:5',
            'answers' => 'nullable|array',
            'answers.wish' => 'nullable|string|max:100',
            'answers.pref' => 'nullable|string|max:100',
            'answers.moment' => 'nullable|string|max:100',
        ]);

        $step = $validated['step'];
        $answers = $validated['answers'] ?? [];

        $user = Auth::user();
        $userName = explode(' ', $user->name)[0] ?: 'toi';
        $interests = $user->interests()->pluck('name')->toArray();

        $apiKey = config('services.gemini.key');
        if (!$apiKey || $apiKey === 'votre_cle_api_gemini_ici') {
            return response()->json(['messages' => $this->fallback($step, $userName)]);
        }

        try {
            $response = Http::withHeaders([
                'x-goog-api-key' => $apiKey,
            ])->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent",
                [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $this->buildPrompt($step, $userName, $interests, $answers)]],
                        ],
                    ],
                    'systemInstruction' => [
                        'parts' => [['text' =>
                            "Tu es NEXIA, l'assistante IA chaleureuse et enjouée de Nexora, un réseau "
                            . "social basé sur les passions (pas sur des algorithmes). Tu accompagnes un "
                            . "nouvel utilisateur dans son onboarding, message par message. Consignes "
                            . "strictes : réponds UNIQUEMENT avec un tableau JSON de chaînes de caractères "
                            . "en français (exemple : [\"Premier message.\", \"Deuxième message.\"]), sans "
                            . "aucun texte avant ou après, sans balises markdown. Chaque message fait 1 à 2 "
                            . "phrases courtes, chaleureuses, avec au maximum 1 emoji.",
                        ]],
                    ],
                ]
            );

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text') ?? '';
                $clean = trim(preg_replace('/```json|```/', '', $text));
                $messages = json_decode($clean, true);

                if (is_array($messages) && count($messages) > 0) {
                    // Sécurité : on ne garde que des chaînes, max 3 messages
                    $messages = array_values(array_filter(array_slice($messages, 0, 3), 'is_string'));

                    if (count($messages) > 0) {
                        return response()->json(['messages' => $messages]);
                    }
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('Companion IA (Gemini) a échoué', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Companion IA (Gemini) exception', ['message' => $e->getMessage()]);
        }

        return response()->json(['messages' => $this->fallback($step, $userName)]);
    }

    /**
     * Construit le prompt utilisateur envoyé à Gemini pour une étape donnée,
     * en injectant le contexte (prénom, intérêts, réponses précédentes).
     */
    private function buildPrompt(int $step, string $userName, array $interests, array $answers): string
    {
        $interestsList = count($interests) ? implode(', ', $interests) : 'pas encore renseignés';

        return match ($step) {
            1 => "Prénom de l'utilisateur : {$userName}. Ses centres d'intérêt : {$interestsList}. "
                . "C'est le tout premier contact. Donne-lui un message de bienvenue chaleureux qui le/la "
                . "salue par son prénom, éventuellement un mot sur ses centres d'intérêt s'il y en a, puis "
                . "termine par une question lui demandant ce qui lui ferait le plus plaisir sur Nexora en "
                . "ce moment (sans lui donner de choix précis, l'interface affichera des boutons après).",

            2 => "Prénom : {$userName}. Il/elle vient de répondre \"{$answers['wish']}\" à la question de ce "
                . "qu'il/elle a envie de faire sur Nexora. Valorise brièvement ce choix, puis demande s'il/elle "
                . "préfère papoter en petit groupe ou en tête-à-tête.",

            3 => "Prénom : {$userName}. Il/elle a choisi le format \"{$answers['pref']}\". Valide ce choix "
                . "brièvement, puis demande s'il/elle est plutôt actif(ve) le matin, l'après-midi, ou le soir.",

            4 => "Prénom : {$userName}. Son moment préféré pour être actif(ve) : \"{$answers['moment']}\". "
                . "Fais un commentaire bref à ce sujet, puis demande s'il/elle est prêt(e) à voir qui partage "
                . "vraiment ses passions sur Nexora.",

            5 => "Prénom : {$userName}. Centres d'intérêt : {$interestsList}. Résume en 2 messages "
                . "enthousiastes que son fil/chat l'attend maintenant sur Nexora, en te basant si possible "
                . "sur ses centres d'intérêt. Termine sur une note chaleureuse et pressée de le/la voir.",

            default => "Dis simplement bonjour à {$userName}.",
        };
    }

    /**
     * Messages de repli fixes si l'API Gemini est indisponible ou non
     * configurée — l'onboarding ne doit jamais rester bloqué.
     */
    private function fallback(int $step, string $userName): array
    {
        return match ($step) {
            1 => [
                "Bonjour {$userName} ! 👋 Ravi de te rencontrer.",
                "Pour commencer, dis-moi : qu'est-ce qui te ferait le plus plaisir sur Nexora en ce moment ?",
            ],
            2 => [
                "Parfait, c'est exactement ce que Nexora fait de mieux !",
                "Tu préfères plutôt papoter en petit groupe, ou en tête-à-tête ?",
            ],
            3 => [
                "Bien noté !",
                "Une dernière chose : tu es plutôt actif(ve) le matin, l'après-midi, ou le soir ?",
            ],
            4 => [
                "Noté !",
                "Tu es prêt(e) à voir qui partage vraiment tes passions ?",
            ],
            5 => [
                "Alors n'attendons plus... 🚀",
                "Ton chat t'attend, {$userName}. À tout de suite !",
            ],
            default => ["Bonjour {$userName} !"],
        };
    }
}
