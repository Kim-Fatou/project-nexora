<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Juste après la création du compte, on envoie l'utilisateur dans le
     * parcours d'onboarding (centres d'intérêt → profil → compagnon),
     * au lieu du raccourci direct vers /socialnet utilisé pour les
     * connexions classiques (config('fortify.home')).
     */
    public function toResponse($request)
    {
        return redirect()->route('interests');
    }
}