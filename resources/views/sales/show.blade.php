@extends('layouts.app')

@section('title', 'Struk Penjualan: ' . $sale->sale_number)
@section('page-title', 'Detail Struk Transaksi Penjualan')

@section('content')
<div style="max-width: 950px; margin: 0 auto;">
    <div style="display: flex; gap: 12px; margin-bottom: 20px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
        <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Riwayat Penjualan</a>

        <div style="display: flex; gap: 8px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">
                🖨️ Cetak Struk Penjualan
            </button>
            <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
                + Buat Penjualan Baru
            </a>
        </div>
    </div>

    <div class="panel">
        <!-- Invoice Header -->
        <div class="panel-header" style="padding: 24px;">
            <div>
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">
                    STRUK RESMI TRANSAKSI PENJUALAN (STOCK-OUT)
                </div>
                <div style="font-size: 24px; font-weight: 800; color: var(--text-main); margin-top: 4px;">
                    <code>{{ $sale->sale_number }}</code>
                </div>
            </div>

            <div style="text-align: right;">
                @if($sale->isPendingCancel())
                    <span class="badge badge-warning" style="font-size: 14px; padding: 6px 14px; background: #fef3c7; color: #92400e;">
                        ⏳ MENUNGGU KONFIRMASI OWNER
                    </span>
                @elseif($sale->isCompleted())
                    <span class="badge badge-success" style="font-size: 14px; padding: 6px 14px;">
                        ✔ COMPLETED (TERJUAL)
                    </span>
                @else
                    <span class="badge badge-danger" style="font-size: 14px; padding: 6px 14px;">
                        ⛔ CANCELLED (DIBATALKAN)
                    </span>
                @endif
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                    Waktu Transaksi: {{ $sale->sold_at->format('d F Y, H:i') }}
                </div>
            </div>
        </div>

        <div class="panel-body" style="padding: 24px;">
            @if($sale->cancellation_reason)
                <div style="background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px;">
                    <div style="font-size: 12px; font-weight: 700; color: #92400e; text-transform: uppercase;">
                        📌 Catatan / Alasan Pembatalan:
                    </div>
                    <div style="font-size: 14px; color: #78350f; margin-top: 4px;">
                        "{{ $sale->cancellation_reason }}"
                    </div>
                </div>
            @endif

            <!-- Meta Details -->
            <div class="grid-2" style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
                <div>
                    <h4 style="font-size: 13px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">
                        Informasi Kasir / Pembeli:
                    </h4>
                    <div style="font-size: 16px; font-weight: 700; color: var(--text-main);">
                        {{ $sale->user->name ?? '-' }}
                    </div>
                    <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                        Email: {{ $sale->user->email ?? '-' }} &bull; Role: <strong>{{ strtoupper($sale->user->role ?? '-') }}</strong>
                    </div>
                </div>

                <div>
                    <h4 style="font-size: 13px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">
                        Status Stok Sistem:
                    </h4>
                    <div style="font-size: 14px; color: var(--text-main);">
                        @if($sale->isPendingCancel())
                            <span style="color: #b45309; font-weight: 600;">
                                ⚠️ Pembeli mengajukan pembatalan. Stok masih dipotong hingga Owner menyetujui pembatalan.
                            </span>
                        @elseif($sale->isCompleted())
                            <span style="color: var(--success); font-weight: 600;">
                                ● Stok fisik barang sudah dipotong dari sistem secara konsisten.
                            </span>
                        @else
                            <span style="color: var(--danger); font-weight: 600;">
                                ● Transaksi dibatalkan; kuantitas barang telah dikembalikan ke stok fisik.
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">Rincian Barang yang Dijual:</h4>
            <div class="table-responsive" style="margin-bottom: 24px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th>Kode SKU</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th style="text-align: right;">Harga Satuan (Rp)</th>
                            <th style="text-align: center;">Jumlah Terjual</th>
                            <th style="text-align: right;">Subtotal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
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
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center;">
                                    <strong style="color: var(--danger); font-size: 15px;">-{{ $item->quantity }}</strong>
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
                                TOTAL PENJUALAN:
                            </td>
                            <td style="text-align: right; font-weight: 800; color: var(--success); padding: 16px; font-size: 18px;">
                                Rp {{ number_format($sale->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Pending Cancel Review Section -->
            @if($sale->isPendingCancel())
                <div style="background: #fffbeb; border: 2px solid #f59e0b; border-radius: 10px; padding: 22px; margin-top: 24px;">
                    <div style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 16px;">
                        <span style="font-size: 26px;">⚠️</span>
                        <div>
                            <div style="font-weight: 800; color: #92400e; font-size: 16px;">
                                Permintaan Pembatalan Pesanan dari Pembeli Menunggu Persetujuan
                            </div>
                            <div style="font-size: 14px; color: #78350f; margin-top: 4px;">
                                Pembeli mengajukan permohonan pembatalan dengan alasan: <strong>"{{ $sale->cancellation_reason }}"</strong>.
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->isOwner())
                        <div style="display: flex; gap: 16px; flex-wrap: wrap; align-items: center; justify-content: flex-end; padding-top: 14px; border-top: 1px dashed #fde68a;">
                            <!-- Form Tolak Pembatalan -->
                            <form method="POST" action="{{ route('sales.reject-cancel', $sale) }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                @csrf
                                <input
                                    type="text"
                                    name="reject_note"
                                    placeholder="Alasan penolakan (opsional)..."
                                    class="form-control"
                                    style="width: 220px; font-size: 13px; padding: 7px 10px;"
                                >
                                <button
                                    type="submit"
                                    class="btn btn-secondary btn-sm"
                                    style="background: #fee2e2; border-color: #fca5a5; color: #991b1b; padding: 8px 14px;"
                                    onclick="return confirm('Apakah Anda yakin ingin MENOLAK pembatalan ini? Transaksi akan tetap berstatus COMPLETED.');"
                                >
                                    ✖ Tolak Pembatalan
                                </button>
                            </form>

                            <!-- Form Setujui Pembatalan -->
                            <form
                                method="POST"
                                action="{{ route('sales.approve-cancel', $sale) }}"
                                onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI pembatalan pesanan ini? Stok seluruh produk akan otomatis dikembalikan ke gudang.');"
                            >
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm" style="background: #16a34a; border-color: #15803d; padding: 8px 18px; font-weight: 700;">
                                    ✔ Setujui Pembatalan & Pulihkan Stok
                                </button>
                            </form>
                        </div>
                    @else
                        <div style="font-size: 13px; color: #92400e; font-style: italic; background: #fef3c7; padding: 8px 12px; border-radius: 6px;">
                            ℹ️ Keputusan persetujuan atau penolakan pembatalan hanya dapat dilakukan oleh Owner toko.
                        </div>
                    @endif
                </div>
            @endif

            <!-- Owner Direct Cancellation for Regular Completed Sales -->
            @if(auth()->user()->isOwner() && $sale->isCompleted())
                <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 18px; margin-top: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <div>
                        <div style="font-weight: 700; color: #9f1239; font-size: 14px;">
                            ⚠️ Pembatalan Transaksi Penjualan Langsung (Owner Only)
                        </div>
                        <div style="font-size: 13px; color: #be123c; margin-top: 4px;">
                            Membatalkan penjualan ini akan mengembalikan seluruh kuantitas produk kembali ke dalam stok gudang secara otomatis.
                        </div>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('sales.cancel', $sale) }}"
                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi penjualan ini? Stok barang akan dikembalikan ke sistem.');"
                    >
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            Batalkan Penjualan & Pulihkan Stok
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
