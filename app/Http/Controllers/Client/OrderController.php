<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        // Exemple de commandes pour le front
        $orders = [
            [
                'id' => 'CMD-2024-001',
                'date' => '15 Mars 2024',
                'status' => 'completed',
                'total' => 156.90,
            ],
            [
                'id' => 'CMD-2024-002',
                'date' => '10 Mars 2024',
                'status' => 'pending',
                'total' => 89.90,
            ],
        ];

        return Inertia::render('Client/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show($orderId)
    {
        // Exemple d'une commande détaillée pour le front
        $order = [
            'id' => 'CMD-2024-001',
            'date' => '15 Mars 2024',
            'status' => 'completed',
            'total' => 156.90,
            'payment' => [
                'status' => 'paid',
                'method' => 'Stripe',
                'cardLast4' => '4242',
                'transactionId' => 'pi_3O9X4Z2eZvKYlo2C1J8F9K2M',
            ],
            'shipping' => [
                'name' => auth()->user()->name,
                'address' => '123 Rue de Paris',
                'city' => 'Paris',
                'postalCode' => '75001',
                'country' => 'France',
            ],
            'items' => [
                [
                    'id' => 1,
                    'name' => 'Table basse vintage',
                    'price' => 89.90,
                    'quantity' => 1,
                    'image' => 'https://via.placeholder.com/150',
                    'ressourcerie' => 'Ressourcerie du Centre',
                ],
                [
                    'id' => 2,
                    'name' => 'Lampe de bureau art déco',
                    'price' => 67.00,
                    'quantity' => 1,
                    'image' => 'https://via.placeholder.com/150',
                    'ressourcerie' => 'Ressourcerie du Centre',
                ],
            ],
        ];

        return Inertia::render('Client/Orders/Show', [
            'order' => $order,
        ]);
    }
}
