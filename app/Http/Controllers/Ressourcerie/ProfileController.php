<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ressourcerie;

use App\Http\Controllers\Controller;
use App\Models\Ressourcerie;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Ressourcerie/Profile/Edit', [
            'ressourcerie' => $request->user()->ressourcerie,
        ]);
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $ressourcerie = $request->user()->ressourcerie;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|size:5',
            'phone' => 'nullable|string|max:15',
            'email' => 'required|email|unique:market__ressourceries,email,' . $ressourcerie->id,
            'website_url' => 'nullable|url|max:255',
            'opening_hours' => 'nullable|array',
            'opening_hours.*' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'siret' => 'required|string|size:14|unique:market__ressourceries,siret,' . $ressourcerie->id,
        ]);

        // Gérer le téléchargement du logo
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('ressourceries/logos', 'public');
            $validated['logo_url'] = $path;
        }

        $validated['slug'] = Str::slug($validated['name']);
        $ressourcerie->update($validated);

        return redirect()->route('ressourcerie.profile.edit')
            ->with('success', 'Profil mis à jour avec succès.');
    }
} 