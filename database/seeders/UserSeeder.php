<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User (for Filament)
        User::create([
            'name' => 'Admin ScanEatz',
            'email' => 'admin@scaneatz.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '081234567890',
        ]);

        // Customer User
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '081298765432',
        ]);
        
        // Address for customer
        $customer = User::where('email', 'customer@example.com')->first();
        $customer->addresses()->create([
            'label' => 'Kantor',
            'full_address' => 'Gedung Menara Merdeka, Jl. Budi Kemuliaan No. 2',
            'district' => 'Gambir',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'postal_code' => '10110',
            'latitude' => -6.176,
            'longitude' => 106.823,
            'is_default' => true,
            'notes' => 'Lobby utama, titip satpam',
        ]);
    }
}
