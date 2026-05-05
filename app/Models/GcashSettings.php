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

    protected $appends = ['gcash_qr_code_url'];

    public function getGcashQrCodeUrlAttribute()
    {
        if (!$this->gcash_qr_code) {
            return null;
        }

        if (filter_var($this->gcash_qr_code, FILTER_VALIDATE_URL)) {
            return $this->gcash_qr_code;
        }

        // Return full URL for Railway deployment
        $baseUrl = env('APP_URL', config('app.url'));
        return rtrim($baseUrl, '/') . '/storage/' . $this->gcash_qr_code;
    }

    /**
     * Get the active GCash settings (singleton pattern)
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }
}
