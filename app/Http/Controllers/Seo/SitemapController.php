<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\QrCode;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect();

        /*
        |--------------------------------------------------------------------------
        | Homepage
        |--------------------------------------------------------------------------
        */
        $urls->push([
            'loc' => route('home'),
            'lastmod' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Subscription
        |--------------------------------------------------------------------------
        */
        $urls->push([
            'loc' => route('public.subscription.index'),
            'lastmod' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Active Subscription Packages
        |--------------------------------------------------------------------------
        */
        Package::query()
            ->where('status', 'active')
            ->get()
            ->each(function (Package $package) use ($urls) {
                $urls->push([
                    'loc' => route('public.subscription.show', [
                        'slug' => $package->slug,
                    ]),
                    'lastmod' => $package->updated_at?->toAtomString(),
                ]);
            });

        /*
        |--------------------------------------------------------------------------
        | Public Customer Menus
        |--------------------------------------------------------------------------
        |
        | Hanya QR aktif dengan type "menu" yang dimasukkan
        | karena halaman ini merupakan halaman publik.
        |
        */
        QrCode::query()
            ->where('status', 'active')
            ->where('type', 'menu')
            ->get()
            ->each(function (QrCode $qrCode) use ($urls) {
                $urls->push([
                    'loc' => route('customer.menu', [
                        'code' => $qrCode->code,
                    ]),
                    'lastmod' => $qrCode->updated_at?->toAtomString(),
                ]);
            });

        return response()
            ->view('components.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}