@extends('layouts.app')

@section('title', 'Laporan Keuangan & Penjualan')
@section('page-title', 'Laporan Analisis Bisnis & Keuangan')

@section('content')
<style>
    @media print {
        .sidebar, .topbar, .filter-section, .btn-print, .no-print, .pagination-box {
            display: none !important;
        }
        .main-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        body {
            background: white !important;
            color: black !important;
        }
        .panel {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
        }
        .print-header {
            display: block !important;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
    }
    .print-header {
        display: none;
    }
</style>

<div class="print-header">
    <h2 style="font-size: 22px; font-weight: 800; margin-bottom: 4px;">LAPORAN LABA / RUGI & ARUS BARANG</h2>
    <p style="font-size: 13px; color: #555;">
        Periode: <strong>{{ $startDate->translatedFormat('d F Y') }}</strong> s/d <strong>{{ $endDate->translatedFormat('d F Y') }}</strong> &bull; Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </p>
</div>

<!-- Filter & Actions Bar -->
<div class="panel filter-section" style="margin-bottom: 24px;">
    <div class="panel-body" style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 16px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main);">Rentang Waktu Laporan</h3>
                <span style="font-size: 13px; color: var(--text-muted);">
                    Data aktif: <strong>{{ $startDate->format('d/m/Y') }}</strong> s/d <strong>{{ $endDate->format('d/m/Y') }}</strong>
                </span>
            </div>

            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <button type="button" class="btn btn-secondary btn-print" onclick="window.print()">
                    🖨️ Cetak Dokumen Laporan
                </button>
            </div>
        </div>

        <form method="GET" action="{{ route('reports.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                <a href="{{ route('reports.index', ['period' => 'today']) }}" class="btn {{ $period === 'today' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Hari Ini</a>
                <a href="{{ route('reports.index', ['period' => 'this_week']) }}" class="btn {{ $period === 'this_week' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Minggu Ini</a>
                <a href="{{ route('reports.index', ['period' => 'this_month']) }}" class="btn {{ $period === 'this_month' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Bulan Ini</a>
                <a href="{{ route('reports.index', ['period' => 'this_year']) }}" class="btn {{ $period === 'this_year' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Tahun Ini</a>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 12px;">Dari Tanggal</label>
                <input type="date" name="from" class="form-control" value="{{ request('from', $startDate->format('Y-m-d')) }}">
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 12px;">Sampai Tanggal</label>
                <input type="date" name="to" class="form-control" value="{{ request('to', $endDate->format('Y-m-d')) }}">
            </div>

            <input type="hidden" name="period" value="custom">

            <div>
                <button type="submit" class="btn btn-primary" style="padding: 10px 18px;">Terapkan Filter</button>
                @if(request()->filled('from') || request()->filled('to') || request('period') !== 'this_month')
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary" style="padding: 10px 14px;">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid-4" style="margin-bottom: 24px;">
    <!-- Omset Penjualan -->
    <div class="kpi-card">
        <div class="kpi-icon" style="background: var(--success-light); color: var(--success);">
            💰
        </div>
        <div>
            <div class="kpi-label">Total Omset Penjualan</div>
            <div class="kpi-value" style="color: var(--success);">
                Rp {{ number_format($totalSales, 0, ',', '.') }}
            </div>
            <div class="kpi-subtext">{{ $salesCount }} transaksi selesai</div>
        </div>
    </div>

    <!-- Total Pembelian Stok -->
    <div class="kpi-card">
        <div class="kpi-icon" style="background: var(--primary-light); color: var(--primary);">
            📥
        </div>
        <div>
            <div class="kpi-label">Pengeluaran Beli Stok (PO)</div>
            <div class="kpi-value" style="color: var(--primary);">
                Rp {{ number_format($totalPurchases, 0, ',', '.') }}
            </div>
            <div class="kpi-subtext">{{ $purchasesCount }} transaksi pasokan</div>
        </div>
    </div>

    <!-- HPP / Modal Barang Terjual -->
    <div class="kpi-card">
        <div class="kpi-icon" style="background: #fef3c7; color: #b45309;">
            📦
        </div>
        <div>
            <div class="kpi-label">HPP Modal Barang Terjual</div>
            <div class="kpi-value" style="color: #b45309;">
                Rp {{ number_format($cogs, 0, ',', '.') }}
            </div>
            <div class="kpi-subtext">Biaya modal produk yang laku</div>
        </div>
    </div>

    <!-- Laba Kotor -->
    <div class="kpi-card">
        <div class="kpi-icon" style="background: {{ $grossProfit >= 0 ? 'var(--success-light)' : 'var(--danger-light)' }}; color: {{ $grossProfit >= 0 ? 'var(--success)' : 'var(--danger)' }};">
            📈
        </div>
        <div>
            <div class="kpi-label">Estimasi Laba Kotor</div>
            <div class="kpi-value" style="color: {{ $grossProfit >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                Rp {{ number_format($grossProfit, 0, ',', '.') }}
            </div>
            <div class="kpi-subtext">
                Margin: <strong>{{ number_format($profitMargin, 1) }}%</strong>
            </div>
        </div>
    </div>
</div>

<!-- Top 5 Best Selling Products in Period -->
<div class="panel" style="margin-bottom: 24px;">
    <div class="panel-header">
        <div>
            <span class="panel-title">🏆 Top 5 Produk Paling Laris Pada Periode Ini</span>
            <span style="font-size: 13px; color: var(--text-muted); display: block;">Berdasarkan total unit kuantitas terjual dalam rentang waktu yang dipilih.</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">Rank</th>
                    <th>Kode SKU</th>
                    <th>Nama Produk</th>
                    <th style="text-align: center;">Jumlah Terjual</th>
                    <th style="text-align: right;">Total Nilai Omset (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProducts as $item)
                    <tr>
                        <td style="font-weight: 700;">
                            @if($loop->iteration == 1) 🥇
                            @elseif($loop->iteration == 2) 🥈
                            @elseif($loop->iteration == 3) 🥉
                            @else #{{ $loop->iteration }}
                            @endif
                        </td>
                        <td><code>{{ $item->sku }}</code></td>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td style="text-align: center;">
                            <span class="badge badge-info" style="font-size: 13px;">{{ number_format($item->total_qty) }} unit</span>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: var(--success);">
                            Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Belum ada produk yang terjual pada rentang tanggal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Detailed Sales in Period -->
<div class="panel" style="margin-bottom: 24px;">
    <div class="panel-header">
        <div>
            <span class="panel-title">📤 Rincian Penjualan (Stock-Out) Periode Ini</span>
            <span style="font-size: 13px; color: var(--text-muted); display: block;">Daftar transaksi penjualan berstatus selesai. Total {{ $salesList->total() }} transaksi.</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>No. Transaksi (SO)</th>
                    <th>Waktu</th>
                    <th>Petugas / Pembeli</th>
                    <th>Jumlah Item</th>
                    <th style="text-align: right;">Total Penjualan</th>
                    <th class="no-print" style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salesList as $sale)
                    <tr>
                        <td>{{ $salesList->firstItem() + $loop->index }}</td>
                        <td><code>{{ $sale->sale_number }}</code></td>
                        <td>{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->user->name ?? '-' }}</td>
                        <td>{{ $sale->items->sum('quantity') }} unit ({{ $sale->items->count() }} macam)</td>
                        <td style="text-align: right; font-weight: 700; color: var(--success);">
                            Rp {{ number_format($sale->total, 0, ',', '.') }}
                        </td>
                        <td class="no-print" style="text-align: right;">
                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary btn-sm" target="_blank">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Tidak ada data penjualan pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($salesList->hasPages())
        <div class="panel-body no-print" style="padding: 12px 24px;">
            {{ $salesList->links() }}
        </div>
    @endif
