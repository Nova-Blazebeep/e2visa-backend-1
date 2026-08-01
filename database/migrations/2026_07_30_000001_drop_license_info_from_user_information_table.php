<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_information', function (Blueprint $table) {
            $table->dropColumn('license_info');
        });
    }

    public function down(): void
    {
        Schema::table('user_information', function (Blueprint $table) {
            $table->string('license_info')->nullable()->after('licensed_states');
        });
    }
};
