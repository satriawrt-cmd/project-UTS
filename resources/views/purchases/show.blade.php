@extends('layouts.app')

@section('title', 'Detail Pembelian: ' . $purchase->purchase_number)
@section('page-title', 'Detail Transaksi Pembelian')

@section('content')
<div style="max-width: 950px; margin: 0 auto;">
    <div style="display: flex; gap: 12px; margin-bottom: 20px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Riwayat Pembelian</a>

        <div style="display: flex; gap: 8px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">
                🖨️ Cetak Bukti PO
            </button>
            <a href="{{ route('purchases.create') }}" class="btn btn-success btn-sm">
                + Buat Pembelian Baru
            </a>
        </div>
    </div>

    <div class="panel">
        <!-- Invoice Header -->
        <div class="panel-header" style="padding: 24px;">
            <div>
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">
                    BUKTI TRANSAKSI PEMBELIAN BARANG (STOCK-IN)
                </div>
                <div style="font-size: 24px; font-weight: 800; color: var(--text-main); margin-top: 4px;">
                    <code>{{ $purchase->purchase_number }}</code>
                </div>
            </div>

            <div style="text-align: right;">
                @if($purchase->isCompleted())
                    <span class="badge badge-success" style="font-size: 14px; padding: 6px 14px;">
                        ✔ COMPLETED (STOK MASUK)
                    </span>
                @else
                    <span class="badge badge-danger" style="font-size: 14px; padding: 6px 14px;">
                        ⛔ CANCELLED (DIBATALKAN)
                    </span>
                @endif
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                    Tanggal: {{ $purchase->purchased_at->format('d F Y, H:i') }}
                </div>
            </div>
        </div>

        <div class="panel-body" style="padding: 24px;">
            <!-- Meta Details -->
            <div class="grid-2" style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
                <div>
                    <h4 style="font-size: 13px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">
                        Informasi Mitra Supplier:
                    </h4>
                    <div style="font-size: 16px; font-weight: 700; color: var(--text-main);">
                        {{ $purchase->supplier->name ?? '-' }}
                    </div>
                    <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                        Telepon: {{ $purchase->supplier->phone ?? '-' }} &bull; Email: {{ $purchase->supplier->email ?? '-' }}
                    </div>
                </div>

                <div>
                    <h4 style="font-size: 13px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">
                        Petugas Penanggung Jawab:
                    </h4>
                    <div style="font-size: 16px; font-weight: 700; color: var(--text-main);">
                        {{ $purchase->user->name ?? '-' }}
                    </div>
                    <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                        Email: {{ $purchase->user->email ?? '-' }} &bull; Role: <strong>{{ strtoupper($purchase->user->role ?? '-') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">Rincian Item Produk Pembelian:</h4>
            <div class="table-responsive" style="margin-bottom: 24px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th>Kode SKU</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th style="text-align: right;">Biaya Satuan (Rp)</th>
                            <th style="text-align: center;">Jumlah Masuk</th>
                            <th style="text-align: right;">Subtotal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><code>{{ $item->product->sku ?? '-' }}</code></td>
                                <td>
                                    @if($item->product)
                                        <a href="{{ route('products.show', $item->product) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                            {{ $item->product->name }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->product->category->name ?? '-' }}</td>
                                <td style="text-align: right;">
                                    Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center;">
                                    <strong style="color: var(--success); font-size: 15px;">+{{ $item->quantity }}</strong>
                                </td>
                                <td style="text-align: right; font-weight: 600;">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8fafc; font-size: 16px;">
                            <td colspan="6" style="text-align: right; font-weight: 700; padding: 16px;">
                                TOTAL KESELURUHAN:
                            </td>
                            <td style="text-align: right; font-weight: 800; color: var(--primary); padding: 16px; font-size: 18px;">
                                Rp {{ number_format($purchase->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Owner Action: Cancellation -->
            @if(auth()->user()->isOwner() && $purchase->isCompleted())
                <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 18px; margin-top: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <div>
                        <div style="font-weight: 700; color: #9f1239; font-size: 14px;">
                            ⚠️ Pembatalan Transaksi Pembelian (Owner Only)
                        </div>
                        <div style="font-size: 13px; color: #be123c; margin-top: 4px;">
                            Membatalkan PO ini akan menarik kembali (mengurangi) stok produk yang telah ditambahkan, selama stok barang tersebut belum terpakai oleh penjualan.
                        </div>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('purchases.cancel', $purchase) }}"
                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi PO ini? Stok barang akan dikurangi kembali.');"
                    >
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            Batalkan Pembelian & Rollback Stok
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
