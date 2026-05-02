<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GcashSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'gcash_number',
        'gcash_qr_code',
        'gcash_account_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active GCash settings (singleton pattern)
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }
}
