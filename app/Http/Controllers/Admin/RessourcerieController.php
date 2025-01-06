<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ressourcerie;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RessourcerieController extends Controller
{
    public function index()
    {
        $ressourceries = Ressourcerie::withCount('products')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Ressourceries/Index', [
            'ressourceries' => $ressourceries
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Ressourceries/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ressourceries',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email|unique:ressourceries',
        ]);

        Ressourcerie::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.ressourceries.index')
            ->with('success', 'Ressourcerie créée avec succès.');
    }
} 