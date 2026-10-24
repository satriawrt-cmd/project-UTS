<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Inventory & Sales Management</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --border: #cbd5e1;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: var(--text-main);
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 36px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
        }

        .logo-box {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .logo-badge {
            width: 50px;
            height: 50px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-badge img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
        }

        .logo-title h1 {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.2;
        }

        .logo-title span {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.15s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 16px 0 20px;
            font-size: 13px;
            color: #475569;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: var(--primary);
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
        }

        .alert-error {
            padding: 12px 14px;
            border-radius: 8px;
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 24px;
            text-align: center;
            color: var(--text-muted);
            font-size: 12px;
        }
    </style>
</head>
<body>

    <main class="login-card">
        <div class="logo-box">
            <div class="logo-badge">
                <img src="{{ asset('images/logo.svg') }}" alt="Inventory & Sales Logo">
            </div>
            <div class="logo-title">
                <h1>INVENTORY & SALES</h1>
                <span>Integrated Laravel System</span>
            </div>
        </div>

        <p class="subtitle">
            Silakan masuk untuk mengelola inventaris, transaksi pembelian, dan penjualan.
        </p>

        @if ($errors->any())
            <div class="alert-error" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Pengguna</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password akun"
                    autocomplete="current-password"
                    required
                >
            </div>

            <div class="remember-row">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    value="1"
                >
                <label for="remember" style="margin: 0; font-weight: normal; cursor: pointer;">Ingat sesi login saya</label>
            </div>

            <button type="submit" class="btn-submit">Masuk ke Sistem &rarr;</button>
        </form>

        <div class="footer" style="margin-top: 24px;">
            Belum punya akun? <a href="{{ route('register') }}" style="color: var(--primary); text-decoration: none; font-weight: 700;">Daftar Akun Belanja di sini &rarr;</a>
        </div>

        <div class="footer" style="margin-top: 10px; font-size: 11px;">
            Web Programming 2 &bull; Ujian Tengah Semester
        </div>
    </main>
</body>
</html>
