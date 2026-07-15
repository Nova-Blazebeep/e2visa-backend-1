<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove "Home Based" and "Other"
        DB::table('business_types')
            ->whereIn('business_type', ['Home Based', 'Other'])
            ->delete();

        // Add new options
        $newTypes = ['May Qualify for Visa', 'SBA Loan Eligible', 'Lender Pre-Qualified'];
        foreach ($newTypes as $type) {
            if (!DB::table('business_types')->where('business_type', $type)->exists()) {
                DB::table('business_types')->insert([
                    'business_type' => $type,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Remove the added types
        DB::table('business_types')
            ->whereIn('business_type', ['May Qualify for Visa', 'SBA Loan Eligible', 'Lender Pre-Qualified'])
            ->delete();

        // Restore removed types
        foreach (['Home Based', 'Other'] as $type) {
            if (!DB::table('business_types')->where('business_type', $type)->exists()) {
                DB::table('business_types')->insert([
                    'business_type' => $type,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
};
