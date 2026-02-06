<?php

namespace Database\Seeders;

use App\Models\User;
use App\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@sirizen.com'],
            [
                'name' => 'Admin User',
                'phone' => '05551234567',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // Test customer users
        $customers = [
            [
                'name' => 'Ahmet Yılmaz',
                'email' => 'ahmet@example.com',
                'phone' => '05321112233',
            ],
            [
                'name' => 'Ayşe Demir',
                'email' => 'ayse@example.com',
                'phone' => '05322223344',
            ],
            [
                'name' => 'Mehmet Kaya',
                'email' => 'mehmet@example.com',
                'phone' => '05323334455',
            ],
            [
                'name' => 'Fatma Öz',
                'email' => 'fatma@example.com',
                'phone' => '05324445566',
            ],
            [
                'name' => 'Ali Çelik',
                'email' => 'ali@example.com',
                'phone' => '05325556677',
            ],
        ];

        foreach ($customers as $customer) {
            User::updateOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'phone' => $customer['phone'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::CUSTOMER,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
