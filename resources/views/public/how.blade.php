@php
    use App\Models\Setting;
    use App\Support\WhatsApp;
    // Longueur du pied (talon → gros orteil) pour chaque pointure européenne homme.
    $sizes = [39 => '24,7', 40 => '25,4', 41 => '26,0', 42 => '26,7', 43 => '27,3', 44 => '28,0', 45 => '28,6', 46 => '29,3', 47 => '30,0'];
    $faq = [
        ['Les chaussures sont-elles authentiques ?', 'Oui. Chaque paire est vérifiée avant d\'être mise en vente et livrée dans sa boîte. En cas de doute, demandez-nous des photos supplémentaires sur WhatsApp.'],
        ['Puis-je essayer avant de payer ?', 'Oui : vous essayez devant le livreur. Si la pointure ne va pas, on organise l\'échange ; vous ne payez que la paire que vous gardez.'],
        ['Combien de temps pour être livré ?', Setting::get('delivery_delay').'. '.Setting::get('delivery_outside')],
        ['Je ne trouve pas mon modèle ou ma pointure.', 'Écrivez-nous sur WhatsApp avec une photo du modèle : nous pouvons souvent le trouver pour vous.'],
    ];
@endphp
<x-layouts.public :title="__('Livraison, paiement et pointures')" :description="__('Comment commander chez Abidjan Shop : livraison partout à Abidjan, paiement à la livraison ou Mobile Money, guide des pointures et échanges.')">
    <section class="bg-jaune text-encre">
        <div class="container-page py-12 sm:py-16">
            <p class="eyebrow before:bg-encre">{{ __('Aide') }}</p>
            <h1 class="mt-4 max-w-3xl text-5xl sm:text-7xl">{{ __('Commander, recevoir, payer') }}</h1>
            <p class="mt-5 max-w-xl text-lg font-medium">{{ __('Tout ce qu\'il faut savoir avant d\'acheter, en deux minutes de lecture.') }}</p>
        </div>
    </section>

    <div class="container-page grid gap-4 pt-10 sm:grid-cols-3">
        @foreach ([
            ['bag', 'Commandez', 'Choisissez votre pointure, validez avec votre nom, téléphone et commune. 24 h/24, sans compte.'],
            ['truck', 'Recevez', Setting::get('delivery_delay').' · '.__('frais : :f.', ['f' => WhatsApp::deliveryLabel(Setting::deliveryFee())])],
            ['wallet', 'Payez', 'Espèces au livreur, ou Orange Money, MTN MoMo, Moov Money, Wave. Rien à payer en ligne.'],
        ] as $i => [$icon, $t, $d])
            <div class="rounded-3xl {{ ['bg-peche', 'bg-menthe', 'bg-azur'][$i] }} p-6">
                <span class="flex size-12 items-center justify-center rounded-2xl {{ ['bg-orange', 'bg-vert-vif', 'bg-ciel'][$i] }}"><x-icon :name="$icon" class="size-6" /></span>
                <h2 class="mt-4 text-3xl">{{ __($t) }}</h2>
                <p class="mt-2 leading-relaxed">{{ __($d) }}</p>
            </div>
        @endforeach
    </div>

    <div class="container-page mt-16 grid gap-12 lg:grid-cols-2">
        <section id="pointures">
            <p class="eyebrow text-gris">{{ __('Guide') }}</p>
            <h2 class="mt-2 text-4xl sm:text-5xl">{{ __('Trouver sa pointure') }}</h2>
            <ol class="mt-5 list-decimal space-y-2 pl-5 leading-relaxed text-encre/85">
                <li>{{ __('Posez le pied à plat sur une feuille, talon contre un mur.') }}</li>
                <li>{{ __('Marquez le bout du plus long orteil et mesurez en centimètres.') }}</li>
                <li>{{ __('Lisez votre pointure dans le tableau. Entre deux tailles ? Prenez la plus grande.') }}</li>
            </ol>
            <div class="mt-6 overflow-hidden rounded-2xl border border-ligne bg-white">
                <table class="w-full text-left text-sm">
                    <caption class="sr-only">{{ __('Correspondance longueur du pied et pointure européenne') }}</caption>
                    <thead class="bg-orange text-encre"><tr><th scope="col" class="px-4 py-3">{{ __('Pointure EU') }}</th><th scope="col" class="px-4 py-3">{{ __('Longueur du pied') }}</th></tr></thead>
                    <tbody class="divide-y divide-ligne">
                        @foreach ($sizes as $eu => $cm)
                            <tr><th scope="row" class="px-4 py-2.5 font-display text-lg [font-stretch:85%]">{{ $eu }}</th><td class="px-4 py-2.5">{{ $cm }} cm</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-3 text-sm text-gris">{{ __('Les baskets Nike et Adidas taillent souvent un peu petit : en cas de doute, prenez une pointure au-dessus ou demandez-nous conseil.') }}</p>
        </section>

        <div class="space-y-12">
            <section id="echanges">
                <p class="eyebrow text-gris">{{ __('Sans stress') }}</p>
                <h2 class="mt-2 text-4xl sm:text-5xl">{{ __('Échanges') }}</h2>
                <p class="mt-4 leading-relaxed text-encre/85">{{ Setting::get('exchange_policy') }}</p>
                <a href="{{ route('wa.contact') }}" rel="nofollow" class="btn-wa mt-5"><x-icon name="whatsapp" />{{ __('Demander un échange') }}</a>
            </section>
            <section>
                <h2 class="text-4xl sm:text-5xl">{{ __('Questions fréquentes') }}</h2>
                <div class="mt-5 divide-y divide-ligne rounded-2xl border border-ligne bg-white">
                    @foreach ($faq as [$q, $a])
                        <details class="group p-5">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold">{{ __($q) }}<x-icon name="chevron" class="size-5 shrink-0 transition group-open:rotate-180" /></summary>
                            <p class="mt-3 leading-relaxed text-gris">{{ __($a) }}</p>
                        </details>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-layouts.public>
