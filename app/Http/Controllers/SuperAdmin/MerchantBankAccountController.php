<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\MerchantBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MerchantBankAccountController extends Controller
{
    /**
     * Display a listing of merchant bank accounts.
     */
    public function index(Request $request)
    {
        $query = MerchantBankAccount::with('merchant')
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('bank_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhereHas('merchant', function ($merchantQuery) use ($search) {

                        $merchantQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );

                    });

            });
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $bankAccounts = $query
            ->paginate(10)
            ->withQueryString();


        return view(
            'super_admin.merchant_bank_accounts.index',
            compact('bankAccounts')
        );
    }


    /**
     * Show the form for creating a new merchant bank account.
     */
    public function create()
    {
        $merchants = Merchant::query()
            ->orderBy('name')
            ->get();


        return view(
            'super_admin.merchant_bank_accounts.create',
            compact('merchants')
        );
    }


    /**
     * Store a newly created merchant bank account.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'merchant_id' => [
                'required',
                'exists:merchants,id',
            ],

            'bank_name' => [
                'required',
                'string',
                'max:100',
            ],

            'account_number' => [
                'required',
                'string',
                'max:50',
            ],

            'account_name' => [
                'required',
                'string',
                'max:150',
            ],

            'status' => [
                'nullable',
                'in:active,inactive',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        |
        | Rekening langsung dikunci.
        |
        */

        DB::transaction(function () use ($validated) {

            MerchantBankAccount::create([

                'merchant_id' => $validated['merchant_id'],

                'bank_name' => $validated['bank_name'],

                'account_number' => $validated['account_number'],

                'account_name' => $validated['account_name'],

                'status' => $validated['status'] ?? 'active',

                'is_locked' => true,

            ]);

        });


        return redirect()
            ->route(
                'super_admin.merchant_bank_accounts.index'
            )
            ->with(
                'success',
                'Rekening merchant berhasil ditambahkan.'
            );
    }


    /**
     * Show the form for editing the specified merchant bank account.
     */
    public function edit($encryptedId)
    {
        $id = decryptId($encryptedId);


        /*
        |--------------------------------------------------------------------------
        | INVALID ID
        |--------------------------------------------------------------------------
        */

        if (!$id) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | FIND ACCOUNT
        |--------------------------------------------------------------------------
        */

        $bankAccount = MerchantBankAccount::with('merchant')
            ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | MERCHANT
        |--------------------------------------------------------------------------
        */

        $merchants = Merchant::query()
            ->orderBy('name')
            ->get();


        return view(
            'super_admin.merchant_bank_accounts.edit',
            compact(
                'bankAccount',
                'merchants'
            )
        );
    }


    /**
     * Update the specified merchant bank account.
     */
    public function update(
        Request $request,
        $encryptedId
    ) {

        $id = decryptId($encryptedId);


        /*
        |--------------------------------------------------------------------------
        | INVALID ID
        |--------------------------------------------------------------------------
        */

        if (!$id) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | FIND ACCOUNT
        |--------------------------------------------------------------------------
        */

        $bankAccount = MerchantBankAccount::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'merchant_id' => [
                'required',
                'exists:merchants,id',
            ],

            'bank_name' => [
                'required',
                'string',
                'max:100',
            ],

            'account_number' => [
                'required',
                'string',
                'max:50',
            ],

            'account_name' => [
                'required',
                'string',
                'max:150',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        |
        | is_locked tetap true.
        |
        | Lock hanya berlaku untuk merchant.
        | Super Admin tetap bisa melakukan perubahan.
        |
        */

        DB::transaction(function () use (
            $bankAccount,
            $validated
        ) {

            $bankAccount->update([

                'merchant_id' => $validated['merchant_id'],

                'bank_name' => $validated['bank_name'],

                'account_number' => $validated['account_number'],

                'account_name' => $validated['account_name'],

                'status' => $validated['status'],

                'is_locked' => true,

            ]);

        });


        return redirect()
            ->route(
                'super_admin.merchant_bank_accounts.index'
            )
            ->with(
                'success',
                'Rekening merchant berhasil diperbarui.'
            );
    }


    /**
     * Remove the specified merchant bank account.
     */
    public function destroy($encryptedId)
    {
        $id = decryptId($encryptedId);


        /*
        |--------------------------------------------------------------------------
        | INVALID ID
        |--------------------------------------------------------------------------
        */

        if (!$id) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | FIND ACCOUNT
        |--------------------------------------------------------------------------
        */

        $bankAccount = MerchantBankAccount::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($bankAccount) {

            $bankAccount->delete();

        });


        return redirect()
            ->route(
                'super_admin.merchant_bank_accounts.index'
            )
            ->with(
                'success',
                'Rekening merchant berhasil dihapus.'
            );
    }
}