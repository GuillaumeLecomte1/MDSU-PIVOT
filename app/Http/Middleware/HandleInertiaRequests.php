<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'firstname' => $user->firstname,
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_label' => $user->getRoleLabel(),
                ] : null,
                'permissions' => $user ? [
                    'isAdmin' => Gate::allows('admin'),
                    'isRessourcerie' => Gate::allows('ressourcerie'),
                    'isClient' => Gate::allows('client'),
                    'canAccessAdmin' => Gate::allows('access-admin'),
                    'canAccessRessourcerie' => Gate::allows('access-ressourcerie'),
                    'canManageUsers' => Gate::allows('manage-users'),
                    'canManageProducts' => Gate::allows('manage-products'),
                    'canManageCategories' => Gate::allows('manage-categories'),
                    'canManageOrders' => Gate::allows('manage-orders'),
                    'canViewDashboard' => Gate::allows('view-dashboard'),
                    'canViewOrders' => Gate::allows('view-orders'),
                ] : [],
            ],
            'csrf_token' => csrf_token(),
            'app' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
                'success' => fn () => $request->session()->get('success'),
            ],
        ];
    }
}
