<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_information', function (Blueprint $table) {
            $table->string('number_of_employees', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('business_information', function (Blueprint $table) {
            $table->integer('number_of_employees')->nullable()->change();
        });
    }
};
