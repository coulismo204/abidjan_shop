@props(['tagline' => false])
{{-- Logo texte : pastille verte « AS » + nom en gras. À remplacer par le vrai logo quand il existera. --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 leading-none whitespace-nowrap']) }}>
    <span class="flex size-[1.9em] items-center justify-center rounded-lg bg-vert text-[.85em] font-black text-white" aria-hidden="true">AS</span>
    <span class="flex flex-col">
        <span class="text-[1.15em] font-black tracking-tight">Abidjan<span class="text-vert">Shop</span></span>
        @if ($tagline)
            <span class="mt-1 text-[.6em] font-semibold opacity-70">{{ __('Chaussures homme · livrées à Abidjan') }}</span>
        @endif
    </span>
</span>
