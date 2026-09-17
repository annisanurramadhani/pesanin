@extends('layouts.merchant')

@section('header')
<div>
    <h1 class="text-2xl font-black text-slate-800">
        Keuangan
    </h1>

    <p class="text-sm text-slate-500 mt-1">
        Kelola saldo, transaksi, dan laporan keuangan merchant
    </p>
</div>
@endsection


@section('content')

<div class="space-y-5">


<div class="grid grid-cols-1 md:grid-cols-3 gap-4">


<div class="bg-white border rounded-2xl p-5 shadow-sm">

<p class="text-xs font-black text-slate-400 uppercase">
Saldo Tersedia
</p>

<h2 
id="walletBalance"
class="mt-2 text-3xl font-black">

Rp {{ number_format($balance,0,',','.') }}

</h2>

<p class="text-sm text-slate-500">
Saldo yang dapat ditarik
</p>

</div>




<div class="bg-white border rounded-2xl p-5 shadow-sm">

<p class="text-xs font-black text-slate-400 uppercase">
Total Pendapatan
</p>

<h2 
id="totalIncome"
class="mt-2 text-3xl font-black text-emerald-600">

Rp {{ number_format($totalIncome,0,',','.') }}

</h2>

<p class="text-sm text-slate-500">
Total pembayaran masuk
</p>

</div>




<div class="bg-white border rounded-2xl p-5 shadow-sm">

<p class="text-xs font-black text-slate-400 uppercase">
Total Penarikan
</p>

<h2 
id="totalWithdraw"
class="mt-2 text-3xl font-black text-rose-600">

Rp {{ number_format($totalWithdraw,0,',','.') }}

</h2>

<p class="text-sm text-slate-500">
Saldo yang sudah ditarik
</p>

</div>


</div>





<div class="flex justify-between items-center">

<h2 class="text-xl font-black">
Laporan Transaksi
</h2>



<div class="flex gap-3">


@if($bankAccount)

<button
onclick="openWithdrawModal()"
class="px-5 h-11 rounded-xl bg-amber-500 text-white font-black">

<i class="fa-solid fa-money-bill-transfer mr-2"></i>

Tarik Saldo

</button>


@else

<span class="px-5 h-11 flex items-center rounded-xl bg-slate-200 text-slate-600 font-black">

Rekening Belum Ada

</span>

@endif




<a
href="{{ route('merchant.finance.pdf') }}"
class="px-5 h-11 flex items-center rounded-xl bg-slate-900 text-white font-black">

<i class="fa-solid fa-file-pdf mr-2"></i>

Export PDF

</a>


</div>

</div>





<div class="bg-white border rounded-2xl p-4">


<form method="GET"
class="flex gap-3">


<input
type="date"
name="date"
value="{{ request('date') }}"
class="h-10 border rounded-xl px-4">


<select
name="sort"
class="h-10 border rounded-xl px-4">

<option value="desc">
Terbaru
</option>

<option value="asc">
Terlama
</option>

</select>



<button
class="px-5 rounded-xl bg-slate-900 text-white font-bold">

Filter

</button>


<a
href="{{ route('merchant.finance.index') }}"
class="px-4 flex items-center">

Reset

</a>


</form>

</div>





<div class="bg-white border rounded-2xl p-5">


<p class="text-xs uppercase font-black text-slate-400">

Rekening Penarikan

</p>



@if($bankAccount)

<h3 class="text-lg font-black mt-3">
{{ $bankAccount->bank_name }}
</h3>


<p>
{{ $bankAccount->account_number }}
</p>


<p>
a.n {{ $bankAccount->account_name }}
</p>



<span class="inline-block mt-3 px-4 py-2 rounded-full bg-emerald-100 text-emerald-700 text-xs font-black">

<i class="fa-solid fa-lock"></i>

Terkunci

</span>


@else

<p class="mt-3 text-slate-500">
Belum ada rekening
</p>


@endif


</div>






<div class="bg-white border rounded-2xl overflow-hidden">


<table class="w-full text-sm">


<thead class="bg-slate-50">

<tr>

<th class="p-4 text-left">
Tanggal
</th>


<th class="p-4 text-left">
Keterangan
</th>


<th class="p-4 text-center">
Jenis
</th>


<th class="p-4 text-right">
Nominal
</th>

</tr>

</thead>




<tbody id="transactionBody">


@foreach($transactions as $transaction)

<tr class="border-b">


<td class="p-4">

{{ $transaction->created_at->format('d M Y H:i') }}

</td>


<td class="p-4 font-bold">

{{ $transaction->description }}

</td>


<td class="p-4 text-center">

@if($transaction->type == 'credit')

<span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-black">

Pemasukan

</span>

@else

<span class="px-3 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-black">

Penarikan

</span>

@endif

</td>



<td class="p-4 text-right font-black">

Rp {{ number_format($transaction->amount,0,',','.') }}

</td>


</tr>


@endforeach


</tbody>


</table>


</div>


</div>





@if($bankAccount)

<div id="withdrawModal"
class="hidden fixed inset-0 bg-black/40 z-50 items-center justify-center">


<div class="bg-white rounded-2xl p-6 w-full max-w-md">


<h3 class="text-xl font-black mb-5">
Tarik Saldo
</h3>



<div class="bg-slate-50 rounded-xl p-4 mb-4">

<p class="text-xs font-black text-slate-400 uppercase">
Rekening Tujuan
</p>


<div class="mt-3 space-y-1">


<p class="font-black text-lg">
{{ $bankAccount->bank_name }}
</p>


