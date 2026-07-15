<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_information', function (Blueprint $table) {
            $table->string('asking_price', 100)->nullable()->change();
            $table->string('cash_flow', 100)->nullable()->change();
            $table->string('ebitdas', 100)->nullable()->change();
            $table->string('gross_revenue', 100)->nullable()->change();
            $table->string('inventory', 100)->nullable()->change();
            $table->string('ffe', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('business_information', function (Blueprint $table) {
            $table->decimal('asking_price', 15, 2)->nullable()->change();
            $table->decimal('cash_flow', 15, 2)->nullable()->change();
            $table->decimal('ebitdas', 15, 2)->nullable()->change();
            $table->decimal('gross_revenue', 15, 2)->nullable()->change();
            $table->decimal('inventory', 15, 2)->nullable()->change();
            $table->decimal('ffe', 15, 2)->nullable()->change();
        });
    }
};
