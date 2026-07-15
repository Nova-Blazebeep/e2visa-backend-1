<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_information', function (Blueprint $table) {
            $table->string('nda_document_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('business_information', function (Blueprint $table) {
            $table->dropColumn('nda_document_path');
        });
    }
};