<p class="text-slate-700">
{{ $bankAccount->account_number }}
</p>


<p class="text-slate-700">
a.n {{ $bankAccount->account_name }}
</p>


</div>


</div>





<div class="bg-amber-50 rounded-xl p-4 mb-4">


<div class="flex justify-between text-sm">

<span class="text-slate-600">
Saldo tersedia
</span>


<span class="font-black">
Rp {{ number_format($balance,0,',','.') }}
</span>


</div>




<div class="flex justify-between text-sm mt-2">


<span class="text-slate-600">
Minimal penarikan
</span>


<span class="font-black text-amber-600">
Rp 10.000
</span>


</div>


</div>





<form 
method="POST"
action="{{ route('merchant.withdrawals.store') }}"
onsubmit="return confirmWithdraw()">


@csrf



<label class="text-sm font-bold">
Jumlah Penarikan
</label>


<input
id="withdrawAmount"
type="number"
name="amount"
min="10000"
max="{{ $balance }}"
required
placeholder="Masukkan nominal"
class="w-full h-12 border rounded-xl px-4 mt-2">



<p 
id="withdrawError"
class="hidden text-sm text-red-500 mt-2">
</p>




<div class="mt-4 bg-slate-100 rounded-xl p-3">


<p class="text-xs text-slate-500">
Dana akan dikirim ke rekening:
</p>


<p class="font-black">

{{ $bankAccount->bank_name }}
-
{{ $bankAccount->account_number }}

</p>


<p class="text-sm">

a.n {{ $bankAccount->account_name }}

</p>


</div>




<div class="flex gap-3 mt-5">


<button
type="button"
onclick="closeWithdrawModal()"
class="flex-1 h-11 bg-slate-200 rounded-xl font-bold">

Batal

</button>



<button
type="submit"
class="flex-1 h-11 bg-amber-500 text-white rounded-xl font-black">

Ajukan

</button>


</div>



</form>


</div>


</div>

@endif





<script>

function openWithdrawModal()
{
    let modal = document.getElementById('withdrawModal');

    modal.classList.remove('hidden');

    modal.classList.add('flex');
}



function closeWithdrawModal()
{
    let modal = document.getElementById('withdrawModal');

    modal.classList.add('hidden');

    modal.classList.remove('flex');
}



function formatRupiah(value)
{
    return new Intl.NumberFormat('id-ID')
    .format(value);
}



function confirmWithdraw()
{

    let amount =
    document.getElementById('withdrawAmount').value;


    let balance =
    {{ $balance }};


    let error =
    document.getElementById('withdrawError');


    error.classList.add('hidden');



    if(!amount)
    {
        error.innerHTML =
        "Nominal penarikan wajib diisi";

        error.classList.remove('hidden');

        return false;
    }



    if(amount < 10000)
    {
        error.innerHTML =
        "Minimal penarikan adalah Rp 10.000";

        error.classList.remove('hidden');

        return false;
    }



    if(amount > balance)
    {
        error.innerHTML =
        "Saldo tidak mencukupi";

        error.classList.remove('hidden');

        return false;
    }



    Swal.fire({

        title: 'Konfirmasi Penarikan',

        html: `
        
        <div class="text-left text-sm">

            <p>
            Nominal:
            <b>
            Rp ${formatRupiah(amount)}
            </b>
            </p>


            <p class="mt-2">
            Bank:
            <b>
            {{ $bankAccount->bank_name }}
            </b>
            </p>


            <p>
            No Rekening:
            <b>
            {{ $bankAccount->account_number }}
            </b>
            </p>


            <p>
            Penerima:
            <b>
            {{ $bankAccount->account_name }}
            </b>
            </p>


        </div>

        `,

        icon: 'warning',

        showCancelButton: true,

        confirmButtonText:
        'Ya, Ajukan',

        cancelButtonText:
        'Batal',

        confirmButtonColor:
        '#f59e0b',

        cancelButtonColor:
        '#64748b'


    }).then((result)=>{


        if(result.isConfirmed)
        {

            document
            .querySelector('#withdrawModal form')
            .submit();

        }


    });



    return false;

}







function loadFinanceData()
{


fetch("{{ route('merchant.finance.data') }}")


.then(response => response.json())


.then(data => {



document
.getElementById('walletBalance')
.innerHTML =
"Rp " + formatRupiah(data.balance);




document
.getElementById('totalIncome')
.innerHTML =
"Rp " + formatRupiah(data.income);




document
.getElementById('totalWithdraw')
.innerHTML =
"Rp " + formatRupiah(data.withdraw);




let html = "";



if(data.transactions.length === 0)
{


html = `

<tr>

<td colspan="4"
class="p-10 text-center text-slate-400">

Belum ada transaksi

</td>

</tr>

`;



}
else
{



data.transactions.forEach(item => {


html += `

<tr class="border-b">


<td class="p-4">

${item.date}

</td>



<td class="p-4 font-bold">

${item.description}

</td>




<td class="p-4 text-center">


${
item.type === 'credit'

?

`

<span 
class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-black">

Pemasukan

</span>

`

:

`

<span 
class="px-3 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-black">

Penarikan

</span>

`

}



</td>




<td class="p-4 text-right font-black">


Rp ${formatRupiah(item.amount)}


</td>



</tr>

`;



});



}



document
.getElementById('transactionBody')
.innerHTML = html;



})

.catch(error => {

console.log(error);

});


}






setInterval(
loadFinanceData,
5000
);



</script>


@endsection