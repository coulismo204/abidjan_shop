@props(['color', 'class' => 'size-3.5'])
<span {{ $attributes->merge(['class' => $class.' inline-block shrink-0 rounded-full ring-1 '.($color->isLight() ? 'ring-encre/30' : 'ring-encre/10')]) }} style="background-color: {{ $color->hex }}" aria-hidden="true"></span>
