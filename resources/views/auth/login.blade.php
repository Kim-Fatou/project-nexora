<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion / Inscription - Nexora</title>
    <!-- Chargement de Tailwind CSS pour un design rapide et moderne -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chargement de FontAwesome pour les icônes des réseaux sociaux -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Styles personnalisés pour des boutons sociaux attractifs */
        .btn-social {
            display: flex; /* Aligner l'icône et le texte */
            align-items: center; /* Centrer verticalement */
            justify-content: center; /* Centrer horizontalement */
            width: 100%; /* Largeur totale */
            padding: 0.75rem; /* Espace intérieur */
            border-radius: 0.5rem; /* Coins arrondis */
            font-weight: 600; /* Texte en gras */
            transition: all 0.3s ease; /* Animation fluide au survol */
            margin-bottom: 0.75rem; /* Espacement en bas */
            color: white; /* Couleur du texte par défaut */
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* Ombre légère */
        }
        .btn-social i {
            margin-right: 0.5rem; /* Espace entre l'icône et le texte */
            font-size: 1.25rem; /* Taille de l'icône */
        }
        /* Couleurs spécifiques à chaque réseau social */
        .btn-google { background-color: #DB4437; }
        .btn-google:hover { background-color: #C23321; }
        .btn-github { background-color: #333333; }
        .btn-github:hover { background-color: #1a1a1a; }
        .btn-twitter { background-color: #1DA1F2; } /* Twitter / X */
        .btn-twitter:hover { background-color: #0c85d0; }
        .btn-apple { background-color: #000000; }
        .btn-apple:hover { background-color: #333333; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        
        <!-- Affichage des messages de succès -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Affichage des messages d'erreur -->
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Bienvenue sur Nexora</h2>

        <!-- Navigation entre Connexion et Inscription -->
        <div class="flex justify-center space-x-4 mb-6">
            <button id="btn-login" class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1" onclick="showForm('login')">Connexion</button>
            <button id="btn-register" class="text-gray-500 font-bold pb-1" onclick="showForm('register')">Inscription</button>
        </div>

        <!-- Formulaire de Connexion -->
        <form id="form-login" action="{{ route('login') }}" method="POST">
            @csrf <!-- Jeton de sécurité obligatoire dans Laravel -->
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" name="email" id="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-semibold mb-2">Mot de passe</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">
                Se connecter avec un Email
            </button>
        </form>

        <!-- Formulaire d'Inscription (Masqué par défaut) -->
        <form id="form-register" action="{{ route('register') }}" method="POST" class="hidden">
            @csrf
            
            <div class="mb-4">
                <label for="nom" class="block text-gray-700 font-semibold mb-2">Nom</label>
                <input type="text" name="nom" id="nom" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="prenom" class="block text-gray-700 font-semibold mb-2">Prénom</label>
                <input type="text" name="prenom" id="prenom" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="email_reg" class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" name="email" id="email_reg" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-4">
                <label for="password_reg" class="block text-gray-700 font-semibold mb-2">Mot de passe</label>
                <input type="password" name="password" id="password_reg" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-700 transition duration-300">
                Créer mon compte
            </button>
        </form>

        <!-- Ligne de séparation visuelle -->
        <div class="flex items-center my-6">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="mx-4 text-gray-500 font-semibold">OU</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <!-- Boutons de connexion complets via les réseaux sociaux -->
        <div class="space-y-3">
            
            <!-- Bouton Google -->
            <a href="{{ route('social.redirect', 'google') }}" class="btn-social btn-google">
                <i class="fab fa-google"></i> Se connecter avec Google
            </a>

            <!-- Bouton GitHub -->
            <a href="{{ route('social.redirect', 'github') }}" class="btn-social btn-github">
                <i class="fab fa-github"></i> Se connecter avec GitHub
            </a>

            <!-- Bouton X / Twitter -->
            <a href="{{ route('social.redirect', 'twitter-oauth-2') }}" class="btn-social btn-twitter">
                <i class="fab fa-twitter"></i> Se connecter avec X
            </a>

            <!-- Bouton Apple / iCloud -->
            <a href="{{ route('social.redirect', 'apple') }}" class="btn-social btn-apple">
                <i class="fab fa-apple"></i> Se connecter avec Apple
            </a>

        </div>

    </div>

    <script>
        function showForm(type) {
            const loginForm = document.getElementById('form-login');
            const registerForm = document.getElementById('form-register');
            const btnLogin = document.getElementById('btn-login');
            const btnRegister = document.getElementById('btn-register');

            if (type === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                
                btnLogin.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                btnLogin.classList.remove('text-gray-500');
                
                btnRegister.classList.add('text-gray-500');
                btnRegister.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                
                btnRegister.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                btnRegister.classList.remove('text-gray-500');
                
                btnLogin.classList.add('text-gray-500');
                btnLogin.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            }
        }
    </script>
</body>
</html>
