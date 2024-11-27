<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market__category_product', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('cascade');
                
            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('cascade');

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