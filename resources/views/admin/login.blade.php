<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('Connexion') }} · Abidjan Shop</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css'])
</head>
<body class="grid min-h-dvh bg-craie lg:grid-cols-2">
    <div class="relative isolate hidden flex-col justify-between overflow-hidden bg-encre p-12 text-white lg:flex">
        <a href="{{ route('home') }}" class="text-2xl"><x-logo tagline /></a>
        <div aria-hidden="true" class="absolute -right-24 -bottom-24 -z-10 size-[30rem] rounded-full bg-vert"></div>
        <x-shoe silhouette="basket" hex="#f6f3ee" class="absolute right-8 bottom-24 -z-10 w-[70%] -rotate-12" />
        <h2 class="max-w-sm text-5xl">{{ __('Votre boutique, ouverte 24 h/24') }}</h2>
    </div>
    <div class="flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-sm">
            <a href="{{ route('home') }}" class="text-xl lg:hidden"><x-logo /></a>
            <p class="eyebrow mt-8 text-gris lg:mt-0">{{ __('Administration') }}</p>
            <h1 class="mt-2 text-5xl">{{ __('Connexion') }}</h1>
            <form method="post" action="{{ route('admin.login.attempt') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="email" class="field-label">{{ __('Identifiant (e-mail)') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="field">
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="field-label">{{ __('Mot de passe') }}</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" class="field">
                </div>
                <label class="flex items-center gap-2 text-sm text-gris"><input type="checkbox" name="remember" value="1" class="size-4 accent-encre"> {{ __('Rester connecté') }}</label>
                <button class="btn-dark w-full py-3.5">{{ __('Se connecter') }}<x-icon name="arrow" class="size-4" /></button>
            </form>
            <p class="mt-6 text-center text-sm"><a href="{{ route('home') }}" class="font-medium text-gris hover:text-encre">← {{ __('Retour au site') }}</a></p>
        </div>
    </div>
</body>
</html>
