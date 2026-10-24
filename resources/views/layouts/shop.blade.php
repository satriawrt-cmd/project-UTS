<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Toko Online') | Inventory & Sales Store</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #eff6ff;
            --success: #16a34a;
            --success-light: #dcfce7;
            --danger: #dc2626;
            --danger-light: #fee2e2;
            --warning: #ea580c;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg-page: #f8fafc;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navigation Header */
        .shop-header {
            background: white;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text-main);
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
            transition: transform 0.2s ease;
        }

        .brand-logo img:hover {
            transform: scale(1.05);
        }

        .brand-info h1 {
            font-size: 17px;
            font-weight: 800;
            line-height: 1.2;
        }

        .brand-info span {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .shop-nav {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.15s;
        }

        .nav-link:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .nav-link.active {
            background: var(--primary-light);
            color: var(--primary);
        }

        .cart-badge {
            background: var(--danger);
            color: white;
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: bold;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-panel {
            background: #f1f5f9;
            color: #1e293b;
            border: 1px solid var(--border);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-panel:hover {
            background: #e2e8f0;
        }

        .btn-logout {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        /* Container */
        .main-container {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 24px auto;
            padding: 0 20px;
        }

        /* Alerts */
        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-success {
            background: var(--success-light);
            border: 1px solid #bbf7d0;
            color: #15803d;
        }

        .alert-danger {
            background: var(--danger-light);
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        /* Card Panels */
        .panel {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .panel-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .panel-title {
            font-size: 16px;
            font-weight: 700;
        }

        .panel-body {
            padding: 24px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #15803d; }
        .btn-secondary { background: white; border-color: var(--border); color: var(--text-main); }
        .btn-secondary:hover { background: #f1f5f9; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .badge-warning { background: #ffedd5; color: #c2410c; }
        .badge-info { background: #e0f2fe; color: #0369a1; }

        /* Tables */
        .table-responsive { width: 100%; overflow-x: auto; }
        table.data-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        table.data-table th { background: #f8fafc; padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border); color: #475569; }
        table.data-table td { padding: 14px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        table.data-table tr:hover td { background-color: #f8fafc; }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 7px;
            font-size: 14px;
        }

        /* Custom Clean Pagination */
        .custom-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            background: white;
            padding: 14px 20px;
            border-radius: 10px;
            border: 1px solid var(--border);
            margin-top: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .pagination-info {
            font-size: 13px;
            color: var(--text-muted);
        }

        .pagination-info strong {
            color: var(--text-main);
        }

        .pagination-links {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }

        .page-item {
            display: inline-block;
        }

        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 13px;
            min-width: 36px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 7px;
            border: 1px solid var(--border);
            background: white;
            color: var(--text-main);
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .page-link:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: var(--primary);
        }

        .page-item.active .page-link {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 2px 6px rgba(37,99,235,0.3);
        }

        .page-item.disabled .page-link {
            color: #94a3b8;
            background: #f8fafc;
            border-color: #e2e8f0;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Failsafe against any oversized SVGs */
        nav svg,
        .custom-pagination svg,
        svg.w-5,
        svg.h-5 {
            width: 16px !important;
            height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        .footer {
            background: white;
            border-top: 1px solid var(--border);
            padding: 24px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .header-container { flex-direction: column; align-items: flex-start; }
            .shop-nav { width: 100%; justify-content: space-between; }
        }
    </style>
    @yield('styles')
</head>
<body>

    <header class="shop-header">
        <div class="header-container">
            <a href="{{ route('shop.index') }}" class="brand-link">
                <div class="brand-logo">
                    <img src="{{ asset('images/shop-logo.svg') }}" alt="Toko Distribusi Online Logo">
                </div>
                <div class="brand-info">
                    <h1>TOKO DISTRIBUSI ONLINE</h1>
                    <span>Belanja Mudah &bull; Stok Terjamin</span>
                </div>
            </a>

            <nav class="shop-nav">
                <a href="{{ route('shop.index') }}" class="nav-link {{ request()->routeIs('shop.index') ? 'active' : '' }}">
                    🏷️ Katalog Produk
                </a>

                @php
                    $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
                @endphp
                <a href="{{ route('shop.cart') }}" class="nav-link {{ request()->routeIs('shop.cart', 'shop.checkout') ? 'active' : '' }}">
                    🛒 Keranjang
                    @if($cartCount > 0)
                        <span class="cart-badge">{{ $cartCount }}</span>
                    @endif
                </a>

                <a href="{{ route('my-orders.index') }}" class="nav-link {{ request()->routeIs('my-orders.*') ? 'active' : '' }}">
                    📋 Pesanan Saya
                </a>
            </nav>

            <div class="user-section">
                @if(auth()->user()->isOwner() || auth()->user()->isStaff())
                    <a href="{{ route('dashboard') }}" class="btn-panel" title="Kembali ke panel manajemen stok & kasir">
                        ⚙️ Panel Admin
                    </a>
                @endif

                <span style="font-size: 13px; color: var(--text-muted);">
                    Halo, <strong>{{ auth()->user()->name }}</strong>
                </span>

                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="main-container">
        @if(session('success'))
            <div class="alert alert-success">
                <span>✅</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <span>⛔</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <span>⚠️</span>
                <div>
                    <strong>Pemberitahuan:</strong>
                    <ul style="margin-top: 6px; padding-left: 18px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <p><strong>Toko Distribusi Online &bull; Inventory & Sales Management</strong></p>
        <p style="margin-top: 6px; font-size: 12px;">
            Setiap transaksi terintegrasi langsung dengan pemotongan stok otomatis di gudang sistem.
        </p>
    </footer>

    @yield('scripts')
</body>
</html>
