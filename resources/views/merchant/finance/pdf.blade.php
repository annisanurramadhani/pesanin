<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">

    <title>
        Laporan Keuangan Merchant
    </title>


    <style>

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }


        .header {
            text-align: center;
            margin-bottom: 25px;
        }


        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
        }


        .header p {
            margin-top: 5px;
            color: #64748b;
        }



        .merchant-info {
            width: 100%;
            margin-bottom: 20px;
        }


        .merchant-info td {
            padding: 5px;
        }



        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }


        .summary td {

            border: 1px solid #d1d5db;
            padding: 15px;

            width: 33%;

        }


        .label {

            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;

        }


        .value {

            margin-top: 8px;
            font-size: 18px;
            font-weight: bold;

        }



        .green {

            color: #059669;

        }


        .red {

            color: #dc2626;

        }



        .blue {

            color: #2563eb;

        }




        .transaction {

            width: 100%;
            border-collapse: collapse;

        }



        .transaction th {

            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: left;

        }



        .transaction td {

            border: 1px solid #e5e7eb;
            padding: 10px;

        }



        .text-right {

            text-align: right;

        }


        .text-center {

            text-align:center;

        }



        .footer {

            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #64748b;

        }


    </style>


</head>


<body>


<div class="header">

    <h1>
        LAPORAN KEUANGAN MERCHANT
    </h1>


    <p>
        PesanIn Merchant Dashboard
    </p>

</div>




<table class="merchant-info">

<tr>


<td>

<strong>Merchant</strong>

<br>

{{ auth()->user()->merchant->name ?? '-' }}

</td>



<td style="text-align:right">

<strong>Tanggal Cetak</strong>

<br>

{{ now()->format('d F Y H:i') }}

</td>


</tr>


</table>





<table class="summary">


<tr>


<td>

<div class="label">
Saldo Tersedia
</div>


<div class="value blue">

Rp {{ number_format(
    $wallet->balance ?? 0,
    0,
    ',',
    '.'
) }}

</div>


</td>




<td>

<div class="label">
Total Pendapatan
</div>


<div class="value green">

Rp {{ number_format(
    $transactions
        ->where('type','credit')
        ->sum('amount'),
    0,
    ',',
    '.'
) }}

</div>


</td>





<td>

<div class="label">
Total Penarikan
</div>


<div class="value red">

Rp {{ number_format(
    $transactions
        ->where('type','debit')
        ->sum('amount'),
    0,
    ',',
    '.'
) }}

</div>


</td>



</tr>


</table>





<h3>
Riwayat Transaksi
</h3>




<table class="transaction">


<thead>

<tr>

<th width="20%">
Tanggal
</th>


<th>
Keterangan
</th>


<th width="15%">
Jenis
</th>


<th width="20%">
Nominal
</th>


</tr>

</thead>



<tbody>


@forelse($transactions as $transaction)


<tr>


<td>

{{ $transaction->created_at->format('d M Y H:i') }}

</td>




<td>

{{ $transaction->description }}

</td>




<td class="text-center">


@if($transaction->type === 'credit')

<span class="green">
Pemasukan
</span>


@else

<span class="red">
Penarikan
</span>


@endif


</td>





<td class="text-right">


@if($transaction->type === 'credit')

<span class="green">

+

Rp {{ number_format(
    $transaction->amount,
    0,
    ',',
    '.'
) }}

</span>


@else

<span class="red">

-

Rp {{ number_format(
    $transaction->amount,
    0,
    ',',
    '.'
) }}

</span>


@endif


</td>



</tr>



@empty


<tr>

<td colspan="4" class="text-center">

Belum ada transaksi

</td>

</tr>


@endforelse



</tbody>


</table>





<div class="footer">

Laporan dibuat otomatis oleh sistem PesanIn

</div>



</body>

</html>
