@props(['silhouette' => 'basket', 'hex' => '#141414'])
{{--
    Dessin vectoriel d'une chaussure, teinté de la couleur du produit.
    Sert de visuel tant que la boutique n'a pas ajouté de photo : le catalogue reste beau dès le premier jour.
--}}
@php
    $hex = preg_match('/^#[0-9a-f]{6}$/i', (string) $hex) ? $hex : '#141414';
    [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
    $light = (0.299 * $r + 0.587 * $g + 0.114 * $b) > 160;
    $shade = $light ? 'rgb(0 0 0 / .14)' : 'rgb(0 0 0 / .32)';
    $detail = $light ? 'rgb(0 0 0 / .28)' : 'rgb(255 255 255 / .38)';
    $outline = 'rgb(18 18 18 / .28)';
@endphp
{{-- Largeur pleine par défaut ; une classe w-… passée au composant la remplace. --}}
<svg {{ $attributes->class(['h-auto', 'w-full' => ! str_contains((string) $attributes->get('class'), 'w-')]) }} viewBox="0 0 240 140" fill="none" aria-hidden="true">
    <ellipse cx="120" cy="124" rx="104" ry="7" fill="rgb(18 18 18 / .1)"/>
    @switch($silhouette)
        @case('ville')
            <path d="M16 106h204c5 0 5 9-1 10H58v2H20c-5 0-6-6-4-12z" fill="#2a221c"/>
            <path d="M20 106c-2-16 4-27 18-31l36-5c12-12 28-18 46-18l16 2c14 4 28 10 46 18 20 8 34 18 38 30v4z" fill="{{ $hex }}" stroke="{{ $outline }}" stroke-width="1.5"/>
            <path d="M74 70c12-8 26-12 42-12-6 8-20 14-36 16z" fill="{{ $shade }}"/>
            <path d="M116 60c10 4 18 8 26 14" stroke="{{ $detail }}" stroke-width="2" stroke-linecap="round" stroke-dasharray="1 5"/>
            <path d="M120 62l8 8M128 59l8 8M136 60l7 7" stroke="{{ $detail }}" stroke-width="2.4" stroke-linecap="round"/>
            <path d="M182 74c16 6 30 16 34 28" stroke="{{ $detail }}" stroke-width="1.6" stroke-linecap="round" stroke-dasharray="2 4"/>
            @break
        @case('mocassin')
            <path d="M18 106h202c5 0 4 9-2 10H24c-6 0-8-6-6-10z" fill="#2a221c"/>
            <path d="M22 106c-2-14 4-24 16-28l40-6c18-8 36-12 56-10 22 3 42 10 60 20 16 8 26 16 28 24z" fill="{{ $hex }}" stroke="{{ $outline }}" stroke-width="1.5"/>
            <path d="M78 72c10-4 22-6 36-6-8 6-20 10-34 10z" fill="{{ $shade }}"/>
            <path d="M118 68c22-4 46 2 64 14" stroke="{{ $detail }}" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 4"/>
            <path d="M112 70c4 6 6 14 4 22M122 69c4 7 5 15 3 23" stroke="{{ $detail }}" stroke-width="2" stroke-linecap="round"/>
            <circle cx="116" cy="94" r="4" fill="{{ $detail }}"/><circle cx="126" cy="94" r="4" fill="{{ $detail }}"/>
            @break
        @case('sandale')
            <path d="M14 98h212c4 0 4 12-4 12H22c-8 0-10-12-8-12z" fill="#2a221c"/>
            <path d="M18 98c40-6 120-6 206-2v6H18z" fill="{{ $shade }}"/>
            <path d="M58 98c8-30 64-34 84 0" stroke="{{ $hex }}" stroke-width="16" stroke-linecap="round"/>
            <path d="M58 98c8-30 64-34 84 0" stroke="{{ $outline }}" stroke-width="1.5" stroke-linecap="round" fill="none"/>
            <path d="M150 98c8-20 40-22 54 0" stroke="{{ $hex }}" stroke-width="12" stroke-linecap="round"/>
            <path d="M28 98c0-16 10-24 24-26" stroke="{{ $hex }}" stroke-width="9" stroke-linecap="round"/>
            <path d="M74 80l12 10M92 76l10 12M110 77l8 12" stroke="{{ $detail }}" stroke-width="2" stroke-linecap="round"/>
            @break
        @case('bottine')
            <path d="M18 106h200c5 0 5 9-1 10H62v2H22c-5 0-6-6-4-12z" fill="#2a221c"/>
            <path d="M24 106c-3-24 1-60 10-84h58c4 22 12 34 32 40 34 9 72 16 90 32 4 4 6 8 6 12z" fill="{{ $hex }}" stroke="{{ $outline }}" stroke-width="1.5"/>
            <path d="M34 22h58c1 5 2 9 3 12H32z" fill="{{ $shade }}"/>
            <path d="M68 34c5 16 10 28 14 40-6 2-14 2-20 0-2-14 0-28 6-40z" fill="{{ $shade }}"/>
            <path d="M40 12v14" stroke="{{ $hex }}" stroke-width="6" stroke-linecap="round"/>
            <path d="M126 64c30 8 64 14 84 30" stroke="{{ $detail }}" stroke-width="1.6" stroke-linecap="round" stroke-dasharray="2 4"/>
            @break
        @default
            <path d="M14 104h206c4 0 4 6 0 10-2 3-5 4-8 4H26c-8 0-12-6-12-14z" fill="{{ $light ? '#2b2b2b' : '#f6f3ee' }}" stroke="{{ $outline }}" stroke-width="1.5"/>
            <path d="M14 110h206" stroke="{{ $light ? 'rgb(255 255 255 / .2)' : 'rgb(0 0 0 / .12)' }}" stroke-width="2"/>
            <path d="M18 104c-2-18 4-32 18-38l22-8c8-18 20-28 38-30h14c2 16 16 26 36 30 30 6 60 16 70 32 4 6 4 10 2 14z" fill="{{ $hex }}" stroke="{{ $outline }}" stroke-width="1.5"/>
            <path d="M60 57c4-15 16-24 34-26h10c-4 9-12 17-26 23z" fill="{{ $shade }}"/>
            <path d="M116 44l10-6M124 50l10-6M133 55l10-6M142 59l10-6" stroke="{{ $detail }}" stroke-width="3" stroke-linecap="round"/>
            <path d="M48 96c40-4 86-14 124-28" stroke="{{ $detail }}" stroke-width="4" stroke-linecap="round"/>
            <path d="M176 70c18 6 32 14 38 26" stroke="{{ $detail }}" stroke-width="1.6" stroke-linecap="round" stroke-dasharray="2 4"/>
            <path d="M22 70c4-6 10-10 16-12" stroke="{{ $detail }}" stroke-width="3" stroke-linecap="round"/>
    @endswitch
</svg>
