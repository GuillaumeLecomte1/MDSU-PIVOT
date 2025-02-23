<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
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
            ->with(['products'])
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'date' => $order->created_at->format('d F Y'),
                    'status' => $order->status,
                    'total' => $order->total,
                    'payment_intent_id' => $order->payment_intent_id,
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

        $order->load('products.ressourcerie');

        return Inertia::render('Client/Orders/Show', [
            'order' => [
                'id' => $order->id,
                'date' => $order->created_at->format('d F Y'),
                'status' => $order->status,
                'total' => $order->total,
                'payment_intent_id' => $order->payment_intent_id,
                'items' => $order->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->pivot->price,
                        'quantity' => $product->pivot->quantity,
                        'image' => $product->images->first()?->url ?? '/images/placeholder.jpg',
                        'ressourcerie' => $product->ressourcerie->name,
                    ];
                }),
            ],
        ]);
    }
}
