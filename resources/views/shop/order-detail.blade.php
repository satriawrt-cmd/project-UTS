@extends('layouts.app')

@section('title', 'Struk Pesanan: ' . $sale->sale_number)
@section('page-title', 'Detail Pesanan: ' . $sale->sale_number)

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <a href="{{ route('my-orders.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Riwayat Pesanan</a>

        <div style="display: flex; gap: 8px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">
                🖨️ Cetak Struk
            </button>
            <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm">
                + Belanja Lagi
            </a>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header" style="padding: 24px;">
            <div>
                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">
                    BUKTI PEMBELIAN RESMI KONSUMEN
                </div>
                <div style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-top: 4px;">
                    <code>{{ $sale->sale_number }}</code>
                </div>
            </div>

            <div style="text-align: right;">
                @if($sale->isPendingCancel())
                    <span class="badge badge-warning" style="font-size: 13px; padding: 6px 14px; background: #fef3c7; color: #92400e;">
                        ⏳ MENUNGGU KONFIRMASI PEMBATALAN
                    </span>
                @elseif($sale->isCompleted())
                    <span class="badge badge-success" style="font-size: 13px; padding: 6px 14px;">
                        ✔ PESANAN SELESAI
                    </span>
                @else
                    <span class="badge badge-danger" style="font-size: 13px; padding: 6px 14px;">
                        ⛔ DIBATALKAN
                    </span>
                @endif
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                    Waktu: {{ $sale->sold_at->format('d F Y, H:i') }}
                </div>
            </div>
        </div>

        <div class="panel-body" style="padding: 24px;">
            @if($sale->isPendingCancel())
                <div style="background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; padding: 16px 20px; border-radius: 8px; margin-bottom: 24px;">
                    <div style="font-weight: 700; color: #92400e; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                        <span>⏳</span> Permintaan Pembatalan Sedang Ditinjau
                    </div>
                    <div style="font-size: 14px; color: #78350f; margin-top: 6px;">
                        Anda telah mengajukan pembatalan untuk pesanan ini. Mohon tunggu Owner toko mengonfirmasi pembatalan.
                    </div>
                    @if($sale->cancellation_reason)
                        <div style="margin-top: 8px; font-size: 13px; color: #92400e; background: #fef3c7; padding: 8px 12px; border-radius: 6px;">
                            <strong>Alasan yang Diajukan:</strong> "{{ $sale->cancellation_reason }}"
                        </div>
                    @endif
                </div>
            @elseif($sale->isCancelled())
                <div style="background: #fee2e2; border: 1px solid #fecaca; border-left: 4px solid #dc2626; padding: 16px 20px; border-radius: 8px; margin-bottom: 24px;">
                    <div style="font-weight: 700; color: #991b1b; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                        <span>⛔</span> Pesanan Telah Dibatalkan
                    </div>
                    <div style="font-size: 14px; color: #b91c1c; margin-top: 4px;">
                        Pesanan ini telah resmi dibatalkan dan barang telah dikembalikan ke stok gudang.
                    </div>
                    @if($sale->cancellation_reason)
                        <div style="margin-top: 8px; font-size: 13px; color: #7f1d1d; background: #fecdd3; padding: 8px 12px; border-radius: 6px;">
                            <strong>Keterangan / Alasan:</strong> "{{ $sale->cancellation_reason }}"
                        </div>
                    @endif
                </div>
            @endif

            <div style="margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
                <div style="font-size: 13px; color: var(--text-muted);">Nama Pemesan:</div>
                <div style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-top: 2px;">
                    {{ $sale->user->name ?? 'Pelanggan' }}
                </div>
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                    Email: {{ $sale->user->email ?? '-' }}
                </div>
            </div>

            <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">Rincian Barang yang Diterima:</h4>
            <div class="table-responsive" style="margin-bottom: 24px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th style="text-align: right;">Harga Satuan</th>
                            <th style="text-align: center;">Jumlah Qty</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $item->product->name ?? 'Produk' }}</strong>
                                    <div style="font-size: 11px; color: var(--text-muted);">
                                        SKU: {{ $item->product->sku ?? '-' }}
                                    </div>
                                </td>
                                <td>{{ $item->product->category->name ?? '-' }}</td>
                                <td style="text-align: right;">
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center; font-weight: 700;">
                                    {{ $item->quantity }}x
                                </td>
                                <td style="text-align: right; font-weight: 700;">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8fafc;">
                            <td colspan="5" style="text-align: right; font-weight: 700; padding: 16px; font-size: 15px;">
                                TOTAL AKHIR:
                            </td>
                            <td style="text-align: right; font-weight: 800; color: var(--success); padding: 16px; font-size: 18px;">
                                Rp {{ number_format($sale->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($sale->isCompleted() && $sale->user_id === auth()->id())
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <div style="font-weight: 700; color: #1e293b; font-size: 15px;">
                                ❓ Ingin Membatalkan Pesanan Ini?
                            </div>
                            <div style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                                Permintaan pembatalan akan diteruskan ke Owner toko untuk diverifikasi dan dikonfirmasi.
                            </div>
                        </div>
                        <button
                            type="button"
                            class="btn btn-secondary btn-sm"
                            onclick="var c = document.getElementById('cancelFormContainer'); c.style.display = (c.style.display === 'none' ? 'block' : 'none');"
                            style="border-color: #fca5a5; color: #b91c1c; font-weight: 600;"
                        >
                            Ajukan Pembatalan Pesanan
                        </button>
                    </div>

                    <div id="cancelFormContainer" style="display: none; margin-top: 18px; padding-top: 16px; border-top: 1px dashed #cbd5e1;">
                        <form method="POST" action="{{ route('my-orders.cancel', $sale) }}">
                            @csrf
                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                                    Alasan Pengajuan Pembatalan <span style="color: var(--danger);">*</span>
                                </label>
                                <textarea
                                    name="reason"
                                    rows="3"
                                    class="form-control"
                                    placeholder="Tuliskan alasan pembatalan Anda (misal: salah varian/jumlah, perubahan alamat pengiriman)..."
                                    required
                                    style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 14px;"
                                ></textarea>
                                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                                    Permohonan Anda akan ditinjau langsung oleh Owner toko sebelum disetujui.
                                </div>
                            </div>

                            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                <button
                                    type="button"
                                    class="btn btn-secondary btn-sm"
                                    onclick="document.getElementById('cancelFormContainer').style.display = 'none';"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Apakah Anda yakin ingin mengirim permohonan pembatalan pesanan ini ke Owner toko?');"
                                >
                                    Kirim Permohonan Pembatalan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div style="background: #f8fafc; border: 1px dashed var(--border); border-radius: 8px; padding: 14px; text-align: center; font-size: 13px; color: var(--text-muted);">
                Terima kasih telah berbelanja di Toko Distribusi Online. Simpan struk ini sebagai bukti transaksi resmi Anda.
            </div>
        </div>
    </div>
</div>
@endsection
