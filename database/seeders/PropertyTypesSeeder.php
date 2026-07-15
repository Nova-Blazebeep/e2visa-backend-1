<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use DB;

class PropertyTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'House'],
            ['name' => 'Apartment'],
            ['name' => 'Villa'],
            ['name' => 'Penthouse'],
            ['name' => 'Studio Apartment'],
            ['name' => 'Upper Portion'],
            ['name' => 'Lower Portion'],
            ['name' => 'Room'],
            ['name' => 'Townhouse'],
            ['name' => 'Shop'],
            ['name' => 'Office'],
            ['name' => 'Plaza'],
            ['name' => 'Warehouse'],
            ['name' => 'Factory'],
            ['name' => 'Showroom'],
            ['name' => 'Residential Plot'],
            ['name' => 'Commercial Plot'],
            ['name' => 'Agricultural Land'],
            ['name' => 'Industrial Land'],
            ['name' => 'Farm Land'],
            ['name' => 'Hospital Building'],
            ['name' => 'School Building'],
            ['name' => 'Hotel'],
            ['name' => 'Guest House'],
            ['name' => 'Banquet Hall'],
        ];

        foreach ($types as $type) {
            DB::table('property_types')->insertOrIgnore([
                'name' => $type['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}