@extends('layouts.app')

@section('title', 'Riwayat Penjualan (Stock-Out)')
@section('page-title', 'Transaksi Penjualan & Kasir')

@section('content')
@if(($pendingCancelCount ?? 0) > 0)
    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="font-size: 24px;">⚠️</div>
            <div>
                <strong style="color: #92400e; font-size: 14px;">Ada {{ $pendingCancelCount }} Pengajuan Pembatalan Pesanan dari Pembeli!</strong>
                <p style="color: #b45309; font-size: 13px; margin: 2px 0 0 0;">
                    Pembeli mengajukan pembatalan pesanan. Owner harus meninjau dan mengonfirmasi apakah disetujui (stok dikembalikan) atau ditolak.
                </p>
            </div>
        </div>
        <a href="{{ route('sales.index', ['status' => 'pending_cancel']) }}" class="btn btn-warning btn-sm" style="background: #f59e0b; color: white;">
            Filter Pesanan Pending ({{ $pendingCancelCount }})
        </a>
    </div>
@endif

<div class="panel">
    <div class="panel-header">
        <div>
            <span class="panel-title">📤 Daftar Transaksi Penjualan (Stock-Out)</span>
            <span style="font-size: 13px; color: var(--text-muted); display: block;">Total {{ $sales->total() }} catatan transaksi penjualan.</span>
        </div>

        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
            + Catat Penjualan Baru
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="panel-body" style="background: #f8fafc; border-bottom: 1px solid var(--border); padding: 16px 24px;">
        <form method="GET" action="{{ route('sales.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 180px;">
                <label class="form-label" style="font-size: 12px;">Cari No. SO</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="SO-2026..."
                    value="{{ request('search') }}"
                >
            </div>

            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 12px;">Status</label>
                <select name="status" class="form-control">
                    <option value="">-- Semua Status --</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                    <option value="pending_cancel" {{ request('status') === 'pending_cancel' ? 'selected' : '' }}>Pending Cancel (Menunggu Owner)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled (Dibatalkan)</option>
                </select>
            </div>

            <div style="flex: 1; min-width: 130px;">
                <label class="form-label" style="font-size: 12px;">Dari Tanggal</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>

            <div style="flex: 1; min-width: 130px;">
                <label class="form-label" style="font-size: 12px;">Sampai Tanggal</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>

            <div>
                <button type="submit" class="btn btn-primary" style="padding: 10px 16px;">Filter</button>
                @if(request()->anyFilled(['search', 'status', 'from', 'to']))
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary" style="padding: 10px 14px;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>Nomor SO</th>
                    <th>Waktu Penjualan</th>
                    <th>Kasir / Petugas</th>
                    <th>Total Transaksi</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td>{{ $sales->firstItem() + $loop->index }}</td>
                        <td>
                            <a href="{{ route('sales.show', $sale) }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">
                                <code>{{ $sale->sale_number }}</code>
                            </a>
                        </td>
                        <td>{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $sale->user->name ?? '-' }}</strong></td>
                        <td><strong>Rp {{ number_format($sale->total, 0, ',', '.') }}</strong></td>
                        <td>
                            @if($sale->isPendingCancel())
                                <span class="badge badge-warning">⏳ Pending Cancel</span>
                            @elseif($sale->isCompleted())
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-danger">Cancelled</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary btn-sm">
                                Detail Struk
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 35px;">
                            Tidak ada transaksi penjualan yang cocok dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sales->hasPages())
        <div class="panel-body" style="padding: 14px 24px;">
            {{ $sales->links() }}
        </div>
    @endif
</div>
@endsection
