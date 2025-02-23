<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'firstname',
        'email',
        'password',
        'role',
        'company_name',
        'ape_code',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    public function ressourcerie()
    {
        return $this->hasOne(Ressourcerie::class);
    }

    /**
     * Get the user's orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the user's favorite products.
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'favorites', 'user_id', 'product_id')
            ->withTimestamps();
    }

    /**
     * Check if user has favorited a specific product.
     */
    public function hasFavorited(Product $product): bool
    {
        return $this->favorites()->where('product_id', $product->id)->exists();
    }

    // Role management methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isRessourcerie(): bool
    {
        return $this->role === 'ressourcerie';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function getRoleLabel(): string
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'ressourcerie' => 'Ressourcerie',
            default => 'Client',
        };
    }
}
