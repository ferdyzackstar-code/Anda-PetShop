<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Anda Petshop</title>
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

        .is-invalid {
            border: 1px solid #ff4d4d !important;
        }

        .error-msg {
            color: #ff4d4d;
            font-size: 11px;
            text-align: left;
            display: block;
            margin-top: -5px;
        }
    </style>
</head>

<body>

    <div class="container">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <h1>Reset Password</h1>
            <p>Silakan buat password baru Anda</p>

            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}"
                placeholder="Email Address" class="@error('email') is-invalid @enderror" required autocomplete="email">
            @error('email')
                <span class="error-msg"><strong>{{ $message }}</strong></span>
            @enderror

            <input id="password" type="password" name="password" placeholder="New Password"
                class="@error('password') is-invalid @enderror" required autocomplete="new-password">
            @error('password')
                <span class="error-msg"><strong>{{ $message }}</strong></span>
            @enderror

            <input id="password-confirm" type="password" name="password_confirmation" placeholder="Confirm New Password"
                required autocomplete="new-password">

            <button type="submit">
                Update Password
            </button>
        </form>
    </div>

</body>

</html>
