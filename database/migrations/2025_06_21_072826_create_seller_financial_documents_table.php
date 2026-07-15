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
        Schema::create('seller_financial_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_information_id');
            $table->string('document_path');
            $table->timestamps();
            $table->softDeletes();
           // $table->foreign('business_information_id')->references('id')->on('business_information')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_financial_documents');
    }
};
