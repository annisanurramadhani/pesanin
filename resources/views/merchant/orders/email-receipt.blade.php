<body
    style="
        margin:0;
        padding:0;
        background:#f4f6f8;
        font-family:Arial, Helvetica, sans-serif;
        color:#111827;
    "
>

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    style="padding:40px 15px;"
>
    <tr>
        <td align="center">

            <table
                width="100%"
                cellpadding="0"
                cellspacing="0"
                style="
                    max-width:600px;
                    background:#ffffff;
                    border-radius:16px;
                    overflow:hidden;
                    border:1px solid #e5e7eb;
                "
            >

                {{-- Header --}}
                <tr>
                    <td
                        style="
                            padding:32px;
                            text-align:center;
                            background:#ffffff;
                        "
                    >

                        <div
                            style="
                                width:64px;
                                height:64px;
                                margin:0 auto 15px;
                                background:#f59e0b;
                                border-radius:16px;
                                line-height:64px;
                                font-size:30px;
                            "
                        >
                            ☕
                        </div>

                        <h1
                            style="
                                margin:0;
                                font-size:26px;
                                color:#111827;
                            "
                        >
                            PesanIn
                        </h1>

                        <p
                            style="
                                margin:6px 0 0;
                                color:#64748b;
                                font-size:13px;
                                letter-spacing:1px;
                                font-weight:bold;
                            "
                        >
                            DIGITAL ORDERING PLATFORM
                        </p>

                    </td>
                </tr>


                {{-- Content --}}
                <tr>
                    <td
                        style="
                            padding:35px;
                        "
                    >

                        {{-- Title --}}
                        <h2
                            style="
                                margin:0 0 8px;
                                font-size:22px;
                                color:#111827;
                            "
                        >
                            Struk Pesanan
                        </h2>

                        <p
                            style="
                                margin:0 0 25px;
                                color:#64748b;
                                font-size:14px;
                                line-height:1.6;
                            "
                        >
                            Terima kasih telah melakukan pemesanan
                            melalui PesanIn. Berikut adalah detail transaksi Anda.
                        </p>


                        {{-- Merchant --}}
                        <div
                            style="
                                padding:18px;
                                margin-bottom:25px;
                                background:#fffbeb;
                                border:1px solid #fde68a;
                                border-radius:12px;
                            "
                        >

                            <p
                                style="
                                    margin:0;
                                    font-size:16px;
                                    font-weight:bold;
                                    color:#111827;
                                "
                            >
                                {{ $order->merchant->name ?? 'PESANIN' }}
                            </p>

                            @if($order->merchant->address)
                                <p
                                    style="
                                        margin:5px 0 0;
                                        font-size:12px;
                                        line-height:1.6;
                                        color:#64748b;
                                    "
                                >
                                    {{ $order->merchant->address }}
                                </p>
                            @endif

                        </div>


                        {{-- Status --}}
                        <div
                            style="
                                padding:15px;
                                margin-bottom:25px;
                                background:#ecfdf5;
                                border:1px solid #bbf7d0;
                                border-radius:10px;
                                text-align:center;
                            "
                        >

                            <p
                                style="
                                    margin:0;
                                    color:#15803d;
                                    font-size:14px;
                                    font-weight:bold;
                                "
                            >
                                ✓ Pesanan Berhasil
                            </p>

                            <p
                                style="
                                    margin:5px 0 0;
                                    color:#16a34a;
                                    font-size:12px;
                                "
                            >
                                Pesanan Anda telah berhasil diproses.
                            </p>

                        </div>


                        {{-- Informasi Pesanan --}}
                        <h3
                            style="
                                margin:0 0 15px;
                                font-size:14px;
                                color:#111827;
                            "
                        >
                            Informasi Pesanan
                        </h3>


                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            style="
                                margin-bottom:25px;
                                border-top:1px solid #e5e7eb;
                            "
                        >

                            <tr>
                                <td
                                    style="
                                        padding:11px 0;
                                        border-bottom:1px solid #f1f5f9;
                                        color:#64748b;
                                        font-size:12px;
                                    "
                                >
                                    No. Order
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:11px 0;
                                        border-bottom:1px solid #f1f5f9;
                                        color:#111827;
                                        font-size:12px;
                                        font-weight:bold;
                                    "
                                >
                                    #{{ $order->order_number }}
                                </td>
                            </tr>

                            <tr>
                                <td
                                    style="
                                        padding:11px 0;
                                        border-bottom:1px solid #f1f5f9;
                                        color:#64748b;
                                        font-size:12px;
                                    "
                                >
                                    Tanggal
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:11px 0;
                                        border-bottom:1px solid #f1f5f9;
                                        color:#374151;
                                        font-size:12px;
                                    "
                                >
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>

                            <tr>
                                <td
                                    style="
                                        padding:11px 0;
                                        border-bottom:1px solid #f1f5f9;
                                        color:#64748b;
                                        font-size:12px;
                                    "
                                >
                                    Area / Meja
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:11px 0;
                                        border-bottom:1px solid #f1f5f9;
                                        color:#374151;
                                        font-size:12px;
                                    "
                                >
                                    {{ $order->qrCode->name ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <td
                                    style="
                                        padding:11px 0;
                                        color:#64748b;
                                        font-size:12px;
                                    "
                                >
                                    Pemesan
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:11px 0;
                                        color:#374151;
                                        font-size:12px;
                                        font-weight:bold;
                                    "
                                >
                                    {{ $order->customer_name }}
                                </td>
                            </tr>

                        </table>


                        {{-- Detail Pesanan --}}
                        <h3
                            style="
                                margin:0 0 15px;
                                font-size:14px;
                                color:#111827;
                            "
                        >
                            Detail Pesanan
                        </h3>


                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            style="
                                border-top:1px solid #e5e7eb;
                            "
                        >

                            @foreach($order->items as $item)

                                <tr>

                                    <td
                                        style="
                                            padding:14px 0;
                                            border-bottom:1px solid #f1f5f9;
                                            vertical-align:top;
                                        "
                                    >

                                        <div
                                            style="
                                                color:#111827;
                                                font-size:13px;
                                                font-weight:bold;
                                            "
                                        >
                                            {{ $item->menu_name ?? $item->menu->name ?? 'Item' }}
                                        </div>

                                        <div
                                            style="
                                                margin-top:5px;
                                                color:#64748b;
                                                font-size:11px;
                                            "
                                        >
                                            {{ $item->quantity }}
                                            ×
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </div>

                                        @if($item->notes)

                                            <div
                                                style="
                                                    margin-top:5px;
                                                    color:#94a3b8;
                                                    font-size:10px;
                                                    font-style:italic;
                                                "
                                            >
                                                Catatan: {{ $item->notes }}
                                            </div>

                                        @endif

                                    </td>

                                    <td
                                        align="right"
                                        style="
                                            padding:14px 0;
                                            border-bottom:1px solid #f1f5f9;
                                            vertical-align:top;
                                            color:#111827;
                                            font-size:12px;
                                            font-weight:bold;
                                            white-space:nowrap;
                                        "
                                    >
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>

                                </tr>

                            @endforeach

                        </table>


                        {{-- Total --}}
                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            style="
                                margin-top:20px;
                                background:#111827;
                                border-radius:12px;
                            "
                        >

                            <tr>

                                <td
                                    style="
                                        padding:18px;
                                        color:#ffffff;
                                        font-size:13px;
                                        font-weight:bold;
                                    "
                                >
                                    TOTAL PEMBAYARAN
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:18px;
                                        color:#fbbf24;
                                        font-size:18px;
                                        font-weight:bold;
                                        white-space:nowrap;
                                    "
                                >
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </td>

                            </tr>

                        </table>


                        {{-- Payment Method --}}
                        <div
                            style="
                                margin-top:20px;
                                text-align:center;
                            "
                        >

                            @if(strtolower($order->payment_method) === 'qris')

                                <span
                                    style="
                                        display:inline-block;
                                        padding:8px 15px;
                                        background:#fffbeb;
                                        border:1px solid #fde68a;
                                        border-radius:20px;
                                        color:#b45309;
                                        font-size:11px;
                                        font-weight:bold;
                                    "
                                >
                                    QRIS
                                </span>

                            @else

                                <span
                                    style="
                                        display:inline-block;
                                        padding:8px 15px;
                                        background:#f1f5f9;
                                        border:1px solid #e2e8f0;
                                        border-radius:20px;
                                        color:#475569;
                                        font-size:11px;
                                        font-weight:bold;
                                    "
                                >
                                    Bayar Kasir
                                </span>

                            @endif

                        </div>


                        {{-- Closing --}}
                        <div
                            style="
                                margin-top:30px;
                                padding-top:25px;
                                border-top:1px dashed #d1d5db;
                                text-align:center;
                            "
                        >

                            <p
                                style="
                                    margin:0;
                                    color:#111827;
                                    font-size:14px;
                                    font-weight:bold;
                                "
                            >
                                Terima kasih telah memesan!
                            </p>

                            <p
                                style="
                                    margin:7px 0 0;
                                    color:#64748b;
                                    font-size:12px;
                                    line-height:1.6;
                                "
                            >
                                Simpan email ini sebagai bukti transaksi Anda.
                            </p>

                        </div>

                    </td>
                </tr>


                {{-- Footer --}}
                <tr>

                    <td
                        style="
                            padding:25px 35px;
                            background:#f8fafc;
                            border-top:1px solid #e5e7eb;
                        "
                    >

                        <p
                            style="
                                margin:0;
                                color:#64748b;
                                font-size:12px;
                                line-height:1.6;
                                text-align:center;
                            "
                        >
                            Email ini dikirim secara otomatis oleh sistem
                            PesanIn. Mohon tidak membalas email ini.
                        </p>

                        <p
                            style="
                                margin:8px 0 0;
                                color:#94a3b8;
                                font-size:11px;
                                text-align:center;
                            "
                        >
                            © {{ date('Y') }} PesanIn
                        </p>

                    </td>

                </tr>

            </table>

        </td>
    </tr>
</table>
