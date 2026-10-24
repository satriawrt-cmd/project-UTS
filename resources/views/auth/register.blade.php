<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Belanja | Inventory & Sales</title>
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

        .card {
            width: 100%;
            max-width: 450px;
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
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        }

        .logo-title h1 {
            font-size: 20px;
            font-weight: 800;
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
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.15s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
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
            margin-top: 10px;
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
            font-size: 13px;
            color: var(--text-muted);
        }

        .footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <main class="card">
        <div class="logo-box">
            <div class="logo-badge">
                <img src="{{ asset('images/shop-logo.svg') }}" alt="Daftar Akun Belanja Logo">
            </div>
            <div class="logo-title">
                <h1>DAFTAR AKUN BELANJA</h1>
                <span>Akun Konsumen / Pembeli</span>
            </div>
        </div>

        <p class="subtitle">
            Daftarkan diri Anda untuk mulai memilih produk, memasukkan ke keranjang, dan melakukan transaksi belanja online.
        </p>

        @if ($errors->any())
            <div class="alert-error" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.process') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Contoh: Rina Melati"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimal 8 karakter"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password_confirmation">Ulangi Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Ketik ulang password"
                    required
                >
            </div>

            <button type="submit" class="btn-submit">Daftar & Langsung Belanja &rarr;</button>
        </form>

        <div class="footer">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </main>

</body>
</html>
