<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        if (!in_array($provider, ['google', 'apple'])) {
            abort(404);
        }

        $clientId = config("services.{$provider}.client_id");

        if (empty($clientId)) {
            // Fallback to simulation view in local/development environments
            return view('auth.social-mock', [
                'provider' => $provider,
            ]);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        if (!in_array($provider, ['google', 'apple'])) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'La connexion sociale a échoué ou a été annulée.']);
        }

        // Find or create user
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Utilisateur',
                'email' => $socialUser->getEmail(),
                'username' => $provider . '_' . Str::random(6),
                'password' => bcrypt(Str::random(24)),
                'email_verified_at' => now(), // Social logins verify email implicitly
            ]);
        }

        Auth::login($user);

        return redirect()->route('socialnet');
    }

    public function mockCallback(string $provider)
    {
        if (!in_array($provider, ['google', 'apple'])) {
            abort(404);
        }

        $mockEmail = $provider . '_tester@nexora.com';
        $mockName = ucfirst($provider) . ' Tester';

        $user = User::where('email', $mockEmail)->first();

        if (!$user) {
            $user = User::create([
                'name' => $mockName,
                'email' => $mockEmail,
                'username' => $provider . '_test',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user);

        return redirect()->route('socialnet');
    }
}
