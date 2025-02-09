<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market__category_product', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('market__products')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('market__categories')->onDelete('cascade');

            // Ajouter des index pour améliorer les performances des requêtes
            $table->index(['product_id', 'category_id']);
            $table->index(['category_id', 'product_id']);

            // Clé primaire composite
            $table->primary(['product_id', 'category_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market__category_product');
    }
};
