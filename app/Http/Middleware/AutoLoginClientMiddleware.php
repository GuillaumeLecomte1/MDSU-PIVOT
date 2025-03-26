<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur n'est pas connecté
        if (!Auth::check()) {
            // Chercher l'utilisateur client dans la base de données
            $user = User::where('email', 'client@example.com')->first();
            
            // Si l'utilisateur n'existe pas, on le crée
            if (!$user) {
                $user = User::create([
                    'name' => 'Client Test',
                    'firstname' => 'Client',
                    'email' => 'client@example.com',
                    'password' => bcrypt('password'),
                    'role' => 'client',
                    'email_verified_at' => now(),
                ]);
            }
            
            // Connecter l'utilisateur
            Auth::login($user);
            dd(Auth::user());
        }

        return $next($request);
    }
}
