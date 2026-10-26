<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\MerchantBankAccount;
use Illuminate\Support\Facades\Auth;

class KycBankController extends Controller
{

    public function index(Request $request)
    {
        $baseQuery = DB::table('merchant_bank_accounts');

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('status', 'active')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        $query = DB::table('merchant_bank_accounts')
            ->join('merchants', 'merchants.id', '=', 'merchant_bank_accounts.merchant_id')
            ->select(
                'merchant_bank_accounts.*',
                'merchants.name as merchant_name'
            )
            ->orderByRaw("
                CASE merchant_bank_accounts.status
                    WHEN 'pending' THEN 1
                    WHEN 'rejected' THEN 2
                    WHEN 'active' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('merchant_bank_accounts.created_at');

        if (in_array($request->query('status'), ['pending', 'active', 'rejected'], true)) {
            $query->where('merchant_bank_accounts.status', $request->query('status'));
        }

        $accounts = $query->paginate(10)->withQueryString();

        return view('super_admin.kyc.bank.index', compact('accounts', 'stats'));
    }


    public function approve(int $id)
    {
        DB::transaction(function () use ($id) {
            $account = MerchantBankAccount::query()
                ->whereKey($id)
                ->lockForUpdate()
                ->first();

            abort_if(!$account, 404, 'Rekening merchant tidak ditemukan.');

            if ($account->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya rekening yang sedang menunggu pemeriksaan yang dapat disetujui.',
                ]);
            }

            $account->status = 'active';
            $account->is_locked = true;
            $account->save();
        });

        return redirect()
            ->route('super_admin.kyc_bank.index')
            ->with('success', 'Rekening merchant berhasil disetujui dan dikunci.');
    }

    public function reject(int $id)
    {
        DB::transaction(function () use ($id) {
            $account = MerchantBankAccount::query()
                ->whereKey($id)
                ->lockForUpdate()
                ->first();

            abort_if(!$account, 404, 'Rekening merchant tidak ditemukan.');

            if ($account->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya rekening yang sedang menunggu pemeriksaan yang dapat ditolak.',
                ]);
            }

            $account->status = 'rejected';
            $account->is_locked = false;
            $account->save();
        });

        return redirect()
            ->route('super_admin.kyc_bank.index')
            ->with('success', 'Pengajuan rekening ditolak. Merchant dapat mengajukan kembali.');
    }
}
