<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('client')->after('email');
            $table->string('firstname')->nullable()->after('name');
            $table->string('company_name')->nullable()->after('role');
            $table->string('ape_code')->nullable()->after('company_name');
            $table->boolean('is_admin')->default(false)->after('ape_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'firstname', 'company_name', 'ape_code', 'is_admin']);
        });
    }
};
