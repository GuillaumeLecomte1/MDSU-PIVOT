<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('market__products')) {
            Schema::table('market__products', function (Blueprint $table) {
                $columns = ['images', 'main_image', 'path'];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('market__products', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('market__products')) {
            Schema::table('market__products', function (Blueprint $table) {
                if (!Schema::hasColumn('market__products', 'images')) {
                    $table->json('images')->nullable();
                }
                if (!Schema::hasColumn('market__products', 'main_image')) {
                    $table->string('main_image')->nullable();
                }
                if (!Schema::hasColumn('market__products', 'path')) {
                    $table->string('path')->nullable();
                }
            });
        }
    }
}; 