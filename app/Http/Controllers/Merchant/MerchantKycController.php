<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;

class MerchantKycController extends Controller
{
    public function index()
    {
        return view('merchant.kyc.index');
    }
}
