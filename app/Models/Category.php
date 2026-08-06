<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'name',
    ];

    // Relasi ke Merchant (Pemilik Kategori)
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    // Relasi ke Menus dalam Kategori ini
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}