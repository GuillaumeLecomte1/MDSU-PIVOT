<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ressourcerie;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Ressourcerie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        Log::info('DashboardController initialized', [
            'controller' => self::class,
            'time' => now()->toDateTimeString()
        ]);
    }

    public function index(Request $request): Response
    {
        Log::info('Accessing ressourcerie dashboard', [
            'user_id' => $request->user()->id,
            'user_role' => $request->user()->role,
            'method' => __METHOD__
        ]);

        try {
            $user = $request->user();
            Log::info('User data', [
                'user_id' => $user->id,
                'has_ressourcerie' => $user->ressourcerie()->exists(),
                'ressourcerie_relation' => get_class($user->ressourcerie())
            ]);

            $ressourcerie = $user->ressourcerie;
            
            if (!$ressourcerie) {
                Log::error('No ressourcerie found for user', [
                    'user_id' => $user->id,
                    'user_role' => $user->role
                ]);
                return Inertia::render('Error/Index', [
                    'message' => 'Aucune ressourcerie associée à votre compte.'
                ]);
            }

            Log::info('Ressourcerie found', [
                'ressourcerie_id' => $ressourcerie->id,
                'ressourcerie_name' => $ressourcerie->name
            ]);
            
            // Statistiques de base
            $productsCount = $ressourcerie->products()->count();
            $activeProductsCount = $ressourcerie->products()->where('is_available', true)->count();
            
            Log::info('Products statistics', [
                'total_products' => $productsCount,
                'active_products' => $activeProductsCount
            ]);

            // Commandes en attente
            $pendingOrdersCount = Order::whereHas('products', function ($query) use ($ressourcerie) {
                $query->where('ressourcerie_id', $ressourcerie->id);
            })->where('status', 'pending')->count();

            // Total des ventes
            $totalSales = Order::whereHas('products', function ($query) use ($ressourcerie) {
                $query->where('ressourcerie_id', $ressourcerie->id);
            })->where('status', 'completed')->sum('total');

            Log::info('Orders statistics', [
                'pending_orders' => $pendingOrdersCount,
                'total_sales' => $totalSales
            ]);

            // Commandes récentes
            $recentOrders = Order::with(['user', 'products'])
                ->whereHas('products', function ($query) use ($ressourcerie) {
                    $query->where('ressourcerie_id', $ressourcerie->id);
                })
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'reference' => sprintf('#%06d', $order->id),
                        'client_name' => $order->user->name,
                        'total' => $order->total,
                        'status' => $order->status->value,
                        'status_label' => $order->status_label,
                        'created_at' => $order->created_at->format('d/m/Y'),
                    ];
                });

            Log::info('Recent orders retrieved', [
                'orders_count' => $recentOrders->count()
            ]);

            // Données pour le graphique des ventes
            $salesData = $this->getSalesData($ressourcerie->id);

            Log::info('Sales data generated', [
                'data_points' => count($salesData['labels'] ?? [])
            ]);

            return Inertia::render('Ressourcerie/Dashboard', [
                'stats' => [
                    'total_products' => $productsCount,
                    'active_products' => $activeProductsCount,
                    'total_sales' => $totalSales,
                    'pending_orders' => $pendingOrdersCount,
                ],
                'ressourcerie' => $ressourcerie,
                'recentOrders' => $recentOrders,
                'salesData' => $salesData,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in ressourcerie dashboard', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()->id ?? null
            ]);

            return Inertia::render('Error/Index', [
                'message' => 'Une erreur est survenue lors du chargement du tableau de bord.'
            ]);
        }
    }

    private function getSalesData(int $ressourcerieId): array
    {
        try {
            Log::info('Generating sales data', [
                'ressourcerie_id' => $ressourcerieId,
                'method' => __METHOD__
            ]);

            $startDate = Carbon::now()->subDays(30);
            $endDate = Carbon::now();

            $salesByDay = Order::whereHas('products', function ($query) use ($ressourcerieId) {
                $query->where('ressourcerie_id', $ressourcerieId);
            })
            ->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

            Log::info('Sales data generated successfully', [
                'days_with_sales' => count($salesByDay)
            ]);

            $labels = [];
            $data = [];

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $dateString = $date->format('Y-m-d');
                $labels[] = $date->format('d/m');
                $data[] = $salesByDay[$dateString] ?? 0;
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Ventes (€)',
                        'data' => $data,
                        'borderColor' => 'rgb(75, 192, 192)',
                        'tension' => 0.1,
                    ],
                ],
            ];

        } catch (\Exception $e) {
            Log::error('Error generating sales data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ressourcerie_id' => $ressourcerieId
            ]);

            return [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'Ventes (€)',
                        'data' => [],
                        'borderColor' => 'rgb(75, 192, 192)',
                        'tension' => 0.1,
                    ],
                ],
            ];
        }
    }
}
