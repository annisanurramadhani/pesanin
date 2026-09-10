<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;

class MerchantLocationController extends Controller
{
    /**
     * Menampilkan peta lokasi seluruh merchant.
     */
    public function index()
    {
        $merchants = Merchant::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get([
                'id',
                'name',
                'phone',
                'address',
                'latitude',
                'longitude',
                'status',
            ]);

        return view(
            'super_admin.merchant_locations.index',
            compact('merchants')
        );
    }
}