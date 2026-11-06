@php
    use App\Enums\PaymentMethod;
    use App\Models\Setting;
    use App\Support\Communes;
    use App\Support\Money;
    use App\Support\WhatsApp;
    $value = fn (string $key) => old($key, $remembered[$key] ?? '');
@endphp
<x-layouts.public :title="__('Livraison')" noindex :floating-wa="false">
    <div class="container-page pt-8 sm:pt-12">
        @include('public.partials.steps', ['current' => 2])
        <h1 class="mt-6 text-3xl sm:text-4xl">{{ __('Où vous livrer ?') }}</h1>
        <p class="mt-3 text-gris">{{ __('Quatre informations et c\'est fini. Pas de compte à créer, rien à payer maintenant.') }}</p>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_24rem]">
            <form method="post" action="{{ route('checkout.store') }}" class="space-y-6 rounded-xl border border-ligne bg-white p-5 sm:p-8" data-once novalidate>
                @csrf
                {{-- Piège à robots : invisible pour les humains --}}
                <div class="hidden" aria-hidden="true"><label>Site web <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="customer_name" class="field-label">{{ __('Nom et prénom') }}</label>
                        <input id="customer_name" name="customer_name" value="{{ $value('customer_name') }}" required maxlength="120" autocomplete="name" class="field" @error('customer_name') aria-invalid="true" aria-describedby="customer_name-error" @enderror>
                        @error('customer_name')<p id="customer_name-error" class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="field-label">{{ __('Téléphone (WhatsApp de préférence)') }}</label>
                        <input id="phone" name="phone" type="tel" inputmode="tel" value="{{ $value('phone') }}" required placeholder="07 08 09 10 11" autocomplete="tel" class="field" aria-describedby="phone-help @error('phone') phone-error @enderror" @error('phone') aria-invalid="true" @enderror>
                        <p id="phone-help" class="field-help">{{ __('Le livreur vous appelle sur ce numéro.') }}</p>
                        @error('phone')<p id="phone-error" class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-[14rem_1fr]">
                    <div>
                        <label for="commune" class="field-label">{{ __('Commune') }}</label>
                        <select id="commune" name="commune" required class="field" @error('commune') aria-invalid="true" aria-describedby="commune-error" @enderror>
                            <option value="">{{ __('Choisir…') }}</option>
                            <optgroup label="Abidjan">
                                @foreach (Communes::ABIDJAN as $commune)
                                    <option @selected($value('commune') === $commune)>{{ $commune }}</option>
                                @endforeach
                            </optgroup>
                            <option value="{{ Communes::OUTSIDE }}" @selected($value('commune') === Communes::OUTSIDE)>{{ __('Hors Abidjan (intérieur du pays)') }}</option>
                        </select>
                        @error('commune')<p id="commune-error" class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="address" class="field-label">{{ __('Quartier et repère') }}</label>
                        <input id="address" name="address" value="{{ $value('address') }}" required maxlength="255" placeholder="{{ __('Ex. Riviera 2, près de la pharmacie Sainte-Cécile') }}" autocomplete="street-address" class="field" @error('address') aria-invalid="true" aria-describedby="address-error" @enderror>
                        @error('address')<p id="address-error" class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <fieldset>
                    <legend class="field-label">{{ __('Paiement') }}</legend>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach (PaymentMethod::cases() as $method)
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-ligne p-4 transition hover:border-encre has-checked:border-encre has-checked:ring-1 has-checked:ring-encre">
                                <input type="radio" name="payment_method" value="{{ $method->value }}" @checked(old('payment_method', PaymentMethod::Cash->value) === $method->value) class="mt-0.5 size-5 accent-encre">
                                <span>
                                    <span class="block font-semibold">{{ $method->shortLabel() }}</span>
                                    <span class="block text-sm text-gris">{{ $method === PaymentMethod::Cash ? __('Vous payez le livreur à la réception.') : __('Orange Money, MTN MoMo, Moov Money ou Wave, à la livraison.') }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('payment_method')<p class="field-error">{{ $message }}</p>@enderror
                </fieldset>

                <div>
                    <label for="note" class="field-label">{{ __('Remarque (facultatif)') }}</label>
                    <textarea id="note" name="note" rows="2" maxlength="1000" placeholder="{{ __('Heure de livraison préférée, autre pointure à essayer…') }}" class="field">{{ old('note') }}</textarea>
                </div>

                <button type="submit" class="btn-primary w-full">{{ __('Valider ma commande') }}<x-icon name="arrow" class="size-4" /></button>
                <p class="text-center text-xs text-gris">{{ __('Après validation, envoyez-nous le récapitulatif sur WhatsApp en un clic : on vous confirme la livraison.') }}</p>
            </form>

            <aside class="h-fit rounded-xl border border-ligne bg-white p-5 sm:p-6 lg:sticky lg:top-24">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl">{{ __('Votre commande') }}</h2>
                    <a href="{{ route('cart') }}" class="text-sm font-semibold underline underline-offset-2">{{ __('Modifier') }}</a>
                </div>
                <ul class="mt-2 divide-y divide-ligne">
                    @foreach ($lines as $line)
                        @include('public.partials.line', ['line' => $line])
                    @endforeach
                </ul>
                <dl class="space-y-2 border-t border-ligne pt-4 text-sm">
                    <div class="flex justify-between"><dt class="text-gris">{{ __('Sous-total') }}</dt><dd class="font-semibold">{{ Money::fcfa($subtotal) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gris">{{ __('Livraison à Abidjan') }}</dt><dd class="text-right font-semibold">{{ WhatsApp::deliveryLabel($deliveryFee) }}</dd></div>
                </dl>
                <div class="mt-4 flex items-baseline justify-between border-t border-ligne pt-4">
                    <p class="font-semibold">{{ __('Total') }}</p>
                    <p class="font-display text-3xl font-semibold">{{ Money::fcfa($subtotal + ($deliveryFee ?? 0)) }}</p>
                </div>
                <p class="mt-3 text-xs text-gris">{{ Setting::get('delivery_outside') }}</p>
            </aside>
        </div>
    </div>
</x-layouts.public>
