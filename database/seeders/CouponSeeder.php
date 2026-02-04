<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::create([
            'code' => 'HEMAT10',
            'name' => 'Diskon 10% Hemat',
            'description' => 'Diskon 10% untuk semua pembelian di atas Rp 50.000',
            'type' => 'percent',
            'value' => 10,
            'min_subtotal' => 50000,
            'max_discount' => 20000,
            'usage_limit' => 1000,
            'usage_per_user' => 1,
            'valid_from' => now(),
            'valid_until' => now()->addMonth(),
        ]);

        Coupon::create([
            'code' => 'ONGKIRFREE',
            'name' => 'Gratis Ongkir',
            'description' => 'Potongan ongkir flat Rp 10.000',
            'type' => 'fixed',
            'value' => 10000,
            'min_subtotal' => 75000,
            'usage_limit' => 500,
            'usage_per_user' => 2,
            'valid_from' => now(),
            'valid_until' => now()->addMonth(),
        ]);
    }
}
