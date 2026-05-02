<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogisticsProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'tracking_url',
        'is_active',
        'shipping_fee',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'shipping_fee' => 'decimal:2',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
