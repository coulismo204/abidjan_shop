@php
    use App\Enums\OrderStatus;
    use App\Support\Money;
@endphp
<x-layouts.admin :title="__('Commandes')">
    <h1 class="text-4xl">{{ __('Commandes') }}</h1>

    <div class="mt-4 flex flex-wrap gap-2">
        <a href="{{ route('admin.orders.index') }}" @class(['rounded-full border px-4 py-2 text-sm font-semibold', 'border-encre bg-encre text-white' => ! $status, 'border-ligne bg-white' => $status])>{{ __('Toutes') }}</a>
        @foreach (OrderStatus::cases() as $case)
            <a href="{{ route('admin.orders.index', ['statut' => $case->value]) }}" @class(['rounded-full border px-4 py-2 text-sm font-semibold', 'border-encre bg-encre text-white' => $status === $case, 'border-ligne bg-white' => $status !== $case])>{{ $case->label() }} <span class="opacity-60">{{ $counts[$case->value] ?? 0 }}</span></a>
        @endforeach
    </div>
    <form class="mt-3 flex max-w-md gap-2" method="get">
        @if ($status)<input type="hidden" name="statut" value="{{ $status->value }}">@endif
        <label for="q" class="sr-only">{{ __('Rechercher') }}</label>
        <input id="q" name="q" value="{{ request('q') }}" placeholder="{{ __('Référence, nom ou téléphone') }}" class="field py-2.5">
        <button class="a-btn-primary">{{ __('Chercher') }}</button>
    </form>

    <div class="card mt-5 overflow-x-auto">
        <table class="w-full min-w-[44rem] text-left text-sm">
            <thead class="border-b border-ligne text-xs tracking-wider text-gris uppercase">
                <tr><th class="px-4 py-3">{{ __('Commande') }}</th><th class="px-4 py-3">{{ __('Client') }}</th><th class="px-4 py-3">{{ __('Livraison') }}</th><th class="px-4 py-3 text-right">{{ __('Total') }}</th><th class="px-4 py-3">{{ __('Statut') }}</th></tr>
            </thead>
            <tbody class="divide-y divide-ligne">
                @forelse ($orders as $order)
                    <tr class="hover:bg-craie">
                        <td class="px-4 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="font-mono font-semibold underline-offset-2 hover:underline">{{ $order->reference }}</a><p class="text-xs text-gris">{{ $order->created_at->translatedFormat('d M · H:i') }}</p></td>
                        <td class="px-4 py-3"><p class="font-semibold">{{ $order->customer_name }}</p><p class="text-xs text-gris">{{ $order->formattedPhone() }}</p></td>
                        <td class="px-4 py-3">{{ $order->commune }}<p class="text-xs text-gris">{{ trans_choice(':count paire|:count paires', (int) $order->items_count) }}</p></td>
                        <td class="px-4 py-3 text-right font-semibold whitespace-nowrap">{{ Money::fcfa($order->total) }}</td>
                        <td class="px-4 py-3"><span class="badge {{ $order->status->badge() }}">{{ $order->status->label() }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gris">{{ __('Aucune commande.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $orders->links() }}</div>
</x-layouts.admin>
