<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_information', function (Blueprint $table) {
            // Generic license/credential text field shared across all professional
            // roles (Attorney, Broker, CPA, Insurance, etc.) rather than the legacy
            // per-role broker_license/attorney_license columns, which only ever
            // covered two of the platform's ~18 professional roles.
            $table->string('license_info')->nullable()->after('licensed_states');
        });
    }

    public function down(): void
    {
        Schema::table('user_information', function (Blueprint $table) {
            $table->dropColumn('license_info');
        });
    }
};
