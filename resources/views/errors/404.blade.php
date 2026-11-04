<x-layouts.public :title="__('Page introuvable')" noindex>
    <div class="container-page py-16 text-center sm:py-24">
        <p class="font-display text-[clamp(6rem,22vw,14rem)] leading-none text-orange [font-stretch:70%]">404</p>
        <h1 class="mt-2 text-4xl sm:text-5xl">{{ __('Cette paire s\'est fait la malle') }}</h1>
        <p class="mx-auto mt-4 max-w-md text-gris">{{ __('La page n\'existe pas ou le modèle n\'est plus en vente.') }}</p>
        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('shop') }}" class="btn-primary">{{ __('Voir les chaussures') }}<x-icon name="arrow" class="size-5" /></a>
            <a href="{{ route('home') }}" class="btn-outline">{{ __('Accueil') }}</a>
        </div>
    </div>
</x-layouts.public>
