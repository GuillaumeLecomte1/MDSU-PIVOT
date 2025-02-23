<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function checkout(Request $request)
    {
        try {
            // Vérifier l'authentification
            if (!Auth::check()) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            // Récupérer les produits du panier
            $cart = session()->get('cart', []);
            if (empty($cart)) {
                return response()->json(['error' => 'Panier vide'], 400);
            }

            // Log pour le débogage
            Log::info('Début du checkout', ['user_id' => Auth::id(), 'cart' => $cart]);

            $products = Product::whereIn('id', array_keys($cart))
                ->where('is_available', true)
                ->where('stock', '>', 0)
                ->get();

            if ($products->isEmpty()) {
                return response()->json(['error' => 'Aucun produit disponible dans le panier'], 400);
            }

            // Calculer le total
            $total = 0;
            foreach ($products as $product) {
                $quantity = $cart[$product->id] ?? 0;
                if ($quantity > $product->stock) {
                    return response()->json([
                        'error' => "Stock insuffisant pour le produit {$product->name}"
                    ], 400);
                }
                $total += $product->price * $quantity;
            }

            if ($total <= 0) {
                return response()->json(['error' => 'Montant total invalide'], 400);
            }

            // Log pour le débogage
            Log::info('Total calculé', ['total' => $total]);

            // Initialiser Stripe avec la clé secrète
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'initialisation de Stripe', [
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'error' => 'Erreur de configuration Stripe'
                ], 500);
            }

            // Créer l'intention de paiement
            try {
                $paymentIntent = PaymentIntent::create([
                    'amount' => (int)($total * 100), // Stripe utilise les centimes
                    'currency' => 'eur',
                    'metadata' => [
                        'user_id' => Auth::id(),
                    ],
                ]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                Log::error('Erreur Stripe API', [
                    'error' => $e->getMessage(),
                    'type' => get_class($e)
                ]);
                return response()->json([
                    'error' => 'Erreur lors de la création du paiement Stripe'
                ], 500);
            }

            // Créer la commande en attente
            try {
                Log::info('Tentative de création de la commande', [
                    'user_id' => Auth::id(),
                    'total' => $total,
                    'payment_intent_id' => $paymentIntent->id
                ]);

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'total' => $total,
                    'status' => 'pending',
                    'payment_intent_id' => $paymentIntent->id,
                ]);

                Log::info('Commande créée avec succès', ['order_id' => $order->id]);

                // Attacher les produits à la commande
                foreach ($products as $product) {
                    $quantity = $cart[$product->id] ?? 0;
                    if ($quantity > 0) {
                        Log::info('Tentative d\'attacher le produit à la commande', [
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'price' => $product->price
                        ]);

                        $order->products()->attach($product->id, [
                            'quantity' => $quantity,
                            'price' => $product->price,
                        ]);

                        Log::info('Produit attaché avec succès');
                    }
                }
            } catch (\Exception $e) {
                Log::error('Erreur lors de la création de la commande', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Erreur lors de la création de la commande: ' . $e->getMessage()
                ], 500);
            }

            Log::info('Checkout réussi', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'total' => $total,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur inattendue lors du checkout', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Une erreur inattendue est survenue lors de la préparation du paiement.'
            ], 500);
        }
    }

    public function success(Request $request)
    {
        try {
            // Vérifier le statut du paiement avec Stripe si nécessaire
            // Mettre à jour le statut de la commande
            
            // Vider le panier après une commande réussie
            session()->forget('cart');

            return Inertia::render('Orders/Success');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la finalisation de la commande: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Une erreur est survenue lors de la finalisation de la commande.');
        }
    }

    public function cancel()
    {
        return Inertia::render('Orders/Cancel');
    }
} 