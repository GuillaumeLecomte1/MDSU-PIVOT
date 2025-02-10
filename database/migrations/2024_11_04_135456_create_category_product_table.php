<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('market__category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('market__categories')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('market__products')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['category_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('market__category_product');
    }
};
