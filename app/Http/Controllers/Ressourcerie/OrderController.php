<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ressourcerie;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Enums\OrderStatus;
use App\Events\OrderStatusUpdated;
use App\Notifications\OrderStatusChanged;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $ressourcerie = Auth::user()->ressourcerie;
        
        $query = Order::with(['user', 'products'])
            ->whereHas('products', function ($query) use ($ressourcerie) {
                $query->where('ressourcerie_id', $ressourcerie->id);
            });

        // Filtre de recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filtre de statut
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Filtre de date
        if ($request->filled('date') && $request->input('date') !== 'all') {
            $date = $request->input('date');
            $now = Carbon::now();

            switch ($date) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [
                        $now->startOfWeek(),
                        $now->endOfWeek(),
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', $now->month)
                        ->whereYear('created_at', $now->year);
                    break;
            }
        }

        $orders = $query->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'reference' => sprintf('#%06d', $order->id),
                    'customer' => [
                        'name' => $order->user->name,
                        'email' => $order->user->email,
                    ],
                    'client_name' => $order->user->name,
                    'date' => $order->created_at->format('d/m/Y H:i'),
                    'created_at' => $order->created_at->format('d/m/Y'),
                    'status' => $order->status->value,
                    'status_label' => $order->status_label,
                    'total' => number_format((float) $order->total, 2, ',', ' '),
                ];
            });

        return Inertia::render('Ressourcerie/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status', 'date']),
        ]);
    }

    public function show(Order $order)
    {
        $ressourcerie = Auth::user()->ressourcerie;
        
        // Vérifier que la commande contient au moins un produit de cette ressourcerie
        abort_unless($order->products()
            ->where('ressourcerie_id', $ressourcerie->id)
            ->exists(), 403);

        $order->load(['user', 'products']);

        return Inertia::render('Ressourcerie/Orders/Show', [
            'order' => [
                'id' => $order->id,
                'customer' => [
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                ],
                'date' => $order->created_at->format('d/m/Y H:i'),
                'status' => $order->status->value,
                'total' => (float) $order->total,
                'products' => $order->products
                    ->where('ressourcerie_id', $ressourcerie->id)
                    ->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => (float) $product->price,
                            'quantity' => $product->pivot->quantity,
                            'image' => $product->image,
                        ];
                    }),
            ]
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        try {
            $request->validate([
                'status' => ['required', 'string', 'in:' . implode(',', array_column(OrderStatus::cases(), 'value'))],
            ]);

            $ressourcerie = Auth::user()->ressourcerie;
            
            // Vérifier que la commande contient au moins un produit de cette ressourcerie
            abort_unless($order->products()
                ->where('ressourcerie_id', $ressourcerie->id)
                ->exists(), 403, 'Vous n\'avez pas accès à cette commande.');

            // Récupérer le nouveau statut
            $newStatus = OrderStatus::from($request->status);
            $oldStatus = $order->status->value;

            // Vérifier si la transition est autorisée
            if (!$order->status->canTransitionTo($newStatus)) {
                return response()->json([
                    'errors' => [
                        'status' => 'Cette transition de statut n\'est pas autorisée.'
                    ]
                ], 422);
            }

            // Vérifier si le statut est disponible pour la ressourcerie
            if (!in_array($newStatus, OrderStatus::availableForRessourcerie())) {
                return response()->json([
                    'errors' => [
                        'status' => 'Ce statut ne peut pas être défini par la ressourcerie.'
                    ]
                ], 422);
            }

            $order->update([
                'status' => $newStatus->value,
            ]);

            // Déclencher l'événement
            event(new OrderStatusUpdated($order, $oldStatus, $newStatus->value));

            // Envoyer la notification au client
            $order->user->notify(new OrderStatusChanged($order, $oldStatus, $newStatus->value));

            return back()->with('success', 'Statut de la commande mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'status' => 'Une erreur est survenue lors de la mise à jour du statut.'
            ]);
        }
    }
} 