<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'phone',
        'address',
        'is_active',
        'subscription_expires_at',
    ];
    protected $casts = [
    'is_active' => 'boolean',
    'subscription_expires_at' => 'date',
];

    // Relasi ke Users (Owner & Staff)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relasi ke Categories
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    // Relasi ke Menus
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    // Relasi ke QrCodes
    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }

    // Relasi ke Orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
