<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Checkout\Session;
use Illuminate\Http\Request;
use App\Models\Product; // Assurez-vous d'avoir un modèle Product
use App\Models\Order; // Ajoutez cette ligne pour importer le modèle Order
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Auth; // Ajoutez cette ligne pour importer Auth


class PaymentController extends Controller
{
    public function charge(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $charge = Charge::create([
            'amount' => $request->amount * 100, // en cents
            'currency' => 'usd',
            'source' => $request->stripeToken,
            'description' => 'Achat marketplace',
        ]);

        return back()->with('success_message', 'Paiement réussi !');
    }

    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $product = Product::findOrFail($request->product_id);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' => $product->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
        ]);

        return response()->json(['id' => $session->id]);
    }

    public function success(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::retrieve($request->get('session_id'));
        $customer = $session->customer_details;
        
        // Stocker les détails de la commande en session
        $order = [
            'user_id' => Auth::id(), // Utilisez Auth::id() pour obtenir l'ID de l'utilisateur authentifié
            'amount' => $session->amount_total / 100,
            'status' => 'completed',
        ];
        $request->session()->put('last_order', $order);

        return view('payment.success', [
            'customer' => $customer,
            'order' => $order,
        ]);
    }

    public function cancel()
    {
        return view('payment.cancel');
    }

    public function showPaymentPage()
    {
        $products = Product::all(); // Ou une logique pour obtenir les produits à afficher
        return view('stripe', compact('products'));
    }

    public function storeOrder(Request $request)
    {
        $order = Order::create($request->all());
        return response()->json($order, 201);
    }
}