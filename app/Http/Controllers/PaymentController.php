<?php

namespace App\Http\Controllers;

use App\Constants\ViewNames;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Charge; // Assurez-vous d'avoir un modèle Product
use Stripe\Checkout\Session; // Ajoutez cette ligne pour importer le modèle Order
use Stripe\Stripe; // Ajoutez cette ligne pour importer Auth
use Illuminate\Contracts\View\View;

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
            'success_url' => route('payment.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
        ]);

        return response()->json(['id' => $session->id]);
    }

    public function success(Request $request): View
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

        return view(ViewNames::PAYMENT_SUCCESS, [
            'customer' => $customer,
            'order' => $order,
        ]);
    }

    public function cancel(): View
    {
        return view(ViewNames::PAYMENT_CANCEL);
    }

    public function showPaymentPage(): View
    {
        $products = Product::all(); // Ou une logique pour obtenir les produits à afficher

        return view(ViewNames::STRIPE, compact('products'));
    }

    public function storeOrder(Request $request)
    {
        $order = Order::create($request->all());

        return response()->json($order, 201);
    }

    /**
     * Show the payment form.
     */
    public function showPaymentForm(): View
    {
        return view(ViewNames::PAYMENT_FORM);
    }

    /**
     * Show the payment confirmation.
     */
    public function showConfirmation(): View
    {
        return view(ViewNames::PAYMENT_CONFIRMATION);
    }
}
