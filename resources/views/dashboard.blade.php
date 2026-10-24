@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Ringkasan Dashboard & Analitik')

@section('content')
<!-- Pending Cancellation Alert (Owner & Staff Notification) -->
@if($pendingCancelCount > 0)
    <div class="alert alert-warning" style="background: #fff7ed; border: 1px solid #fdba74; color: #9a3412; margin-bottom: 24px;">
        <span style="font-size: 22px;">⚠️</span>
        <div style="flex: 1;">
            <div style="font-size: 15px; font-weight: 700;">
                Permintaan Pembatalan Pesanan Menunggu Konfirmasi!
            </div>
            <p style="font-size: 13px; margin: 4px 0 8px; color: #c2410c;">
                Terdapat <strong>{{ $pendingCancelCount }}</strong> pesanan yang diajukan pembatalan oleh pembeli/pelanggan. Harap verifikasi dan konfirmasi apakah pembatalan disetujui (stok dikembalikan) atau ditolak.
            </p>
            <a href="{{ route('sales.index', ['status' => 'pending_cancel']) }}" class="btn btn-primary btn-sm" style="background: #ea580c; border-color: #c2410c;">
                Periksa & Konfirmasi Pembatalan Sekarang &rarr;
            </a>
        </div>
    </div>
@endif

<!-- Quick Action Shortcuts -->
<div style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
    <a href="{{ route('sales.create') }}" class="btn btn-primary">
        <span>➕</span> Catat Penjualan (Kasir)
    </a>
    <a href="{{ route('purchases.create') }}" class="btn btn-success">
        <span>➕</span> Catat Pembelian (Stock-In)
    </a>
    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
        <span>📈</span> Lihat Laporan Bisnis
    </a>
    @if(auth()->user()->isOwner())
        <a href="{{ route('products.create') }}" class="btn btn-secondary">
            <span>🏷️</span> Tambah Master Produk
        </a>
    @endif
    <a href="{{ route('products.low-stock') }}" class="btn btn-secondary" style="border-color: #fca5a5; color: #b91c1c;">
        <span>⚠️</span> Periksa Stok Menipis ({{ $lowStock->total() }})
    </a>
</div>

