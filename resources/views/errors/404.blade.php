<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan | PesanIn</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f8f9fb;
            color: #252525;
            overflow-x: hidden;
        }

        .page {
            width: 100%;
            max-width: 760px;
            text-align: center;
        }

        .illustration {
            width: min(430px, 72vw);
            margin: 0 auto 12px;
            animation: floating 3.5s ease-in-out infinite;
        }

        .illustration img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: contain;
        }

        .title {
            font-size: clamp(24px, 3vw, 32px);
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 9px;
            color: #202020;
        }

        .description {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            font-size: clamp(13px, 1.6vw, 15px);
            line-height: 1.6;
            color: #707070;
        }

        .countdown-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto 15px;
            padding: 9px 17px;
            border-radius: 40px;
            background: #ffffff;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
            color: #666666;
            font-size: 13px;
        }

        .countdown {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 27px;
            height: 27px;
            margin: 0 5px;
            border-radius: 50%;
            background: #222222;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            background: #222222;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 18px rgba(0, 0, 0, 0.15);
        }

        .footer {
            margin-top: 22px;
            font-size: 11px;
            line-height: 18px;
            color: #aaaaaa;
        }

        @keyframes floating {
            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 25px 18px;
            }

            .illustration {
                width: min(380px, 78vw);
                margin-bottom: 10px;
            }

            .title {
                font-size: 24px;
            }

            .description {
                font-size: 13px;
                max-width: 430px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 15px;
            }

            .page {
                max-width: 100%;
            }

            .illustration {
                width: min(330px, 86vw);
                margin-bottom: 8px;
            }

            .title {
                font-size: 21px;
                margin-bottom: 7px;
            }

            .description {
                font-size: 12px;
                line-height: 1.55;
                padding: 0 8px;
            }

            .countdown-box {
                margin-top: 17px;
                padding: 8px 14px;
                font-size: 12px;
            }

            .countdown {
                width: 25px;
                height: 25px;
                font-size: 11px;
            }

            .back-button {
                padding: 9px 17px;
                font-size: 12px;
            }

            .footer {
                margin-top: 18px;
                font-size: 10px;
            }
        }

        @media (max-height: 700px) and (min-width: 600px) {
            .illustration {
                width: 320px;
            }

            .title {
                font-size: 25px;
            }

            .description {
                font-size: 13px;
            }

            .countdown-box {
                margin-top: 14px;
            }

            .footer {
                margin-top: 15px;
            }
        }
    </style>
</head>

<body>

    <main class="page">

        <div class="illustration">
            <img
                src="{{ asset('images/error-404.png') }}"
                alt="Ilustrasi halaman tidak ditemukan"
            >
        </div>

        <h1 class="title">
            Ups! Halamannya lagi main petak umpet 🫣
        </h1>

        <p class="description">
            Sepertinya halaman yang kamu cari tidak ditemukan.
            Tenang, kita bantu kembali ke halaman sebelumnya.
        </p>

        <div class="countdown-box">
            Kembali dalam
            <span class="countdown" id="countdown">5</span>
            detik...
        </div>

        <button
            type="button"
            class="back-button"
            onclick="goBack()"
        >
            ← Kembali Sekarang
        </button>

        <div class="footer">
            PesanIn • Pesan Lebih Mudah • Cepat • Praktis
        </div>

    </main>

    <script>
        let seconds = 5;
        const countdown = document.getElementById('countdown');

        const timer = setInterval(() => {
            seconds--;
            countdown.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(timer);
                goBack();
            }
        }, 1000);

        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '/';
            }
        }
    </script>

</body>
</html>
