<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Définition des rôles de base
        Gate::define('admin', fn(User $user) => $user->role === 'admin');
        Gate::define('ressourcerie', fn(User $user) => $user->role === 'ressourcerie');
        Gate::define('client', fn(User $user) => $user->role === 'client');

        // Permissions spécifiques
        Gate::define('access-admin', fn(User $user) => $user->role === 'admin');
        Gate::define('access-ressourcerie', fn(User $user) => $user->role === 'ressourcerie');
        
        // Permissions de gestion
        Gate::define('manage-users', fn(User $user) => $user->role === 'admin');
        Gate::define('manage-products', fn(User $user) => in_array($user->role, ['admin', 'ressourcerie']));
        Gate::define('manage-categories', fn(User $user) => $user->role === 'admin');
        Gate::define('manage-orders', fn(User $user) => in_array($user->role, ['admin', 'ressourcerie']));
        
        // Permissions de visualisation
        Gate::define('view-dashboard', fn(User $user) => in_array($user->role, ['admin', 'ressourcerie']));
        Gate::define('view-orders', fn(User $user) => in_array($user->role, ['admin', 'ressourcerie']));
    }
}
