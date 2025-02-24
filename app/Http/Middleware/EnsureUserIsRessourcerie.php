<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsRessourcerie
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('EnsureUserIsRessourcerie middleware started', [
            'path' => $request->path(),
            'method' => $request->method(),
            'user_id' => $request->user()?->id
        ]);

        $user = $request->user();

        if (!$user) {
            Log::warning('Tentative d\'accès à une route ressourcerie sans authentification', [
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);
            return redirect()->route('login');
        }

        Log::info('User details in middleware', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_class' => get_class($user),
            'has_ressourcerie_method' => method_exists($user, 'isRessourcerie'),
            'has_ressourcerie_relation' => method_exists($user, 'ressourcerie')
        ]);

        if (!$user->isRessourcerie()) {
            Log::warning('Tentative d\'accès à une route ressourcerie par un utilisateur non autorisé', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'path' => $request->path()
            ]);
            return redirect()->route('forbidden');
        }

        // Vérifier que l'utilisateur a bien une ressourcerie associée
        try {
            $ressourcerie = $user->ressourcerie;
            
            Log::info('Ressourcerie relation check', [
                'user_id' => $user->id,
                'has_ressourcerie' => (bool)$ressourcerie,
                'ressourcerie_id' => $ressourcerie?->id ?? null
            ]);

            if (!$ressourcerie) {
                Log::error('Utilisateur avec rôle ressourcerie mais sans ressourcerie associée', [
                    'user_id' => $user->id,
                    'user_role' => $user->role
                ]);
                return redirect()->route('forbidden')->with('error', 'Aucune ressourcerie associée à votre compte.');
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification de la relation ressourcerie', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('forbidden')->with('error', 'Une erreur est survenue lors de la vérification de vos droits.');
        }

        Log::info('EnsureUserIsRessourcerie middleware passed', [
            'user_id' => $user->id,
            'ressourcerie_id' => $ressourcerie->id
        ]);

        return $next($request);
    }
} 