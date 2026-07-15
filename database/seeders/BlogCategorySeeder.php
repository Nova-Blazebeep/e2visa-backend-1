<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'General',
            // 'Business Broker Why & How',
            // 'Buyers Articles',
            // 'Listings',
            // 'Sellers Articles',
            // 'Uncategorized',
            // 'Videos',
            // 'Visa/Immigration',
        ];

        foreach ($categories as $category) {
           BlogCategory::create(['name' => $category]);
        }
    }
}
