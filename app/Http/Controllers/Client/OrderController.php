<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $orders = $user->orders()
            ->with(['products.ressourcerie'])
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'date' => $order->created_at->format('d/m/Y H:i'),
                    'status' => $order->status,
                    'total' => $order->total,
                    'can_complete' => $order->status === OrderStatus::DELIVERED->value,
                    'ressourceries' => $order->products->pluck('ressourcerie.name')->unique(),
                ];
            });

        return Inertia::render('Client/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        // Vérifier que l'utilisateur est propriétaire de la commande
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['products.ressourcerie']);

        return Inertia::render('Client/Orders/Show', [
            'order' => [
                'id' => $order->id,
                'date' => $order->created_at->format('d/m/Y H:i'),
                'status' => $order->status,
                'total' => $order->total,
                'can_complete' => $order->status === OrderStatus::DELIVERED->value,
                'products' => $order->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $product->pivot->quantity,
                        'image' => $product->image,
                        'ressourcerie' => [
                            'id' => $product->ressourcerie->id,
                            'name' => $product->ressourcerie->name,
                        ],
                    ];
                }),
            ],
        ]);
    }

    public function confirmReception(Order $order)
    {
        // Vérifier que la commande appartient au client
        abort_unless($order->user_id === Auth::id(), 403);

        // Vérifier que la commande est bien en statut DELIVERED
        abort_unless($order->status === OrderStatus::DELIVERED->value, 422, 'La commande n\'est pas encore livrée.');

        $oldStatus = $order->status;

        $order->update([
            'status' => OrderStatus::COMPLETED->value,
            'completed_at' => now(),
        ]);

        // Déclencher l'événement
        event(new \App\Events\OrderStatusUpdated($order, $oldStatus, OrderStatus::COMPLETED->value));

        // Notifier les ressourceries
        foreach ($order->products->pluck('ressourcerie')->unique() as $ressourcerie) {
            $ressourcerie->user->notify(new \App\Notifications\OrderCompleted($order));
        }

        return back()->with('success', 'Réception de la commande confirmée avec succès.');
    }
}
