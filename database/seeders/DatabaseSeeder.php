<?php

namespace Database\Seeders;

use App\Models\ListingType;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $this->call([
        PermissionSeeder::class,
        RoleSeeder::class,
        UserSeeder::class,
        CategorySeeder::class,
        BlogCategorySeeder::class,
        CountrySeeder::class,
        StateSeeder::class,
        CountySeeder::class,
        BusinessTypeSeeder::class,
        ListingTypeSeeder::class,
        EstablishedYearsSeeder::class,
        SubcategorySeeder::class,
        BadgeSeeder::class,
        PropertyTypesSeeder::class,
    ]);
    }
}
