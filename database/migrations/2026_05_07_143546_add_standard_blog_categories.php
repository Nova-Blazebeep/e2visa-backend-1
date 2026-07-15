<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename 'Generic' to 'General' if it exists
        \Illuminate\Support\Facades\DB::table('blog_categories')
            ->where('name', 'Generic')
            ->update(['name' => 'General', 'updated_at' => now()]);

        $categories = [
            'General', // Including General here to ensure it exists
            'Become a Business Owner',
            'Business Broker Why & How',
            'Buyers/Become A Business Owner',
            'Listings',
            'Sellers',
            'Visa/Immigration',
            'Buyers/Sellers'
        ];

        foreach ($categories as $category) {
            \Illuminate\Support\Facades\DB::table('blog_categories')->updateOrInsert(
                ['name' => $category],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to delete in case of rollback unless specifically requested
    }
};
