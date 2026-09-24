<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
    ];



    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];



    /**
     * Pastikan JSON lama tetap terbaca
     */
    public function getOldValuesAttribute($value)
    {

        if (!$value) {
            return [];
        }


        if (is_array($value)) {
            return $value;
        }


        $decoded = json_decode($value, true);


        return is_array($decoded)
            ? $decoded
            : [];

    }



    public function getNewValuesAttribute($value)
    {

        if (!$value) {
            return [];
        }


        if (is_array($value)) {
            return $value;
        }


        $decoded = json_decode($value, true);


        return is_array($decoded)
            ? $decoded
            : [];

    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
