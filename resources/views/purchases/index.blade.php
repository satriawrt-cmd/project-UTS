@extends('layouts.app')

@section('title', 'Riwayat Pembelian (Stock-In)')
@section('page-title', 'Transaksi Pembelian & Stok Masuk')

@section('content')
<div class="panel">
    <div class="panel-header">
        <div>
            <span class="panel-title">📥 Daftar Transaksi Pembelian (Stock-In)</span>
            <span style="font-size: 13px; color: var(--text-muted); display: block;">Total {{ $purchases->total() }} catatan transaksi pembelian.</span>
        </div>

        <a href="{{ route('purchases.create') }}" class="btn btn-success btn-sm">
            + Catat Pembelian Baru
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="panel-body" style="background: #f8fafc; border-bottom: 1px solid var(--border); padding: 16px 24px;">
        <form method="GET" action="{{ route('purchases.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 180px;">
                <label class="form-label" style="font-size: 12px;">Cari No. PO</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="PO-2026..."
                    value="{{ request('search') }}"
                >
            </div>

            <div style="flex: 1; min-width: 160px;">
                <label class="form-label" style="font-size: 12px;">Filter Supplier</label>
                <select name="supplier_id" class="form-control">
                    <option value="">-- Semua Supplier --</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                            {{ $sup->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 12px;">Status</label>
                <select name="status" class="form-control">
                    <option value="">-- Semua Status --</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                @if(request()->anyFilled(['search', 'supplier_id', 'status', 'from', 'to']))
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary" style="padding: 10px 14px;">Reset</a>
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
                    <th>Nomor PO</th>
                    <th>Tanggal Beli</th>
                    <th>Mitra Supplier</th>
                    <th>Petugas Input</th>
                    <th>Total Transaksi</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                    <tr>
                        <td>{{ $purchases->firstItem() + $loop->index }}</td>
                        <td>
                            <a href="{{ route('purchases.show', $purchase) }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">
                                <code>{{ $purchase->purchase_number }}</code>
                            </a>
                        </td>
                        <td>{{ $purchase->purchased_at->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $purchase->supplier->name ?? '-' }}</strong></td>
                        <td>{{ $purchase->user->name ?? '-' }}</td>
                        <td><strong>Rp {{ number_format($purchase->total, 0, ',', '.') }}</strong></td>
                        <td>
                            @if($purchase->isCompleted())
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-danger">Cancelled</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary btn-sm">
                                Detail Transaksi
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 35px;">
                            Tidak ada transaksi pembelian yang cocok dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($purchases->hasPages())
        <div class="panel-body" style="padding: 14px 24px;">
            {{ $purchases->links() }}
        </div>
    @endif
</div>
@endsection
