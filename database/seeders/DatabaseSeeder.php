<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- ROLES ----
        $adminRole = Role::create(['name' => 'admin']);
        $directorRole = Role::create(['name' => 'director']);
        $userRole = Role::create(['name' => 'user']);

        // ---- PERMISSIONS ----
        $reviewTaskPermission = Permission::create(['name' => 'review tasks']);

        // Optionally assign permission to a role
        $adminRole->givePermissionTo($reviewTaskPermission);

        // ---- USERS ----
        $usersData = [
            [
                'name' => 'Admin User',
                'email' => 'mianokey@gmail.com',
                'phone' => '+254724381846', // must include country code + 9 digits
                'role' => $adminRole,
            ],
            [
                'name' => 'Normal User',
                'email' => 'user@example.com',
                'phone' => '+254724381846',
                'role' => $userRole,
            ],
        ];

        foreach ($usersData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['phone']), // phone as default password
                ]
            );

            $user->assignRole($data['role']);

            // Save phone as extra details
            if (method_exists($user, 'extraDetails')) {
                $user->extraDetails()->updateOrCreate(
                    ['key' => 'phone'],
                    ['value' => $data['phone']]
                );
            }
        }

        $this->command->info('Roles, permissions, and users with phone numbers created successfully!');
    }
}
