<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | Si-Pita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            color-scheme: light;
            --auth-ink: #18356f;
            --auth-blue: #315fd0;
            --auth-header: #edf2ff;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            min-height: 100svh;
            margin: 0;
            display: flex;
            flex-direction: column;
            color: var(--auth-ink);
            background: #f3f5fa;
            font-family: "Avenir Next", Avenir, "Segoe UI", sans-serif;
        }

        .auth-header {
            min-height: 60px;
            padding-inline: clamp(16px, 5vw, 56px);
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--auth-header);
        }

        .auth-menu-button {
            width: 32px;
            height: 36px;
            padding: 0;
            border: 0;
            color: var(--auth-ink);
            background: transparent;
            font-size: 21px;
        }

        .auth-current {
            margin-left: auto;
            color: #26334e;
            font-size: 13px;
            text-decoration: none;
        }

        .auth-main {
            flex: 1;
            padding: 48px 20px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #edf1f9, #e6eafa);
        }

        .login-card {
            position: relative;
            width: min(100%, 390px);
            padding: 56px 34px 30px;
            border: 2px solid #fff;
            border-radius: 18px;
            background: #eef2ff;
            box-shadow: 0 12px 32px rgba(39, 64, 119, 0.08);
        }

        .login-avatar {
            position: absolute;
            top: -34px;
            left: 50%;
            width: 64px;
            height: 64px;
            display: grid;
            place-items: center;
            transform: translateX(-50%);
            border: 3px solid #fff;
            border-radius: 50%;
            color: #fff;
            background: #a1a1a1;
            font-size: 27px;
        }

        .login-back {
            position: absolute;
            top: 14px;
            left: 14px;
            padding: 4px 10px;
            border-radius: 5px;
            color: #fff;
            background: var(--auth-blue);
            font-size: 11px;
            text-decoration: none;
        }

        .login-title {
            margin: 0 0 24px;
            color: var(--auth-ink);
            font-size: 19px;
            font-weight: 700;
            text-align: center;
        }

        .login-field {
            width: 100%;
            height: 40px;
            margin-bottom: 14px;
            padding: 0 15px;
            border: 1px solid #fff;
            border-radius: 999px;
            outline: none;
            color: #fff;
            background: var(--auth-blue);
            font-size: 13px;
        }

        .login-field::placeholder { color: #e4ebff; opacity: 1; }
        .login-field:focus { box-shadow: 0 0 0 3px rgba(49, 95, 208, 0.18); }

        .login-submit {
            width: 64%;
            min-height: 42px;
            margin: 15px auto 0;
            display: block;
            border: 0;
            border-radius: 10px;
            color: #fff;
            background: var(--auth-blue);
            font-size: 13px;
            font-weight: 600;
        }

        .login-submit:hover { background: #284fae; }

        .auth-footer {
            min-height: 36px;
            padding: 9px 16px;
            color: #34415c;
            background: var(--auth-header);
            font-size: 11px;
            text-align: center;
        }

        .auth-menu-link {
            display: block;
            padding: 12px 0;
            border-bottom: 1px solid #e5eaf5;
            color: var(--auth-ink);
            text-decoration: none;
        }

        @media (max-width: 480px) {
            .auth-main { padding: 42px 18px; }
            .login-card { padding-inline: 24px; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('si-pita-theme.css') }}">
</head>
<body>
    <header class="auth-header">
        <button class="auth-menu-button" type="button" data-bs-toggle="offcanvas" data-bs-target="#authMenu" aria-controls="authMenu" aria-label="Buka menu">
            <i class="bi bi-list" aria-hidden="true"></i>
        </button>
        <a href="{{ route('landing') }}" class="si-pita-brand">SI - PITA</a>
        <a href="{{ route('login') }}" class="auth-current" aria-current="page">Login</a>
    </header>

    <main class="auth-main">
        <section class="login-card" aria-labelledby="login-title">
            <div class="login-avatar" aria-hidden="true"><i class="bi bi-person-fill"></i></div>
            <a href="{{ route('landing') }}" class="login-back"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
            <h1 class="login-title" id="login-title">Login Admin</h1>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <label for="email" class="visually-hidden">Email</label>
                <input id="email" type="email" class="login-field @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="email" autofocus>
                @error('email')
                    <div class="invalid-feedback d-block text-center mb-2">{{ $message }}</div>
                @enderror

                <label for="password" class="visually-hidden">Password</label>
                <input id="password" type="password" class="login-field @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback d-block text-center mb-2">{{ $message }}</div>
                @enderror

                <button type="submit" class="login-submit">Login</button>
            </form>
        </section>
    </main>

    <footer class="auth-footer">© Copyright DISKOMINFO × Diskominfo</footer>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="authMenu" aria-labelledby="authMenuTitle">
        <div class="offcanvas-header">
            <h2 class="offcanvas-title si-pita-brand" id="authMenuTitle">SI - PITA</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body pt-0">
            <a class="auth-menu-link" href="{{ route('landing') }}">Halaman publik</a>
            <a class="auth-menu-link" href="{{ route('login') }}">Login admin</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>