<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_information', function (Blueprint $table) {
            $table->boolean('real_estate_leased')->default(false)->after('growth_and_expansion_pros_and_cons');
            $table->boolean('real_estate_available')->default(false)->after('real_estate_leased');
            $table->boolean('real_estate_included')->default(false)->after('real_estate_available');
            $table->boolean('home_based_business')->default(false)->after('real_estate_included');
            $table->string('real_estate_rent', 100)->nullable()->after('home_based_business');
        });
    }

    public function down(): void
    {
        Schema::table('business_information', function (Blueprint $table) {
            $table->dropColumn([
                'real_estate_leased',
                'real_estate_available',
                'real_estate_included',
                'home_based_business',
                'real_estate_rent',
            ]);
        });
    }
};
