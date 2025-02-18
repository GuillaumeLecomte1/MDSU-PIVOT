<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            if (!Schema::hasColumn('product_images', 'order')) {
                $table->integer('order')->default(0);
            }

            if (!Schema::hasColumn('product_images', 'thumbnails')) {
                $table->json('thumbnails')->nullable();
            }

            // Drop the foreign key if it exists
            try {
                $table->dropForeign(['product_id']);
            } catch (\Exception $e) {
                // La contrainte n'existe peut-être pas
            }

            // Add the new foreign key
            $table->foreign('product_id')
                ->references('id')
                ->on('market__products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            if (Schema::hasColumn('product_images', 'order')) {
                $table->dropColumn('order');
            }

            if (Schema::hasColumn('product_images', 'thumbnails')) {
                $table->dropColumn('thumbnails');
            }

            // Drop the foreign key if it exists
            try {
                $table->dropForeign(['product_id']);
            } catch (\Exception $e) {
                // La contrainte n'existe peut-être pas
            }

            // Add back the original foreign key
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    private function listTableForeignKeys(string $table): array
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();
        return array_map(
            fn($key) => $key->getName(),
            $conn->listTableForeignKeys($table)
        );
    }
}; 