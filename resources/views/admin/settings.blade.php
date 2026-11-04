<x-layouts.admin :title="__('Réglages')">
    <h1 class="text-4xl">{{ __('Réglages') }}</h1>
    <form method="post" action="{{ route('admin.settings.update') }}" class="mt-5 max-w-2xl space-y-5">
        @csrf @method('put')
        <section class="card space-y-4 p-5">
            <h2 class="text-2xl">WhatsApp</h2>
            <div>
                <label for="whatsapp_number" class="field-label">{{ __('Numéro qui reçoit les commandes') }}</label>
                <input id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $values['whatsapp_number']) }}" required inputmode="tel" class="field" placeholder="225 07 00 00 00 00">
                <p class="field-help">{{ __('Avec l\'indicatif 225. Tous les boutons du site utilisent ce numéro.') }}</p>
            </div>
            <a href="{{ $testUrl }}" target="_blank" rel="noopener" class="a-btn-ghost"><x-icon name="whatsapp" class="size-4 text-vert" />{{ __('Tester le numéro enregistré') }}</a>
        </section>

        <section class="card space-y-4 p-5">
            <h2 class="text-2xl">{{ __('Livraison et échanges') }}</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="delivery_fee" class="field-label">{{ __('Frais de livraison à Abidjan (FCFA)') }}</label>
                    <input id="delivery_fee" name="delivery_fee" type="number" min="0" step="100" value="{{ old('delivery_fee', $values['delivery_fee']) }}" class="field">
                    <p class="field-help">{{ __('0 = livraison offerte. Hors Abidjan, les frais sont confirmés sur WhatsApp.') }}</p>
                </div>
                <div>
                    <label for="delivery_delay" class="field-label">{{ __('Délai (bandeau du site)') }}</label>
                    <input id="delivery_delay" name="delivery_delay" value="{{ old('delivery_delay', $values['delivery_delay']) }}" maxlength="120" class="field">
                </div>
            </div>
            <div>
                <label for="delivery_outside" class="field-label">{{ __('Livraison hors Abidjan') }}</label>
                <textarea id="delivery_outside" name="delivery_outside" rows="2" maxlength="300" class="field">{{ old('delivery_outside', $values['delivery_outside']) }}</textarea>
            </div>
            <div>
                <label for="exchange_policy" class="field-label">{{ __('Conditions d\'échange') }}</label>
                <textarea id="exchange_policy" name="exchange_policy" rows="2" maxlength="500" class="field">{{ old('exchange_policy', $values['exchange_policy']) }}</textarea>
            </div>
        </section>

        <section class="card space-y-4 p-5">
            <h2 class="text-2xl">{{ __('Accueil') }}</h2>
            <div>
                <label for="hero_title" class="field-label">{{ __('Grand titre') }}</label>
                <input id="hero_title" name="hero_title" value="{{ old('hero_title', $values['hero_title']) }}" maxlength="120" class="field">
            </div>
            <div>
                <label for="hero_subtitle" class="field-label">{{ __('Sous-titre') }}</label>
                <textarea id="hero_subtitle" name="hero_subtitle" rows="2" maxlength="300" class="field">{{ old('hero_subtitle', $values['hero_subtitle']) }}</textarea>
            </div>
        </section>

        <section class="card space-y-4 p-5">
            <h2 class="text-2xl">{{ __('Contact et réseaux sociaux') }}</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="contact_hours" class="field-label">{{ __('Horaires') }}</label>
                    <input id="contact_hours" name="contact_hours" value="{{ old('contact_hours', $values['contact_hours']) }}" maxlength="150" class="field">
                </div>
                <div>
                    <label for="contact_phone" class="field-label">{{ __('Téléphone (facultatif)') }}</label>
                    <input id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $values['contact_phone']) }}" class="field">
                </div>
                <div>
                    <label for="contact_email" class="field-label">{{ __('E-mail (facultatif)') }}</label>
                    <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email', $values['contact_email']) }}" class="field">
                </div>
                <div class="sm:col-span-2">
                    <label for="contact_address" class="field-label">{{ __('Ville / adresse (pied de page)') }}</label>
                    <input id="contact_address" name="contact_address" value="{{ old('contact_address', $values['contact_address']) }}" maxlength="200" class="field">
                </div>
                @foreach (['instagram_url' => 'Instagram', 'facebook_url' => 'Facebook', 'tiktok_url' => 'TikTok'] as $key => $label)
                    <div>
                        <label for="{{ $key }}" class="field-label">{{ $label }}</label>
                        <input id="{{ $key }}" name="{{ $key }}" type="url" value="{{ old($key, $values[$key]) }}" class="field" placeholder="https://">
                    </div>
                @endforeach
            </div>
        </section>
        <button class="a-btn-primary py-3">{{ __('Enregistrer les réglages') }}</button>
    </form>
</x-layouts.admin>
