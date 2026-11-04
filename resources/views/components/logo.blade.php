@props(['tagline' => false])
{{-- Logo texte : « ABIDJAN » + carré orange + « SHOP ». À remplacer par le vrai logo quand il existera. --}}
<span {{ $attributes->merge(['class' => 'inline-flex flex-col leading-none whitespace-nowrap']) }}>
    <span class="font-display text-[1.45em] font-black tracking-tight uppercase [font-stretch:72%]">Abidjan<span class="mx-[.08em] inline-block size-[.32em] translate-y-[-.08em] bg-orange align-baseline" aria-hidden="true"></span>Shop</span>
    @if ($tagline)
        <span class="mt-1.5 text-[.62em] font-semibold tracking-[.26em] uppercase opacity-70">{{ __('Chaussures homme · Abidjan') }}</span>
    @endif
</span>
