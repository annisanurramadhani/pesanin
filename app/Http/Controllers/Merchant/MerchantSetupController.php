<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\PackageDuration;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MerchantSetupController extends Controller
{
    /**
     * Menampilkan halaman setup data toko.
     */
    public function create()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | User sudah memiliki merchant
        |--------------------------------------------------------------------------
        */

        if ($user->merchant_id) {

            /*
            | Cari subscription pending milik merchant.
            */

            $pendingSubscription = Subscription::where(
                'merchant_id',
                $user->merchant_id
            )
                ->where('status', 'pending')
                ->latest()
                ->first();


            /*
            | Jika ada subscription pending,
            | langsung lanjut ke pembayaran.
            */

            if ($pendingSubscription) {

                return redirect()->route(
                    'public.subscription.payment',
                    [
                        'encryptedSubscription' =>
                            encryptId($pendingSubscription->id),
                    ]
                );
            }


            /*
            | Kalau merchant sudah ada tetapi tidak ada
            | subscription pending, masuk dashboard.
            */

            return redirect()
                ->route('dashboard');
        }


        /*
        |--------------------------------------------------------------------------
        | User belum memiliki merchant
        |--------------------------------------------------------------------------
        */

        if (!session()->has('subscription.duration_id')) {

            return redirect()
                ->route('public.subscription.index')
                ->with(
                    'error',
                    'Silakan pilih paket langganan terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Tampilkan form data toko
        |--------------------------------------------------------------------------
        */

        return view('merchant.setup');
    }


    /**
     * Menyimpan data toko.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'min:10',
                'max:15',
            ],

            'address' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);


        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | Kalau user sudah punya merchant
        |--------------------------------------------------------------------------
        */

        if ($user->merchant_id) {

            $pendingSubscription = Subscription::where(
                'merchant_id',
                $user->merchant_id
            )
                ->where('status', 'pending')
                ->latest()
                ->first();


            if ($pendingSubscription) {

                return redirect()->route(
                    'public.subscription.payment',
                    [
                        'encryptedSubscription' =>
                            encryptId($pendingSubscription->id),
                    ]
                );
            }


            return redirect()
                ->route('dashboard')
                ->with(
                    'info',
                    'Akun kamu sudah memiliki toko.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil duration dari session
        |--------------------------------------------------------------------------
        */

        $durationId = session(
            'subscription.duration_id'
        );


        if (!$durationId) {

            return redirect()
                ->route('public.subscription.index')
                ->with(
                    'error',
                    'Durasi langganan tidak ditemukan.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil package duration
        |--------------------------------------------------------------------------
        */

        $duration = PackageDuration::where(
            'id',
            $durationId
        )
            ->where('status', 'active')
            ->first();


        if (!$duration) {

            return redirect()
                ->route('public.subscription.index')
                ->with(
                    'error',
                    'Durasi langganan tidak valid.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Buat merchant + subscription
        |--------------------------------------------------------------------------
        */

        try {

            $subscription = DB::transaction(
                function () use (
                    $request,
                    $user,
                    $duration
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Merchant
                    |--------------------------------------------------------------------------
                    */

                    $merchant = Merchant::create([

                        'name' => $request->name,

                        'slug' =>
                            Str::slug($request->name)
                            . '-'
                            . Str::lower(
                                Str::random(5)
                            ),

                        'phone' =>
                            $request->phone,

                        'address' =>
                            $request->address,

                        /*
                        | Merchant boleh active.
                        | Yang pending adalah subscription.
                        */

                        'status' => 'active',
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | Hubungkan user dengan merchant
                    |--------------------------------------------------------------------------
                    */

                    $user->update([
                        'merchant_id' =>
                            $merchant->id,
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | Harga
                    |--------------------------------------------------------------------------
                    */

                    $price =
                        $duration->discount_price
                        ?? $duration->price;


                    /*
                    |--------------------------------------------------------------------------
                    | Subscription
                    |--------------------------------------------------------------------------
                    |
                    | BELUM BAYAR
                    |
                    | status = pending
                    |
                    */

                    return Subscription::create([

                        'merchant_id' =>
                            $merchant->id,

                        'package_duration_id' =>
                            $duration->id,

                        'start_date' =>
                            null,

                        'end_date' =>
                            null,

                        'price' =>
                            $price,

                        'status' =>
                            'pending',

                    ]);
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Hapus session
            |--------------------------------------------------------------------------
            */

            session()->forget([
                'subscription.package_id',
                'subscription.duration_id',
            ]);


            /*
            |--------------------------------------------------------------------------
            | JANGAN dashboard
            |--------------------------------------------------------------------------
            |
            | Langsung ke payment.
            |
            */

            return redirect()->route(
                'public.subscription.payment',
                [
                    'encryptedSubscription' =>
                        encryptId($subscription->id),
                ]
            );

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Data toko gagal disimpan. Silakan coba lagi.'
                );
        }
    }
}
