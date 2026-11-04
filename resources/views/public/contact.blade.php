@php use App\Models\Setting; @endphp
<x-layouts.public :title="__('Contact')" :description="__('Contactez Abidjan Shop sur WhatsApp : conseils de pointure, disponibilité d\'un modèle, suivi de commande.')">
    <div class="container-page pt-10 sm:pt-16">
        <div class="grid gap-8 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="eyebrow text-gris">{{ __('Contact') }}</p>
                <h1 class="mt-4 text-5xl sm:text-7xl">{{ __('On parle chaussures ?') }}</h1>
                <p class="mt-5 max-w-lg text-lg leading-relaxed text-gris">{{ __('Une pointure à confirmer, un modèle introuvable, une commande à suivre : le plus rapide, c\'est WhatsApp.') }}</p>
                <a href="{{ route('wa.contact') }}" rel="nofollow" class="btn-wa mt-8 px-8 py-4 text-base"><x-icon name="whatsapp" class="size-6" />{{ __('Écrire sur WhatsApp') }}</a>
            </div>
            <ul class="space-y-3">
                <li class="flex gap-4 rounded-3xl border border-ligne bg-white p-5"><x-icon name="clock" class="size-6 shrink-0" /><div><p class="font-semibold">{{ __('Horaires') }}</p><p class="text-gris">{{ Setting::get('contact_hours') }}</p></div></li>
                @if ($phone = Setting::get('contact_phone'))
                    <li class="flex gap-4 rounded-3xl border border-ligne bg-white p-5"><x-icon name="phone" class="size-6 shrink-0" /><div><p class="font-semibold">{{ __('Téléphone') }}</p><a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="text-gris hover:text-encre">{{ $phone }}</a></div></li>
                @endif
                @if ($email = Setting::get('contact_email'))
                    <li class="flex gap-4 rounded-3xl border border-ligne bg-white p-5"><x-icon name="arrow" class="size-6 shrink-0" /><div><p class="font-semibold">{{ __('E-mail') }}</p><a href="mailto:{{ $email }}" class="text-gris hover:text-encre">{{ $email }}</a></div></li>
                @endif
                <li class="flex gap-4 rounded-3xl border border-ligne bg-white p-5"><x-icon name="pin" class="size-6 shrink-0" /><div><p class="font-semibold">{{ __('Livraison') }}</p><p class="text-gris">{{ Setting::get('delivery_delay') }}. {{ Setting::get('delivery_outside') }}</p></div></li>
            </ul>
        </div>
    </div>
</x-layouts.public>
