<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $cart = session()->get('cart', []);
        
        // Debug logs
        Log::info('HandleInertiaRequests - share method', [
            'user' => $user,
            'is_authenticated' => $request->user() !== null,
            'session_id' => $request->session()->getId(),
            'session_data' => $request->session()->all(),
        ]);

        $sharedData = array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ] : null,
                'permissions' => [
                    'canAccessAdmin' => $user ? Gate::allows('access-admin') : false,
                    'canAccessRessourcerie' => $user ? Gate::allows('access-ressourcerie') : false,
                ],
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'csrf_token' => csrf_token(),
            'cartCount' => array_sum($cart),
        ]);

        // Debug logs for shared data
        Log::info('HandleInertiaRequests - shared data', [
            'auth' => $sharedData['auth'],
            'csrf_token' => $sharedData['csrf_token'],
        ]);

        return $sharedData;
    }
}
