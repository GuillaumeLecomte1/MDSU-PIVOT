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
                    'canAccessAdmin' => $user ? $user->can('access-admin') : false,
                    'canAccessRessourcerie' => $user ? $user->can('access-ressourcerie') : false,
                    'canAccessClient' => $user ? $user->can('access-client') : false,
                    'canManageUsers' => $user ? $user->can('manage-users') : false,
                    'canManageProducts' => $user ? $user->can('manage-products') : false,
                    'canManageCategories' => $user ? $user->can('manage-categories') : false,
                    'canManageOrders' => $user ? $user->can('manage-orders') : false,
                    'canViewOrders' => $user ? $user->can('view-orders') : false,
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
