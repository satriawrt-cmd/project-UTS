<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | Inventory & Sales System</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <!-- Chart.js for interactive analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>

        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #eff6ff;
            --secondary: #64748b;
            --success: #16a34a;
            --success-light: #dcfce7;
            --warning: #ea580c;
            --warning-light: #ffedd5;
            --danger: #dc2626;
            --danger-light: #fee2e2;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --sidebar-width: 260px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: #1e293b;
            color: #f1f5f9;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
            transition: transform 0.25s ease-in-out;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }

        .sidebar-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #334155;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
            transition: transform 0.2s ease;
        }

        .brand-icon img:hover {
            transform: scale(1.05);
        }

        .brand-text h2 {
            font-size: 16px;
            font-weight: 700;
            color: white;
            letter-spacing: -0.02em;
        }

        .brand-text span {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sidebar-menu {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .menu-header {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            padding: 12px 12px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 8px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
            transition: all 0.15s ease;
        }

        .nav-item:hover {
            background: #334155;
            color: white;
        }

        .nav-item.active {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: bold;
        }

        .sidebar-user {
            padding: 16px 20px;
            border-top: 1px solid #334155;
            background: #0f172a;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .user-meta {
            flex: 1;
            overflow: hidden;
        }

        .user-meta .name {
            font-size: 13px;
            font-weight: 600;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-meta .role-pill {
            display: inline-block;
            font-size: 10px;
            padding: 1px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .role-owner {
            background: #fde047;
            color: #713f12;
        }

        .role-staff {
            background: #bae6fd;
            color: #0369a1;
        }

        .role-user {
            background: #dcfce7;
            color: #15803d;
        }

        /* Layout Main */
        .layout-main {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 100vh;
        }

        /* Top Bar */
        .topbar {
            height: 64px;
            background: white;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .toggle-btn {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--text-main);
        }

        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-logout {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-logout:hover {
            background: #fca5a5;
            color: #7f1d1d;
        }

        /* Content Container */
        .content-area {
            flex: 1;
            padding: 28px;
            max-width: 1300px;
            width: 100%;
            margin: 0 auto;
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
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
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

        .alert-warning {
            background: var(--warning-light);
            border: 1px solid #fed7aa;
            color: #c2410c;
        }

        /* Cards and Panels */
        .panel {
            background: var(--bg-card);
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
            color: var(--text-main);
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
            padding: 9px 16px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }
        .btn-success:hover {
            background: #15803d;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }
        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-secondary {
            background: white;
            border-color: var(--border);
            color: var(--text-main);
        }
        .btn-secondary:hover {
            background: #f1f5f9;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 5px;
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        table.data-table th {
            background: #f8fafc;
            padding: 12px 16px;
            font-weight: 600;
            color: #475569;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        table.data-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
            vertical-align: middle;
        }

        table.data-table tr:hover td {
            background-color: #f8fafc;
        }

        /* Forms */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 7px;
            font-size: 14px;
            color: var(--text-main);
            background: white;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
        }

        .form-help {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .form-error {
            font-size: 12px;
            color: var(--danger);
            margin-top: 4px;
            font-weight: 500;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-warning {
            background: #ffedd5;
            color: #c2410c;
        }

        .badge-info {
            background: #e0f2fe;
            color: #0369a1;
        }

        /* Pagination container & Custom Pagination */
        .pagination-box {
            margin-top: 20px;
        }

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
            margin-top: 20px;
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
            padding: 6px 12px;
            min-width: 34px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
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

        /* Footer */
        .footer {
            padding: 20px 28px;
            border-top: 1px solid var(--border);
            background: white;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Grid */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
        }

        @media (max-width: 900px) {
            .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid-3 { grid-template-columns: 1fr; }
            .grid-2 { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .layout-main {
                margin-left: 0;
            }
            .toggle-btn {
                display: block;
            }
            .grid-4 { grid-template-columns: 1fr; }
            .content-area { padding: 16px; }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <img src="{{ asset('images/logo.svg') }}" alt="Inventory & Sales Logo">
            </div>
            <div class="brand-text">
                <h2>INVENTORY & SALES</h2>
                <span>Distribusi & Ritel</span>
            </div>
        </div>

        <div class="sidebar-menu">
            @if(auth()->user()->isOwner() || auth()->user()->isStaff())
                <div class="menu-header">Menu Utama</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span>📊</span> Dashboard
                </a>
            @endif

            <div class="menu-header">Toko & Belanja</div>
            <a href="{{ route('shop.index') }}" class="nav-item {{ request()->routeIs('shop.index') ? 'active' : '' }}" title="Katalog belanja produk">
                <span>🛒</span> Katalog Belanja
            </a>
            @php
                $cartItemCount = array_sum(array_column(session('cart', []), 'quantity'));
            @endphp
            <a href="{{ route('shop.cart') }}" class="nav-item {{ request()->routeIs('shop.cart', 'shop.checkout') ? 'active' : '' }}" title="Keranjang belanja">
                <span>🛍️</span> Keranjang
                @if($cartItemCount > 0)
                    <span class="nav-badge" style="background: #16a34a;">{{ $cartItemCount }}</span>
                @endif
            </a>
            <a href="{{ route('my-orders.index') }}" class="nav-item {{ request()->routeIs('my-orders.*') ? 'active' : '' }}" title="Riwayat pesanan">
                <span>📋</span> Pesanan Saya
            </a>

            @if(auth()->user()->isOwner() || auth()->user()->isStaff())
                <div class="menu-header">Master Data</div>
                <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.index', 'products.show', 'products.create', 'products.edit') ? 'active' : '' }}">
                    <span>🏷️</span> Produk
                </a>
                <a href="{{ route('categories.index') }}" class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <span>📁</span> Kategori
                </a>
                <a href="{{ route('suppliers.index') }}" class="nav-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <span>🏭</span> Pemasok
                </a>
                <a href="{{ route('products.low-stock') }}" class="nav-item {{ request()->routeIs('products.low-stock') ? 'active' : '' }}">
                    <span>⚠️</span> Stok Menipis
                    @php
                        $lowStockBadgeCount = \App\Models\Product::whereColumn('stock', '<=', 'minimum_stock')->count();
                    @endphp
                    @if($lowStockBadgeCount > 0)
                        <span class="nav-badge">{{ $lowStockBadgeCount }}</span>
                    @endif
                </a>

                <div class="menu-header">Transaksi</div>
                <a href="{{ route('purchases.index') }}" class="nav-item {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                    <span>📥</span> Pembelian (Stock-In)
                </a>
                <a href="{{ route('sales.index') }}" class="nav-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                    <span>📤</span> Penjualan (Stock-Out)
                    @php
                        $pendingCancelSidebarCount = \App\Models\Sale::where('status', 'pending_cancel')->count();
                    @endphp
                    @if($pendingCancelSidebarCount > 0)
                        <span class="nav-badge" style="background: #ea580c;" title="{{ $pendingCancelSidebarCount }} Pesanan Butuh Konfirmasi">{{ $pendingCancelSidebarCount }}</span>
                    @endif
                </a>
                <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <span>📈</span> Laporan Bisnis
                </a>
            @endif

            @if(auth()->user()->isOwner())
                <div class="menu-header">Administrasi (Owner)</div>
                <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <span>👥</span> Manajemen Pengguna
                </a>
            @endif
        </div>

        <div class="sidebar-user">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="user-meta">
                <div class="name" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</div>
                <span class="role-pill {{ auth()->user()->isOwner() ? 'role-owner' : (auth()->user()->isStaff() ? 'role-staff' : 'role-user') }}">
                    {{ auth()->user()->isCustomer() ? 'Customer' : auth()->user()->role }}
                </span>
            </div>
        </div>
    </aside>

    <!-- Layout Main Wrapper -->
    <div class="layout-main">
        <!-- Topbar Header -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="toggle-btn" id="menuToggle" aria-label="Toggle Navigation">☰</button>
                <div class="page-title">@yield('page-title', 'Inventory & Sales Management')</div>
            </div>

            <div class="topbar-right">
                <span style="font-size: 13px; color: var(--text-muted);">
                    Halo, <strong>{{ auth()->user()->name }}</strong>
                </span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="content-area">
            <!-- Flash Success Message -->
            @if(session('success'))
                <div class="alert alert-success">
                    <span>✅</span>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <!-- Flash Error Message -->
            @if(session('error'))
                <div class="alert alert-danger">
                    <span>⛔</span>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            <!-- Validation Errors List -->
            @if($errors->any())
                <div class="alert alert-danger">
                    <span>⚠️</span>
                    <div>
                        <strong>Terjadi kesalahan input:</strong>
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

        <!-- Footer -->
        <footer class="footer">
            Inventory & Sales Management System &bull; Web Programming 2 Integrated Project
        </footer>
    </div>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });

            // Close sidebar on click outside in mobile view
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                        sidebar.classList.remove('open');
                    }
                }
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
