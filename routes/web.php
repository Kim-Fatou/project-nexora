<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcranDemarrageController; // Vérifie bien cette ligne en haut du fichier

// Home (alias) -> écran de démarrage
Route::get('/', function () {
    return redirect()->route('ecran-demarrage');
})->name('home');


Route::get('/ecran-demarrage', function () {
    return view('ecran-demarrage');
})->name('ecran-demarrage');

Route::get('/integration1', function () {
    return view('integration1');
})->name('integration1');

Route::get('/integration2', function () {
    return view('integration2');
})->name('integration2');

Route::get('/integration3', function () {
    return view('integration3');
})->name('integration3');

Route::get('/matching', function () {
    return view('matching');
})->name('matching');

Route::get('/match', function () {
    return view('match');
})->name('match');

Route::get('/chat', function () {
    return view('chat');
})->name('chat');

Route::get('/appel', function () {
    return view('appel');
})->name('appel');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/apropos', function () {
    return view('apropos');
})->name('apropos');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Route pour la page paramètres
Route::get('/Parametre', function () {
    return view('Parametre');
})->name('Parametre');

// Routes pour l'authentification classique
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes pour l'authentification Socialite
Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('social.callback');
