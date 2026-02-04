<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutletBlackoutDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'blackout_date',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'blackout_date' => 'date',
        ];
    }

    /**
     * Get the outlet that owns the blackout date.
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }
}
