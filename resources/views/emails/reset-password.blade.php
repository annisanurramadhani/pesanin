<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pengaturan Ulang Password</title>
</head>

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

                <!-- Header -->
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
                            PESANIN DASHBOARD
                        </p>

                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td
                        style="
                            padding:35px;
                        "
                    >

                        <h2
                            style="
                                margin:0 0 15px;
                                font-size:22px;
                                color:#111827;
                            "
                        >
                            Pengaturan Ulang Password
                        </h2>

                        <p
                            style="
                                margin:0 0 15px;
                                color:#4b5563;
                                font-size:15px;
                                line-height:1.7;
                            "
                        >
                            Halo, {{ $user->name }}.
                        </p>

                        <p
                            style="
                                margin:0 0 20px;
                                color:#4b5563;
                                font-size:15px;
                                line-height:1.7;
                            "
                        >
                            Kami menerima permintaan untuk mengatur ulang
                            password akun PesanIn Anda. Klik tombol di bawah
                            untuk membuat password baru.
                        </p>

                        <!-- Button -->
                        <div style="text-align:center; margin:30px 0;">

                            <a
                                href="{{ $url }}"
                                style="
                                    display:inline-block;
                                    padding:14px 25px;
                                    background:#111827;
                                    color:#ffffff;
                                    text-decoration:none;
                                    border-radius:10px;
                                    font-size:14px;
                                    font-weight:bold;
                                "
                            >
                                Atur Ulang Password
                            </a>

                        </div>

                        <p
                            style="
                                margin:0 0 15px;
                                color:#64748b;
                                font-size:14px;
                                line-height:1.6;
                            "
                        >
                            Tautan ini berlaku selama
                            <strong>60 menit</strong>.
                        </p>

                        <p
                            style="
                                margin:0;
                                color:#64748b;
                                font-size:14px;
                                line-height:1.6;
                            "
                        >
                            Jika Anda tidak merasa meminta pengaturan ulang
                            password, abaikan email ini. Tidak ada perubahan
                            yang akan dilakukan pada akun Anda.
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
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

                    </td>
                </tr>

            </table>

        </td>
    </tr>

</table>

</body>

</html>
