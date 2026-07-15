<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_information', function (Blueprint $table) {

            $table->enum('business_type', ['business', 'real-estate'])->nullable();
            $table->unsignedBigInteger('property_type_id')->nullable()->index();
            $table->string('property_type')->nullable()->index();
            $table->integer('rooms')->nullable();
            $table->integer('baths')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('business_information', function (Blueprint $table) {
            $table->dropColumn(['property_type_id', 'property_type', 'rooms', 'baths']);
        });
    }
};