</div>

<!-- Detailed Purchases in Period -->
<div class="panel">
    <div class="panel-header">
        <div>
            <span class="panel-title">📥 Rincian Pembelian dari Pemasok (Stock-In) Periode Ini</span>
            <span style="font-size: 13px; color: var(--text-muted); display: block;">Daftar penerimaan barang dari supplier. Total {{ $purchasesList->total() }} transaksi.</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>No. Transaksi (PO)</th>
                    <th>Waktu Pembelian</th>
                    <th>Pemasok / Supplier</th>
                    <th>Petugas Input</th>
                    <th style="text-align: right;">Total Pembelian</th>
                    <th class="no-print" style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchasesList as $purchase)
                    <tr>
                        <td>{{ $purchasesList->firstItem() + $loop->index }}</td>
                        <td><code>{{ $purchase->purchase_number }}</code></td>
                        <td>{{ $purchase->purchased_at->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $purchase->supplier->name ?? '-' }}</strong></td>
                        <td>{{ $purchase->user->name ?? '-' }}</td>
                        <td style="text-align: right; font-weight: 700; color: var(--primary);">
                            Rp {{ number_format($purchase->total, 0, ',', '.') }}
                        </td>
                        <td class="no-print" style="text-align: right;">
                            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary btn-sm" target="_blank">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Tidak ada data pembelian pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchasesList->hasPages())
        <div class="panel-body no-print" style="padding: 12px 24px;">
            {{ $purchasesList->links() }}
        </div>
    @endif
</div>
@endsection
