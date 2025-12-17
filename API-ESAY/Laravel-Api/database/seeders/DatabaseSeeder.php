<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Check if roles already exist
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $userRole = Role::where('slug', 'user')->first();

        // Create roles if they don't exist
        if (!$superAdminRole) {
            $superAdminRole = Role::create([
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Has all permissions including managing admins'
            ]);
        }

        if (!$adminRole) {
            $adminRole = Role::create([
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Has permissions to manage own content'
            ]);
        }

        if (!$userRole) {
            $userRole = Role::create([
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Has limited permissions to manage own content'
            ]);
        }

        // Check if users already exist
        $superAdmin = User::where('email', 'superadmin@example.com')->first();
        $admin = User::where('email', 'admin@example.com')->first();
        $user = User::where('email', 'usertest@example.com')->first();

        // Create default Super Admin account if it doesn't exist
        if (!$superAdmin) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('1234678'),
                'role_id' => $superAdminRole->id,
                'phone' => '0123450789'
            ]);
        }

        // Create default Admin account if it doesn't exist
        if (!$admin) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('1234678'),
                'role_id' => $adminRole->id,
                'phone' => '9876543210'
            ]);
        }

        // Create default Regular User account if it doesn't exist
        if (!$user) {
            User::create([
                'name' => 'User Test',
                'email' => 'usertest@example.com',
                'password' => Hash::make('1234678'),
                'role_id' => $userRole->id,
                'phone' => '1212131213'
            ]);
        }

        // Run other seeders
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            AdminProductSeeder::class,
        ]);
    }
}
