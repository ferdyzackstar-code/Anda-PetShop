<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Sesi Berakhir | PetShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f0f7ff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-box {
            max-width: 800px;
            width: 100%;
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .animal-img {
            width: 150px;
            height: 150px;
            object-fit: contain;
            margin-bottom: 20px;
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }

            100% {
                transform: translateY(0px);
            }
        }
    </style>
</head>

<body>
    <div class="error-box">
        <img src="{{ asset('asset/img/kucingtidur2.jpg') }}" class="animal-img">
        <h1 class="display-1 fw-bold text-warning">419</h1>
        <h2 class="fw-bold">Sesi Anda Tertidur...</h2>
        <p class="text-muted">Sepertinya kamu meninggalkan halaman ini terlalu lama sampai kucing kami tertidur. Silakan
            segarkan halaman.</p>
        <a href="{{ route('dashboard.index') }}" class="btn btn-warning btn-lg rounded-pill shadow px-5 mt-3">
            <i class="fas fa-sync-alt me-2"></i> Refresh Halaman
        </a>
    </div>
</body>

</html>
