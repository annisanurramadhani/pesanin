<html>

<head>

    <style>
        @page {
            margin: 3mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #000;
        }

        .receipt {
            width: 52mm;
            margin: auto;
        }

        .center {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .right {
            text-align: right;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .logo {
            width: 35px;
            height: 35px;
            object-fit: cover;
        }

        h3 {
            margin: 5px 0 2px;
            font-size: 13px;
        }

        p {
            margin: 2px 0;
        }

        .small {
            font-size: 8px;
        }

        .item-name {
            width: 65%;
            word-break: break-word;
        }

        .item-price {
            width: 35%;
            text-align: right;
        }

        .total {
            font-size: 12px;
            font-weight: bold;
        }
    </style>

</head>


<body>

    <div class="receipt">


        {{-- =====================
        MERCHANT
        ===================== --}}

        <div class="center">


            @if ($order->merchant?->logo)
                <img class="logo" src="{{ public_path('storage/' . $order->merchant->logo) }}">
            @endif


            <h3>
                {{ $order->merchant->name ?? 'PESANIN' }}
            </h3>


            @if ($order->merchant?->address)
                <p class="small">
                    {{ $order->merchant->address }}
                </p>
            @endif


        </div>


        <div class="divider"></div>



        {{-- =====================
        ORDER INFO
        ===================== --}}

        <table>

            <tr>
                <td>
                    No. Order
                </td>

                <td class="right">
                    #{{ $order->order_number }}
                </td>
            </tr>


            <tr>
                <td>
                    Tanggal
                </td>

                <td class="right">
                    {{ $order->created_at->format('d/m/Y H:i') }}
                </td>
            </tr>


            <tr>
                <td>
                    Area / Meja
                </td>

                <td class="right">
                    {{ $order->qrCode?->name ?? '-' }}
                </td>
            </tr>


            <tr>
                <td>
                    Pemesan
                </td>

                <td class="right">
                    {{ $order->customer_name }}
                </td>
            </tr>



            @if ($order->cashier)
                <tr>
                    <td>
                        Kasir
                    </td>

                    <td class="right">
                        {{ $order->cashier->name }}
                    </td>
                </tr>
            @endif


        </table>



        <div class="divider"></div>



        {{-- =====================
        ITEM
        ===================== --}}


        @foreach ($order->items as $item)
            <table>


                <tr>

                    <td class="item-name">

                        <b>
                            {{ $item->menu_name ?? ($item->menu?->name ?? 'Item') }}
                        </b>

                    </td>


                    <td class="item-price">

                        <b>
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </b>

                    </td>


                </tr>


                <tr>

                    <td class="small">

                        {{ $item->quantity }}
                        x
                        Rp {{ number_format($item->price, 0, ',', '.') }}


                        @if ($item->notes)
                            <br>
                            * {{ $item->notes }}
                        @endif


                    </td>

                    <td></td>

                </tr>


            </table>
        @endforeach



        <div class="divider"></div>



        {{-- =====================
        TOTAL
        ===================== --}}


        <table>


            <tr>

                <td>
                    Subtotal
                </td>

                <td class="right">

                    Rp {{ number_format($order->subtotal, 0, ',', '.') }}

                </td>

            </tr>



            @if ($order->discount)
                <tr>

                    <td>
                        Diskon
                    </td>

                    <td class="right">

                        Rp {{ number_format($order->discount, 0, ',', '.') }}

                    </td>

                </tr>
            @endif



            <tr class="total">

                <td>
                    TOTAL
                </td>

                <td class="right">

                    Rp {{ number_format($order->total, 0, ',', '.') }}

                </td>

            </tr>



        </table>



        <div class="divider"></div>



        {{-- =====================
        PAYMENT
        ===================== --}}

        @if (strtolower($order->payment_method) === 'cash')
            <table>

                <tr>
                    <td>
                        Metode
                    </td>

                    <td class="right">
                        CASH
                    </td>
                </tr>


                <tr>
                    <td>
                        Dibayar
                    </td>

                    <td class="right">
                        Rp {{ number_format($order->cash_received ?? 0, 0, ',', '.') }}
                    </td>
                </tr>


                <tr>
                    <td>
                        Kembali
                    </td>

                    <td class="right">
                        Rp {{ number_format($order->cash_change ?? 0, 0, ',', '.') }}
                    </td>
                </tr>


            </table>
        @elseif(strtolower($order->payment_method) === 'qris')
            <div class="center">

                <p>
                    <b>
                        QRIS
                    </b>
                </p>

            </div>
        @elseif(strtolower($order->payment_method) === 'bank')
            <div class="center">

                <p>
                    <b>
                        TRANSFER BANK
                    </b>
                </p>

            </div>
        @endif

        <div class="divider"></div>



        {{-- =====================
        FOOTER
        ===================== --}}


        <div class="center">


            <p>
                <b>
                    Terima Kasih!
                </b>
            </p>


            <p class="small">
                Pesanan diproses melalui PesanIn
            </p>



            @if ($order->merchant?->settings?->cs_phone)
                <br>


                <p class="small">

                    Customer Service

                    <br>

                    {{ $order->merchant->settings->cs_phone }}

                </p>
            @endif



        </div>



    </div>


</body>

</html>
