<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Log;

class Ressourcerie extends Model
{
    use HasFactory;

    protected $table = 'market__ressourceries';

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'postal_code',
        'phone',
        'email',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($ressourcerie) {
            Log::info('Ressourcerie model retrieved', [
                'id' => $ressourcerie->id,
                'name' => $ressourcerie->name,
                'user_id' => $ressourcerie->user_id
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(
            Order::class,
            Product::class,
            'ressourcerie_id', // Clé étrangère sur la table products
            'id', // Clé locale sur la table orders
            'id', // Clé locale sur la table ressourceries
            'order_id' // Clé étrangère sur la table pivot order_product
        );
    }
}
