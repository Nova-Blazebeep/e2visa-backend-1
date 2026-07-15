<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_information', function (Blueprint $table) {
            $table->string('sale_price', 100)->nullable()->after('asking_price');
            $table->string('monthly_rent', 100)->nullable()->after('sale_price');
        });

        // Best-effort backfill: existing real-estate listings stored a bracket-minimum
        // value in asking_price. Carry that value into the matching new column so
        // existing listings don't go blank; it's not an exact price for old rows.
        DB::table('business_information')
            ->where('business_type', 'real-estate')
            ->whereNotNull('asking_price')
            ->where(function ($q) {
                $q->whereRaw("LOWER(property_type) LIKE '%[for rent/lease]%'");
            })
            ->update(['monthly_rent' => DB::raw('asking_price')]);

        DB::table('business_information')
            ->where('business_type', 'real-estate')
            ->whereNotNull('asking_price')
            ->where(function ($q) {
                $q->whereRaw("LOWER(property_type) NOT LIKE '%[for rent/lease]%'")
                    ->orWhereNull('property_type');
            })
            ->update(['sale_price' => DB::raw('asking_price')]);
    }

    public function down(): void
    {
        Schema::table('business_information', function (Blueprint $table) {
            $table->dropColumn(['sale_price', 'monthly_rent']);
        });
    }
};
