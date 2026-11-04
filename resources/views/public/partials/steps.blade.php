{{-- Étapes de la commande : le client sait toujours où il en est. --}}
@php($current = $current ?? 1)
<ol class="flex items-center gap-2 text-xs font-semibold sm:gap-3 sm:text-sm" aria-label="{{ __('Étapes de la commande') }}">
    @foreach ([__('Panier'), __('Livraison'), __('Confirmation')] as $i => $label)
        <li class="flex items-center gap-2 sm:gap-3" @if ($i + 1 === $current) aria-current="step" @endif>
            <span @class([
                'flex size-7 items-center justify-center rounded-full',
                'bg-encre text-white' => $i + 1 === $current,
                'bg-vert text-white' => $i + 1 < $current,
                'bg-sable text-gris' => $i + 1 > $current,
            ])>
                @if ($i + 1 < $current)<x-icon name="check" class="size-4" />@else{{ $i + 1 }}@endif
            </span>
            <span @class(['text-gris' => $i + 1 > $current])>{{ $label }}</span>
            @unless ($loop->last)<span class="h-px w-5 bg-ligne sm:w-10" aria-hidden="true"></span>@endunless
        </li>
    @endforeach
</ol>
