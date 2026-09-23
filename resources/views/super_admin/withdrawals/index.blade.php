@extends('layouts.admin')

@section('header')
<div>
    <h1 class="text-2xl font-black text-slate-800">
        Penarikan Saldo Merchant
    </h1>

    <p class="text-sm text-slate-500 mt-1">
        Kelola permintaan pencairan saldo merchant
    </p>
</div>
@endsection


@section('content')

<div class="space-y-6">

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

<div class="px-6 py-5 border-b border-slate-100">
<h2 class="text-lg font-black text-slate-800">
Daftar Withdrawal
</h2>
</div>


<div class="overflow-x-auto">

<table class="w-full text-sm">

<thead class="bg-slate-50">

<tr>

<th class="px-6 py-4 text-left font-black text-slate-500">
Merchant
</th>

<th class="px-6 py-4 text-left font-black text-slate-500">
Nominal
</th>

<th class="px-6 py-4 text-left font-black text-slate-500">
Rekening
</th>

<th class="px-6 py-4 text-center font-black text-slate-500">
Status
</th>

<th class="px-6 py-4 text-left font-black text-slate-500">
Payout
</th>

<th class="px-6 py-4 text-center font-black text-slate-500">
Aksi
</th>

</tr>

</thead>


<tbody>


@forelse($withdrawals as $withdrawal)

<tr class="border-b border-slate-100 hover:bg-slate-50">


<td class="px-6 py-5">

<p class="font-black text-slate-800">
{{ $withdrawal->merchant->name ?? '-' }}
</p>

<p class="text-xs text-slate-400">
#WD-{{ $withdrawal->id }}
</p>

</td>



<td class="px-6 py-5 font-black">

Rp {{ number_format($withdrawal->amount,0,',','.') }}

</td>




<td class="px-6 py-5">


@if($withdrawal->bankAccount)

<p class="font-bold text-slate-700">
{{ $withdrawal->bankAccount->bank_name }}
</p>


<p class="text-xs text-slate-500">
{{ $withdrawal->bankAccount->account_number }}
</p>


<p class="text-xs text-slate-500">
a.n {{ $withdrawal->bankAccount->account_name }}
</p>


@else

<span class="text-slate-400">
Rekening tidak ditemukan
</span>

@endif


</td>


<td class="px-6 py-5 text-center">


@if($withdrawal->status == 'pending')


<span class="px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-700">
Menunggu
</span>



@elseif($withdrawal->status == 'processing')


<span class="px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-700">
Diproses
</span>



@elseif($withdrawal->status == 'paid')


<span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700">
Berhasil
</span>



@elseif($withdrawal->status == 'failed')


<span class="px-3 py-1 rounded-full text-xs font-black bg-red-100 text-red-700">
Gagal
</span>



@elseif($withdrawal->status == 'rejected')


<span class="px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-700">
Ditolak
</span>


@endif


</td>

<td class="px-6 py-5 text-xs text-slate-500">
{{ $withdrawal->payout_status ?? '-' }}
@if($withdrawal->payout_id)
<p class="mt-1 break-all text-[10px] text-slate-400">{{ $withdrawal->payout_id }}</p>
@endif
@if($withdrawal->status === 'failed' && data_get($withdrawal->payout_response, 'message'))
<p class="mt-1 text-red-600">{{ data_get($withdrawal->payout_response, 'message') }}</p>
@endif
@if($withdrawal->note)
<p class="mt-1 text-rose-600">{{ $withdrawal->note }}</p>
@endif
</td>





<td class="px-6 py-5 text-center">


@if($withdrawal->status == 'pending')


<div class="flex justify-center gap-2">


<form method="POST"
action="{{ route('super_admin.withdrawals.approve',$withdrawal->id) }}">

@csrf


<button
class="px-4 py-2 rounded-xl bg-emerald-500 text-white text-xs font-black">

Setujui

</button>


</form>




<form method="POST"
action="{{ route('super_admin.withdrawals.reject',$withdrawal->id) }}">

@csrf

<input name="note" required maxlength="1000" placeholder="Alasan penolakan"
class="mb-2 w-full rounded border px-2 py-1 text-xs">


<button
class="px-4 py-2 rounded-xl bg-rose-500 text-white text-xs font-black">

Tolak

</button>


</form>


</div>



@elseif($withdrawal->status == 'paid')


<div>

<span class="text-xs font-black text-emerald-600">
Selesai
</span>


@if($withdrawal->payout_id)

<p class="text-[10px] text-slate-400 mt-1">
{{ $withdrawal->payout_id }}
</p>

@endif


</div>



@elseif($withdrawal->status == 'processing')


<span class="text-xs font-black text-blue-600">
Menunggu payout
</span>



@else


<span class="text-xs font-black text-slate-400">
Selesai
</span>


@endif


</td>


</tr>



@empty


<tr>

<td colspan="6"
class="py-10 text-center text-slate-400">

Belum ada permintaan penarikan

</td>

</tr>


@endforelse


</tbody>

</table>


</div>

</div>


@if($withdrawals->hasPages())

{{ $withdrawals->links() }}

@endif


</div>

@endsection
