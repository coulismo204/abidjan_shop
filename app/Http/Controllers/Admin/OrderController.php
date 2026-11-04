<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductSize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = OrderStatus::tryFrom((string) $request->query('statut'));

        $orders = Order::withSum('items as items_count', 'quantity')
            ->when($status, fn (Builder $q) => $q->where('status', $status))
            ->when($request->query('q'), function (Builder $q, string $term) {
                $digits = preg_replace('/\D+/', '', $term);
                $q->where(fn (Builder $w) => $w->where('reference', 'like', '%'.strtoupper($term).'%')
                    ->orWhereRaw('lower(customer_name) like ?', ['%'.mb_strtolower($term).'%'])
                    ->when($digits !== '', fn ($p) => $p->orWhere('phone', 'like', '%'.$digits.'%')));
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $counts = Order::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');

        return view('admin.orders.index', compact('orders', 'status', 'counts'));
    }

    public function show(Order $order): View
    {
        $order->load('items.product');

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Changement de statut. L'annulation remet les paires en stock ; une commande annulée
     * reste annulée (on en crée une nouvelle si le client revient).
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::enum(OrderStatus::class)]]);
        $status = OrderStatus::from($data['status']);

        if ($order->status === OrderStatus::Cancelled) {
            return back()->with('error', __('Cette commande est annulée : son statut ne peut plus changer.'));
        }

        DB::transaction(function () use ($order, $status) {
            if ($status === OrderStatus::Cancelled) {
                foreach ($order->items as $item) {
                    if ($item->product_size_id) {
                        ProductSize::whereKey($item->product_size_id)->increment('stock', $item->quantity);
                    }
                }
            }
            $order->update(['status' => $status]);
        });

        return back()->with('status', $status === OrderStatus::Cancelled
            ? __('Commande annulée, les paires sont remises en stock.')
            : __('Statut mis à jour : :s.', ['s' => $status->label()]));
    }
}
