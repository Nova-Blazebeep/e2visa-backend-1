<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('property_types')->truncate();

        $types = [
            'House [for sale]',
            'Condo/Co-op [for sale]',
            'Townhome [for sale]',
            'Multi-family [for sale]',
            'Mobile/Manufactured [for sale]',
            'Land/Lots [for sale]',
            'Farm [for sale]',
            'House [for rent/lease]',
            'Townhouse [for rent/lease]',
            'Apartment/Condo [for rent/lease]',
        ];

        foreach ($types as $name) {
            DB::table('property_types')->insert([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('property_types')->truncate();

        $original = [
            'House', 'Apartment', 'Villa', 'Penthouse', 'Studio Apartment',
            'Upper Portion', 'Lower Portion', 'Room', 'Townhouse', 'Shop',
            'Office', 'Plaza', 'Warehouse', 'Factory', 'Showroom',
            'Residential Plot', 'Commercial Plot', 'Agricultural Land',
            'Industrial Land', 'Farm Land', 'Hospital Building',
            'School Building', 'Hotel', 'Guest House', 'Banquet Hall',
        ];

        foreach ($original as $name) {
            DB::table('property_types')->insert([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
