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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('banner')->nullable();
            $table->longText('content');
            $table->unsignedBigInteger('category_id')->default(1);
            $table->integer('created_by');
            $table->boolean('is_active')->default(false); 
            $table->date('active_date')->nullable(); 
            $table->timestamps();
            $table->softDeletes(); // This enables soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
