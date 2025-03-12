<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class InertiaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Inertia::share([
            'errors' => function () {
                return session()->get('errors')
                    ? session()->get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
        ]);

        Inertia::version(function () {
            return md5_file(public_path('build/manifest.json'));
        });
    }
} 