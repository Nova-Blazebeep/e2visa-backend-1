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
        Schema::create('user_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('phone_number', 20)->nullable();
            $table->string('time_frame_for_immigration', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('country_name')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('city')->nullable();
            $table->string('state_name')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('county_id')->nullable();
            $table->string('county_name')->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->boolean('have_broker')->default(false);
            $table->boolean('have_attorney')->default(false);
            $table->boolean('subscribe_for_newsletter')->default(false);
            $table->string('broker_license', 255)->nullable();
            $table->string('attorney_license', 255)->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

           // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_information');
    }
};
