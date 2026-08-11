<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'category_id',
        'name',
        'price',
        'stock',
        'description',
        'image',
        'is_available',
    ];

    // Relasi ke Model Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke Model Merchant
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}