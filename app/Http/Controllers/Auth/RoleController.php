<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function switchToClient()
    {
        $user = Auth::user();
        $user->role = 'client';
        $user->save();

        return redirect()->back()->with('status', 'Role switched to Client');
    }

    public function switchToRessourcerie()
    {
        $user = Auth::user();
        $user->role = 'ressourcerie';
        $user->save();

        return redirect()->back()->with('status', 'Role switched to Ressourcerie');
    }

    public function switchToAdmin()
    {
        $user = Auth::user();
        $user->role = 'admin';
        $user->save();

        return redirect()->back()->with('status', 'Role switched to Admin');
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'ressourcerie' => redirect()->route('ressourcerie.dashboard'),
            default => redirect()->route('client.dashboard'),
        };
    }
}
