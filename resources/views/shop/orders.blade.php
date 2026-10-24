@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya')
@section('page-title', 'Riwayat Pesanan Saya')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2 style="font-size: 22px; font-weight: 800;">📋 Riwayat Pesanan Saya</h2>
        <span style="font-size: 13px; color: var(--text-muted);">Daftar seluruh transaksi belanja yang telah Anda lakukan.</span>
    </div>
    <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm">+ Belanja Lagi</a>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>Nomor Pesanan (SO)</th>
                    <th>Waktu Pemesanan</th>
                    <th>Jumlah Jenis Barang</th>
                    <th>Total Belanja</th>
                    <th>Status Pesanan</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $orders->firstItem() + $loop->index }}</td>
                        <td>
                            <a href="{{ route('my-orders.show', $order) }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">
                                <code>{{ $order->sale_number }}</code>
                            </a>
                        </td>
                        <td>{{ $order->sold_at->format('d F Y, H:i') }}</td>
                        <td>{{ $order->items->count() }} Macam Item</td>
                        <td><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></td>
                        <td>
                            @if($order->isPendingCancel())
                                <span class="badge badge-warning" style="background: #ffedd5; color: #c2410c;">⏳ Menunggu Batal</span>
                            @elseif($order->isCompleted())
                                <span class="badge badge-success">Selesai / Terbayar</span>
                            @else
                                <span class="badge badge-danger">Dibatalkan</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('my-orders.show', $order) }}" class="btn btn-secondary btn-sm">
                                Lihat Struk Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 45px;">
                            <div style="font-size: 32px; margin-bottom: 8px;">🛒</div>
                            <p style="font-size: 15px; font-weight: 600;">Anda belum pernah melakukan pemesanan.</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm" style="margin-top: 12px;">Mulai Belanja Sekarang</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="panel-body" style="padding: 14px 24px;">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
