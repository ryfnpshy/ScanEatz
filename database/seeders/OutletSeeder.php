<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Outlet;

class OutletSeeder extends Seeder
{
    public function run(): void
    {
        // Outlet 1: Pusat
        $pusat = Outlet::create([
            'code' => 'otl-1',
            'name' => 'Gajah Mada Food Street - Pusat',
            'address' => 'Jl. Gajah Mada No. 174',
            'district' => 'Taman Sari',
            'city' => 'Jakarta Pusat',
            'latitude' => -6.167,
            'longitude' => 106.820,
            'delivery_radius_km' => 7.0,
            'phone' => '021-6345678',
            'base_eta_minutes' => 25,
            'is_active' => true,
        ]);

        // Operating Hours: Daily 09:00 - 22:00
        for ($day = 0; $day <= 6; $day++) {
            $pusat->operatingHours()->create([
                'day_of_week' => $day,
                'open_time' => '09:00',
                'close_time' => '22:00',
            ]);
        }

        // Outlet 2: Senen
        $senen = Outlet::create([
            'code' => 'otl-2',
            'name' => 'Gajah Mada Food Street - Senen',
            'address' => 'Jl. Senen Raya No. 135',
            'district' => 'Senen',
            'city' => 'Jakarta Pusat',
            'latitude' => -6.175,
            'longitude' => 106.840,
            'delivery_radius_km' => 6.0,
            'phone' => '021-4234567',
            'base_eta_minutes' => 30,
            'is_active' => true,
        ]);

        // Operating Hours: Daily 09:00 - 21:00
        for ($day = 0; $day <= 6; $day++) {
            $senen->operatingHours()->create([
                'day_of_week' => $day,
                'open_time' => '09:00',
                'close_time' => '21:00',
            ]);
        }
    }
}