<!-- Stat KPI Cards -->
<div class="grid-3" style="margin-bottom: 24px;">
    <!-- Card 1 -->
    <div class="panel" style="border-left: 5px solid var(--primary); margin-bottom: 0;">
        <div class="panel-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Master Produk</div>
            <div style="font-size: 32px; font-weight: 800; color: var(--text-main); margin-top: 4px;">
                {{ number_format($productCount) }}
            </div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                <a href="{{ route('products.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">Lihat katalog produk &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="panel" style="border-left: 5px solid var(--success); margin-bottom: 0;">
        <div class="panel-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Penjualan Hari Ini</div>
            <div style="font-size: 28px; font-weight: 800; color: #15803d; margin-top: 4px;">
                Rp {{ number_format($salesToday, 0, ',', '.') }}
            </div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                Bulan Ini: <strong>Rp {{ number_format($salesMonth, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="panel" style="border-left: 5px solid var(--danger); margin-bottom: 0;">
        <div class="panel-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Produk Stok Menipis</div>
            <div style="font-size: 32px; font-weight: 800; color: var(--danger); margin-top: 4px;">
                {{ number_format($lowStock->total()) }}
            </div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                Perlu restock segera &bull; <a href="{{ route('products.low-stock') }}" style="color: var(--danger); text-decoration: none; font-weight: 600;">Detail &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- 📊 SECTION GRAFIK ANALITIK BISNIS -->
<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Grafik 1: Tren Penjualan 7 Hari Terakhir -->
    <div class="panel" style="margin-bottom: 0;">
        <div class="panel-header">
            <div>
                <span class="panel-title">📈 Tren Transaksi 7 Hari Terakhir</span>
                <div style="font-size: 12px; color: var(--text-muted);">Grafik perbandingan nilai Penjualan vs Pembelian Stok</div>
            </div>
            <span class="badge badge-info">7 Hari</span>
        </div>
        <div class="panel-body">
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Grafik 2: Komposisi Penjualan per Kategori -->
    <div class="panel" style="margin-bottom: 0;">
        <div class="panel-header">
            <div>
                <span class="panel-title">🍩 Komposisi Kategori Produk</span>
                <div style="font-size: 12px; color: var(--text-muted);">{{ $categoryTitle }}</div>
            </div>
            <span class="badge badge-success">Kategori</span>
        </div>
        <div class="panel-body">
            <div style="position: relative; height: 260px; width: 100%; display: flex; align-items: center; justify-content: center;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Table Panel -->
<div class="panel">
    <div class="panel-header">
        <div>
            <div class="panel-title">⚠️ Peringatan Stok Menipis (Di Bawah Batas Minimum)</div>
            <span style="font-size: 12px; color: var(--text-muted);">Segera lakukan pembelian stok (stock-in) untuk mencegah kehabisan barang.</span>
        </div>
        <a href="{{ route('purchases.create') }}" class="btn btn-success btn-sm">+ Buat PO Pembelian</a>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Stok Saat Ini</th>
                    <th>Batas Minimum</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lowStock as $prod)
                    <tr>
                        <td>{{ $lowStock->firstItem() + $loop->index }}</td>
                        <td><code>{{ $prod->sku }}</code></td>
                        <td><strong>{{ $prod->name }}</strong></td>
                        <td>{{ $prod->category->name ?? '-' }}</td>
                        <td>
                            <strong style="color: var(--danger); font-size: 15px;">{{ $prod->stock }}</strong>
                        </td>
                        <td>{{ $prod->minimum_stock }}</td>
                        <td>
                            <span class="badge badge-danger">Stok Menipis</span>
                        </td>
                        <td>
                            <a href="{{ route('purchases.create') }}" class="btn btn-success btn-sm">Restock</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            🎉 Semua stok produk saat ini aman (di atas batas minimum).
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($lowStock->hasPages())
        <div class="panel-body" style="padding: 12px 24px;">
            {{ $lowStock->links() }}
        </div>
    @endif
</div>

<!-- Recent Transactions Grid -->
<div class="grid-2">
    <!-- Recent Sales Panel -->
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">📤 5 Penjualan Terbaru</span>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">Semua Penjualan</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. SO</th>
                        <th>Waktu</th>
                        <th>Kasir / User</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales as $sale)
                        <tr>
                            <td><code>{{ $sale->sale_number }}</code></td>
                            <td>{{ $sale->sold_at->format('d/m H:i') }}</td>
                            <td>{{ $sale->user->name ?? '-' }}</td>
                            <td><strong>Rp {{ number_format($sale->total, 0, ',', '.') }}</strong></td>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 20px;">
                                Belum ada transaksi penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Purchases Panel -->
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">📥 5 Pembelian Terbaru</span>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm">Semua Pembelian</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. PO</th>
                        <th>Waktu</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPurchases as $purchase)
                        <tr>
                            <td><code>{{ $purchase->purchase_number }}</code></td>
                            <td>{{ $purchase->purchased_at->format('d/m H:i') }}</td>
                            <td>{{ $purchase->supplier->name ?? '-' }}</td>
                            <td><strong>Rp {{ number_format($purchase->total, 0, ',', '.') }}</strong></td>
                            <td>
                                <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 20px;">
                                Belum ada transaksi pembelian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data dari Controller
        const trendLabels = @json($trendLabels);
        const trendSales = @json($trendSales);
        const trendPurchases = @json($trendPurchases);

        const categoryLabels = @json($categoryLabels);
        const categoryData = @json($categoryData);

        // 1. Line Chart: Tren Penjualan & Pembelian 7 Hari
        const ctxTrend = document.getElementById('trendChart');
        if (ctxTrend && typeof Chart !== 'undefined') {
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [
                        {
                            label: 'Penjualan (Omset)',
                            data: trendSales,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#2563eb'
                        },
                        {
                            label: 'Pembelian (Modal)',
                            data: trendPurchases,
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22, 163, 74, 0.05)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0.35,
                            pointRadius: 3,
                            pointBackgroundColor: '#16a34a'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + Number(context.raw).toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + Number(value).toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Doughnut Chart: Penjualan per Kategori
        const ctxCat = document.getElementById('categoryChart');
        if (ctxCat && typeof Chart !== 'undefined' && categoryLabels.length > 0) {
            new Chart(ctxCat, {
                type: 'doughnut',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryData,
                        backgroundColor: [
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#8b5cf6',
                            '#ec4899',
                            '#06b6d4',
                            '#f97316'
                        ],
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + Number(context.raw).toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection