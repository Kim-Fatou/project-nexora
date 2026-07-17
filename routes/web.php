<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\ChatAttachmentController;
use App\Http\Controllers\RoomAttachmentController; // contrôleur qui sert les fichiers joints des salons
use App\Http\Controllers\StoryAttachmentController;
use App\Http\Controllers\PostAttachmentController;
use GuzzleHttp\Middleware;
use App\Http\Controllers\SocialAuthController;

Route::get('/', function () {
    return view('welcome');
});



// Sert les pièces jointes du chat (images/fichiers) — protégée par 'auth'
// ET par une vérification supplémentaire dans le contrôleur.
Route::get('/chat/attachment/{message}', [ChatAttachmentController::class, 'show'])
    ->middleware('auth')
    ->name('chat.attachment');


// Sert les pièces jointes des messages de SALON — même logique de sécurité
// que pour le chat privé (vérifie que tu es bien membre avant de donner le fichier)
Route::get('/room/attachment/{message}', [RoomAttachmentController::class, 'show'])
    ->middleware('auth')
    ->name('room.attachment');



    // Sert les images de STORY — visible seulement par l'auteur ou ses amis (connexion acceptée)
Route::get('/story/attachment/{story}', [StoryAttachmentController::class, 'show'])
    ->middleware('auth')
    ->name('story.attachment');




    // Sert les images des publications du flux — visible par tout utilisateur connecté (flux public)
Route::get('/post/attachment/{post}', [PostAttachmentController::class, 'show'])
    ->middleware('auth')
    ->name('post.attachment');



/*
Route::get('/chat', function () {
    return view('homechat');
});//->middleware('auth')->name('homechat');


Route::get('/dashboard', function () {
    return view('dash.dashboardhome');
});//->middleware('dash')->name('dashboardhomme'); */
Volt::route('/chat', 'socialnet')->middleware(['auth', 'verified'])->name('chat');
Volt::route('/interests', 'interests')->middleware(['auth', 'verified'])->name('interests');
Volt::route('/dashboard', 'dash.dashboardhome')->middleware(['auth', 'verified'])->name('dashboard');
Volt::route('/socialnet', 'socialnet')->middleware(['auth', 'verified'])->name('socialnet');
Volt::route('/companion', 'companion')->middleware(['auth', 'verified'])->name('companion');
Volt::route('/profile', 'profile')->middleware(['auth', 'verified'])->name('profile');
Volt::route('/fonction', 'fonctionnalites')->name('fonctionnalites');
Volt::route('/propos', 'propos')->name('propos');
Volt::route('/communaute', 'communaute')->name('communaute');
Volt::route('/contact', 'contact')->name('contact');
Volt::route('/admin', 'dash.admin')->middleware(['auth', 'verified'])->name('admin');

// Social authentication routes
Route::get('/auth/redirect/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/callback/{provider}', [SocialAuthController::class, 'callback'])->name('social.callback');
Route::post('/auth/callback/{provider}/mock', [SocialAuthController::class, 'mockCallback'])->name('social.callback.mock');




Route::post('/nexia/ask', function (Illuminate\Http\Request $request) {
    $ip = $request->ip();
    if (Illuminate\Support\Facades\RateLimiter::tooManyAttempts('nexia-public:'.$ip, 5)) {
        return response()->json([
            'reply' => "Tu poses des questions un peu trop vite, attends une minute 😊"
        ], 429);
    }
    Illuminate\Support\Facades\RateLimiter::hit('nexia-public:'.$ip, 60);

    $validated = $request->validate([
        'message' => 'required|string|max:1000',
        'history' => 'nullable|array',
    ]);

    $userMessage = $validated['message'];
    $history = $validated['history'] ?? [];

    $apiKey = config('services.gemini.key');
    if (!$apiKey || $apiKey === 'votre_cle_api_gemini_ici') {
        \Illuminate\Support\Facades\Log::error('NEXIA : Clé API Gemini manquante ou configurée par défaut dans services.php.');
        return response()->json([
            'reply' => "Désolé, NEXIA n'est pas encore totalement configurée (clé API manquante)."
        ]);
    }

    $contents = [];
    foreach ($history as $h) {
        if (isset($h['role']) && isset($h['content'])) {
            $contents[] = [
                'role' => $h['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $h['content']]]
            ];
        }
    }
    $contents[] = [
        'role' => 'user',
        'parts' => [['text' => $userMessage]]
    ];

    try {
        $http = Illuminate\Support\Facades\Http::asJson();
        if (config('app.env') === 'local') {
            $http = $http->withoutVerifying();
        }

        $response = $http->withHeaders([
                'x-goog-api-key' => $apiKey,
            ])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent", [
                'contents' => $contents,
                'systemInstruction' => [
                    'parts' => [['text' => "Tu es NEXIA, l'assistante IA de Nexora, un réseau social basé sur les passions. Réponds de façon très brève (maximum 2-3 sentences), chaleureuse et utile, en français. Propose-leur d'explorer Nexora, et s'ils n'ont pas encore de compte, encourage-les chaleureusement à en créer un. Rappelle-leur qu'ici on se connecte par passions, pas par algorithmes !"]]
                ]
            ]);

        if ($response->successful()) {
            $jsonData = $response->json();
            $reply = $jsonData['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($reply) {
                return response()->json(['reply' => $reply]);
            } else {
                \Illuminate\Support\Facades\Log::warning('NEXIA : Réponse JSON Gemini invalide ou structure inattendue.', [
                    'json' => $jsonData
                ]);
            }
        } else {
            \Illuminate\Support\Facades\Log::warning('NEXIA (Gemini) a échoué', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('NEXIA (Gemini) exception', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    return response()->json([
        'reply' => "L'assistante NEXIA est momentanément indisponible. Réessaie dans un instant !"
    ]);
});

// Premium checkout routes
Route::get('/premium/checkout', function () {
    // 1. Check FedaPay (West Africa Mobile Money / Card)
    $fedapayKey = env('FEDAPAY_SECRET_KEY');
    if ($fedapayKey) {
        try {
            $env = str_starts_with($fedapayKey, 'sk_live') ? 'live' : 'sandbox';
            $baseUrl = $env === 'live' ? 'https://api.fedapay.com/v1' : 'https://sandbox-api.fedapay.com/v1';
            
            $user = auth()->user();
            $response = Illuminate\Support\Facades\Http::withToken($fedapayKey)
                ->post("$baseUrl/transactions", [
                    'description' => 'Abonnement Premium Nexora',
                    'amount' => 3270, // ~ 5 EUR in FCFA (XOF)
                    'currency' => ['iso' => 'XOF'],
                    'callback_url' => route('premium.checkout.fedapay.callback'),
                    'customer' => [
                        'firstname' => $user->username,
                        'email' => $user->email,
                    ]
                ]);
            
            if ($response->successful()) {
                $transactionId = null;
                $data = $response->json();
                if (isset($data['v1/transaction']['id'])) {
                    $transactionId = $data['v1/transaction']['id'];
                } elseif (isset($data['transaction']['id'])) {
                    $transactionId = $data['transaction']['id'];
                }
                
                if ($transactionId) {
                    $tokenResponse = Illuminate\Support\Facades\Http::withToken($fedapayKey)
                        ->post("$baseUrl/transactions/$transactionId/token");
                    
                    if ($tokenResponse->successful()) {
                        $tokenData = $tokenResponse->json();
                        $url = $tokenData['token']['url'] ?? $tokenData['url'] ?? null;
                        if ($url) {
                            return redirect()->away($url);
                        }
                    }
                }
            }
            \Illuminate\Support\Facades\Log::error('FedaPay transaction initiation failed: ' . $response->body());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('FedaPay exception: ' . $e->getMessage());
        }
    }

    // 2. Check CinetPay (West Africa Mobile Money / Card)
    $cinetpayKey = env('CINETPAY_API_KEY');
    $cinetpaySiteId = env('CINETPAY_SITE_ID');
    if ($cinetpayKey && $cinetpaySiteId) {
        try {
            $user = auth()->user();
            $transactionId = 'NX_' . time() . '_' . $user->id;
            
            $response = Illuminate\Support\Facades\Http::post("https://api-checkout.cinetpay.com/api/v1/payment", [
                'apikey' => $cinetpayKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $transactionId,
                'amount' => 3270, // 3270 FCFA
                'currency' => 'XOF',
                'description' => 'Abonnement Premium Nexora',
                'customer_id' => (string)$user->id,
                'customer_name' => $user->username,
                'customer_surname' => $user->username,
                'customer_email' => $user->email,
                'notify_url' => route('premium.checkout.cinetpay.notify'),
                'return_url' => route('premium.checkout.cinetpay.callback'),
                'channels' => 'ALL'
            ]);
            
            if ($response->successful() && $response->json('code') == '201') {
                $paymentUrl = $response->json('data.payment_url');
                if ($paymentUrl) {
                    return redirect()->away($paymentUrl);
                }
            }
            \Illuminate\Support\Facades\Log::error('CinetPay transaction initiation failed: ' . $response->body());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('CinetPay exception: ' . $e->getMessage());
        }
    }

    // 3. Fallback to Stripe
    $stripeKey = env('STRIPE_SECRET');
    if ($stripeKey) {
        try {
            \Stripe\Stripe::setApiKey($stripeKey);
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => env('STRIPE_PREMIUM_PRICE_ID'),
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('dashboard') . '?payment=success',
                'cancel_url' => route('dashboard') . '?payment=cancel',
                'client_reference_id' => auth()->id(),
            ]);
            return redirect()->away($session->url);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Stripe session creation failed: ' . $e->getMessage());
        }
    }

    // 4. Default to Mock checkout locally if no key is configured
    return redirect()->route('premium.checkout.mock');
})->middleware(['auth', 'verified'])->name('premium.checkout');

Route::get('/premium/fedapay/callback', function (\Illuminate\Http\Request $request) {
    $status = $request->query('status');
    if ($status === 'approved') {
        $user = auth()->user();
        if ($user) {
            $user->update([
                'is_premium' => true,
                'premium_expires_at' => now()->addMonth(),
            ]);
            $user->subscriptions()->updateOrCreate([
                'plan_type' => 'premium',
            ], [
                'status' => 'active',
                'ends_at' => now()->addMonth(),
            ]);
            return redirect()->to(route('dashboard') . '?payment=success');
        }
    }
    return redirect()->to(route('dashboard') . '?payment=cancel');
})->middleware(['auth', 'verified'])->name('premium.checkout.fedapay.callback');

Route::get('/premium/cinetpay/callback', function () {
    $user = auth()->user();
    if ($user) {
        $user->update([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ]);
        $user->subscriptions()->updateOrCreate([
            'plan_type' => 'premium',
        ], [
            'status' => 'active',
            'ends_at' => now()->addMonth(),
        ]);
        return redirect()->to(route('dashboard') . '?payment=success');
    }
    return redirect()->to(route('dashboard') . '?payment=cancel');
})->middleware(['auth', 'verified'])->name('premium.checkout.cinetpay.callback');

Route::post('/premium/cinetpay/notify', function () {
    return response('OK', 200);
})->name('premium.checkout.cinetpay.notify');

Route::get('/premium/checkout-mock', function () {
    return view('auth.checkout-mock');
})->middleware(['auth', 'verified'])->name('premium.checkout.mock');

Route::post('/premium/checkout-mock', function () {
    $user = auth()->user();
    if ($user) {
        $user->update([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ]);
        $user->subscriptions()->create([
            'status' => 'active',
            'plan_type' => 'premium',
            'ends_at' => now()->addMonth(),
        ]);
    }
    return redirect()->to(route('dashboard') . '?payment=success');
})->middleware(['auth', 'verified'])->name('premium.checkout.mock.post');

Route::post('/stripe/webhook', function (\Illuminate\Http\Request $request) {
    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

    try {
        $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
    } catch (\UnexpectedValueException $e) {
        return response('Invalid payload', 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        return response('Invalid signature', 400);
    }

    if ($event->type === 'checkout.session.completed') {
        $session = $event->data->object;
        $userId = $session->client_reference_id;
        $user = \App\Models\User::find($userId);
        if ($user) {
            $user->update([
                'is_premium' => true,
                'premium_expires_at' => now()->addMonth(),
            ]);
            $user->subscriptions()->create([
                'status' => 'active',
                'plan_type' => 'premium',
                'ends_at' => now()->addMonth(),
            ]);
        }
    } elseif ($event->type === 'customer.subscription.deleted') {
        $subscription = $event->data->object;
        $stripeSecret = env('STRIPE_SECRET');
        \Stripe\Stripe::setApiKey($stripeSecret);
        try {
            $customer = \Stripe\Customer::retrieve($subscription->customer);
            if (!empty($customer->email)) {
                $user = \App\Models\User::where('email', $customer->email)->first();
                if ($user) {
                    $user->update([
                        'is_premium' => false,
                        'premium_expires_at' => now(),
                    ]);
                    $user->subscriptions()->where('status', 'active')->update([
                        'status' => 'expired',
                        'ends_at' => now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Stripe webhook customer retrieval failed: ' . $e->getMessage());
        }
    }

    return response('Webhook Handled', 200);
});