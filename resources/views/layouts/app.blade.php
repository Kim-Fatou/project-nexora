<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nexora</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logos/logonexora.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @if(!isset($slot))
        <link rel="stylesheet" href="{{ asset('css/auth.css') }}" />
    @endif
</head>
<body>

    <!-- ⚡ Pour les composants Volt / Livewire (chat, socialnet, dashboard...) -->
    @if(isset($slot))
        {{ $slot }}
    @endif

    <!-- 🔒 Pour les vues Blade classiques (login, register) -->
    @yield('content')

</body>
</html>

