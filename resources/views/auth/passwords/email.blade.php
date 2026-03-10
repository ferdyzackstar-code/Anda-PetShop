<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Anda Petshop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background: linear-gradient(to right, #e2e2e2, #c9d6ff);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
            position: relative;
            width: 450px;
            max-width: 100%;
            padding: 40px;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }

        p {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        input {
            background-color: #eee;
            border: none;
            margin: 8px 0;
            padding: 12px 15px;
            font-size: 13px;
            border-radius: 8px;
            width: 100%;
            outline: none;
        }

        button {
            background-color: #512da8;
            color: #fff;
            font-size: 12px;
            padding: 12px 45px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 20px;
            cursor: pointer;
            width: 100%;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            font-size: 13px;
            color: #512da8;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="container">
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <h1>Reset Password</h1>
            <p>Kami akan mengirimkan link reset ke email Anda</p>

            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <input id="email" type="email" name="email" value="{{ old('email') }}"
                placeholder="Enter Email Address" class="@error('email') is-invalid @enderror" required
                autocomplete="email" autofocus>

            @error('email')
                <span style="color: red; font-size: 11px;">{{ $message }}</span>
            @enderror

            <button type="submit">
                Send Reset Link
            </button>

            <a href="{{ route('login') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Login
            </a>
        </form>
    </div>

</body>

</html>
