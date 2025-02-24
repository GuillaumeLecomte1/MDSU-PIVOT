<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Get the redirect path based on user role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public static function redirectTo(Request $request): string
    {
        if (!$request->user()) {
            return self::HOME;
        }

        // Check roles and redirect accordingly
        if ($request->user()->isAdmin()) {
            return route('admin.dashboard');
        }

        if ($request->user()->isRessourcerie()) {
            return route('ressourcerie.dashboard');
        }

        // Default to client dashboard or home
        return route('dashboard');
    }
}
