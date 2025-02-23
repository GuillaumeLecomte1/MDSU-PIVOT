<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

trait Favoritable
{
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites', 'product_id', 'user_id')
            ->withTimestamps();
    }

    public function isFavoritedBy(?User $user = null): bool
    {
        if (!$user && !Auth::check()) {
            return false;
        }

        $user = $user ?? Auth::user();

        return $this->favorites()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function toggleFavorite(?User $user = null): bool
    {
        if (!$user && !Auth::check()) {
            return false;
        }

        $user = $user ?? Auth::user();

        if ($this->isFavoritedBy($user)) {
            $this->favorites()->detach($user->id);
            return false;
        }

        $this->favorites()->attach($user->id);
        return true;
    }
} 