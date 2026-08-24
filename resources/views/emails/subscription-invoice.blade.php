<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Invoice PesanIn</title>
</head>

<body
    style="
        margin:0;
        padding:0;
        background:#f3f4f8;
        font-family:Arial, Helvetica, sans-serif;
        color:#111827;
    "
>

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    style="background:#f3f4f8; padding:40px 15px;"
>

    <tr>
        <td align="center">

            <table
                width="100%"
                cellpadding="0"
                cellspacing="0"
                style="
                    max-width:620px;
                    background:#ffffff;
                    border-radius:18px;
                    overflow:hidden;
                    border:1px solid #e5e7eb;
                "
            >

                {{-- HEADER --}}
                <tr>
                    <td
                        style="
                            background:#f59e0b;
                            padding:28px 35px;
                        "
                    >

                        <table width="100%">
                            <tr>

                                <td>

                                    <div
                                        style="
                                            display:inline-block;
                                            background:#ffffff;
                                            border-radius:12px;
                                            padding:10px 14px;
                                            font-size:20px;
                                            font-weight:800;
                                            color:#111827;
                                        "
                                    >
                                        ☕ PesanIn
                                    </div>

                                </td>

                                <td
                                    align="right"
                                    style="
                                        color:#111827;
                                        font-size:12px;
                                        font-weight:bold;
                                    "
                                >
                                    INVOICE
                                </td>

                            </tr>
                        </table>

                    </td>
                </tr>


                {{-- CONTENT --}}
                <tr>
                    <td style="padding:35px;">

                        <h1
                            style="
                                margin:0;
                                font-size:26px;
                                color:#111827;
                            "
                        >
                            Pembayaran Berhasil
                        </h1>

                        <p
                            style="
                                margin:10px 0 0;
                                color:#64748b;
                                font-size:14px;
                                line-height:1.6;
                            "
                        >
                            Halo {{ $user->name }},
                        </p>

                        <p
                            style="
                                margin:5px 0 0;
                                color:#64748b;
                                font-size:14px;
                                line-height:1.6;
                            "
                        >
                            Pembayaran langganan PesanIn Anda telah berhasil.
                            Berikut adalah detail invoice pembayaran Anda.
                        </p>


                        {{-- STATUS --}}
                        <div
                            style="
                                margin-top:25px;
                                padding:15px 18px;
                                background:#ecfdf5;
                                border:1px solid #a7f3d0;
                                border-radius:12px;
                            "
                        >

                            <strong
                                style="
                                    color:#047857;
                                    font-size:14px;
                                "
                            >
                                ✓ Pembayaran Berhasil
                            </strong>

                            <div
                                style="
                                    margin-top:4px;
                                    color:#64748b;
                                    font-size:12px;
                                "
                            >
                                Pembayaran telah dikonfirmasi oleh sistem.
                            </div>

                        </div>


                        {{-- INVOICE INFO --}}
                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            style="
                                margin-top:25px;
                                border-collapse:collapse;
                            "
                        >

                            <tr>

                                <td
                                    style="
                                        padding:12px 0;
                                        color:#64748b;
                                        font-size:13px;
                                    "
                                >
                                    Nomor Invoice
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:12px 0;
                                        color:#111827;
                                        font-size:13px;
                                        font-weight:bold;
                                    "
                                >
                                    {{ $subscription->invoice_number }}
                                </td>

                            </tr>

                            <tr>

                                <td
                                    style="
                                        padding:12px 0;
                                        color:#64748b;
                                        font-size:13px;
                                        border-top:1px solid #f1f5f9;
                                    "
                                >
                                    Tanggal Pembayaran
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:12px 0;
                                        color:#111827;
                                        font-size:13px;
                                        font-weight:bold;
                                        border-top:1px solid #f1f5f9;
                                    "
                                >
                                    {{ $subscription->paid_at?->format('d M Y, H:i') ?? '-' }}
                                </td>

                            </tr>

                        </table>


                        {{-- DETAIL PAKET --}}
                        <div
                            style="
                                margin-top:20px;
                                padding:20px;
                                background:#f8fafc;
                                border-radius:14px;
                            "
                        >

                            <div
                                style="
                                    color:#94a3b8;
                                    font-size:11px;
                                    font-weight:bold;
                                    text-transform:uppercase;
                                    letter-spacing:1px;
                                "
                            >
                                Paket Langganan
                            </div>

                            <div
                                style="
                                    margin-top:7px;
                                    font-size:17px;
                                    font-weight:bold;
                                    color:#111827;
                                "
                            >
                                {{ $subscription->packageDuration->package->name }}
                            </div>

                            <div
                                style="
                                    margin-top:4px;
                                    color:#64748b;
                                    font-size:13px;
                                "
                            >
                                {{ $subscription->packageDuration->name }}
                                ·
                                {{ $subscription->packageDuration->duration_days }}
                                hari
                            </div>

                        </div>


                        {{-- TOTAL --}}
                        <div
                            style="
                                margin-top:25px;
                                padding-top:20px;
                                border-top:1px solid #e5e7eb;
                            "
                        >

                            <table width="100%">

                                <tr>

                                    <td
                                        style="
                                            font-size:14px;
                                            font-weight:bold;
                                            color:#64748b;
                                        "
                                    >
                                        Total Pembayaran
                                    </td>

                                    <td
                                        align="right"
                                        style="
                                            font-size:24px;
                                            font-weight:800;
                                            color:#111827;
                                        "
                                    >
                                        Rp {{ number_format($subscription->price, 0, ',', '.') }}
                                    </td>

                                </tr>

                            </table>

                        </div>


                        {{-- PERIOD --}}
                        <div
                            style="
                                margin-top:20px;
                                font-size:13px;
                                color:#64748b;
                                line-height:1.7;
                            "
                        >

                            <strong style="color:#334155;">
                                Masa Berlaku
                            </strong>

                            <br>

                            {{ $subscription->start_date?->format('d M Y') }}
                            -
                            {{ $subscription->end_date?->format('d M Y') }}

                        </div>


                        <p
                            style="
                                margin-top:30px;
                                font-size:13px;
                                line-height:1.7;
                                color:#64748b;
                            "
                        >
                            Terima kasih telah menggunakan
                            <strong style="color:#111827;">
                                PesanIn
                            </strong>.
                            Invoice ini merupakan bukti pembayaran
                            langganan Anda.
                        </p>

                    </td>
                </tr>


                {{-- FOOTER --}}
                <tr>
                    <td
                        style="
                            background:#f8fafc;
                            padding:22px 35px;
                            text-align:center;
                            border-top:1px solid #e5e7eb;
                        "
                    >

                        <p
                            style="
                                margin:0;
                                font-size:11px;
                                color:#94a3b8;
                            "
                        >
                            © {{ date('Y') }} PesanIn
                        </p>

                        <p
                            style="
                                margin:5px 0 0;
                                font-size:11px;
                                color:#94a3b8;
                            "
                        >
                            Email ini dikirim secara otomatis.
                            Mohon tidak membalas email ini.
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>

</table>

</body>
</html>
