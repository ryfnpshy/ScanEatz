<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;

class CouponService
{
    /**
     * Validate coupon code.
     */
    public function validate(string $code, User $user, int $subtotal): ?Coupon
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            throw new \Exception('Kode kupon tidak ditemukan.');
        }

        if (!$coupon->isValid($user)) {
             throw new \Exception('Kupon tidak valid atau sudah melampaui batas penggunaan.');
        }

        if ($subtotal < $coupon->min_subtotal) {
            $gap = $coupon->min_subtotal - $subtotal;
            throw new \Exception("Belanja Rp " . number_format($gap,0,',','.') . " lagi untuk menggunakan kupon ini.");
        }

        return $coupon;
    }
}
