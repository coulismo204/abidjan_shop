@props(['product', 'photo' => null, 'loading' => 'lazy', 'sizes' => '(min-width: 1024px) 25vw, 50vw'])
@php($photo ??= $product->relationLoaded('coverPhoto') ? $product->coverPhoto : $product->photos->first())
@if ($photo)
    <img src="{{ $photo->url() }}" alt="{{ $product->fullName() }}" loading="{{ $loading }}" decoding="async" {{ $attributes->merge(['class' => 'size-full object-cover']) }}>
@else
    <div {{ $attributes->merge(['class' => 'flex size-full items-center justify-center p-[9%]']) }} role="img" aria-label="{{ $product->fullName() }}">
        <x-shoe :silhouette="$product->category?->silhouette" :hex="$product->color?->hex" class="w-full -rotate-6" />
    </div>
@endif
