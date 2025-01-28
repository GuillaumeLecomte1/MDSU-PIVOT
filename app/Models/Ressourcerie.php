<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ressourcerie extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'market__ressourceries';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'city',
        'postal_code',
        'phone',
        'email',
        'website_url',
        'logo_url',
        'opening_hours',
        'latitude',
        'longitude',
        'is_active',
        'siret',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Get the products for the ressourcerie.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
