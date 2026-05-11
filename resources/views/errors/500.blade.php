<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @include('dashboard.layouts.partials.meta')
    <title>500 — Kesalahan Server</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,700,800" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f0f2f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .error-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            text-align: center;
        }

        .error-header {
            padding: 2rem 2rem 1.5rem;
        }

        .error-code {
            font-size: 4.5rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            letter-spacing: -.02em;
            margin-top: .5rem;
        }

        .error-body {
            padding: 2rem;
        }

        .error-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #3d4466;
            margin-bottom: .5rem;
        }

        .error-desc {
            font-size: .88rem;
            color: #858796;
            line-height: 1.6;
            margin-bottom: 1.75rem;
        }

        .btn-error {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            font-size: .875rem;
            padding: .6rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: opacity .15s;
            margin: .25rem;
        }

        .btn-error:hover {
            opacity: .85;
            color: #fff;
            text-decoration: none;
        }

        .btn-outline {
            background: #fff !important;
            border: 2px solid currentColor;
        }

        .btn-outline:hover {
            opacity: .85;
        }

        .error-header {
            background: linear-gradient(135deg, #e74a3b 0%, #a71d0d 100%);
        }

        .btn-danger-color {
            background: linear-gradient(135deg, #e74a3b 0%, #a71d0d 100%);
        }
    </style>
</head>

<body>
    <div class="error-card">
        <div class="error-header">
            <img src="{{ asset('errors/500.svg') }}" alt="Ikan Terbalik" width="140" height="115">
            <div class="error-code">500</div>
        </div>
        <div class="error-body">
            <div class="error-title">Ups! Ada yang Salah</div>
            <p class="error-desc">
                Ikan server kami sedang terbalik!<br>
                Tim teknis sudah diberitahu. Coba lagi beberapa saat.
            </p>
            <a href="/" class="btn-error btn-danger-color">
                <i class="fas fa-home"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>

</html>
