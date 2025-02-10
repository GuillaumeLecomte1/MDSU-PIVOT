<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isRessourcerie();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isRessourcerie() && $product->ressourcerie_id === $user->ressourcerie->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isRessourcerie() && $product->ressourcerie_id === $user->ressourcerie->id;
    }
} 