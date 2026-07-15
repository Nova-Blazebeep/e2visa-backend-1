<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Admin',
            'Buyer',
            'Seller',
            'Business Broker',
            'Attorney',
            'Real Estate Broker',
            'Real Estate Agent',
            'Commercial Real Estate Agent',
            'Attorney - Immigration/Real Estate/Business',
            'Immigration Consultant',
            'Moderator',
            'CPA/Accountant',
            'Appraiser, Business/Real Estate',
            'Affiliate Services',
            'Lender/Loan Officer',
            'Home Inspector',
            'Insurance',
            'Financial Advisor',
            'Consultant - General',
            'Title Company',
        ];

        $permissions = Permission::all();

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            if($roleName==='Admin'){
            $role->syncPermissions($permissions);
            }
        }
    }
}
