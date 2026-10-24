@extends('layouts.app')

@section('title', 'Konfirmasi Pesanan & Checkout')
@section('page-title', 'Konfirmasi Pesanan & Checkout')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 22px; font-weight: 800;">💳 Konfirmasi Checkout Pesanan</h2>
        <a href="{{ route('shop.cart') }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Keranjang</a>
    </div>

    <form method="POST" action="{{ route('shop.checkout.process') }}">
        @csrf

        <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 24px; align-items: start;">
            <!-- Rincian Item Pesanan -->
            <div class="panel">
                <div class="panel-header">
                    <span class="panel-title">📦 Rincian Barang yang Dipesan</span>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: right;">Harga</th>
                                <th style="text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item['name'] }}</strong>
                                        <div style="font-size: 11px; color: var(--text-muted);">{{ $item['sku'] }}</div>
                                    </td>
                                    <td style="text-align: center;">{{ $item['quantity'] }}x</td>
                                    <td style="text-align: right;">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: 700;">
                                        Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background: #f8fafc; font-size: 15px;">
                                <td colspan="3" style="text-align: right; font-weight: 700; padding: 14px 16px;">
                                    Total Belanja:
                                </td>
                                <td style="text-align: right; font-weight: 800; color: var(--primary); padding: 14px 16px;">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Identitas Pembeli & Konfirmasi Form -->
            <div class="panel">
                <div class="panel-header">
                    <span class="panel-title">👤 Informasi Pembeli</span>
                </div>

                <div class="panel-body">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Nama Penerima</label>
                        <div style="font-size: 15px; font-weight: 600; color: var(--text-main); margin-top: 3px;">
                            {{ auth()->user()->name }}
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Email Akun</label>
                        <div style="font-size: 14px; color: var(--text-main); margin-top: 3px;">
                            {{ auth()->user()->email }}
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Catatan Pengiriman / Alamat (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Kirim ke Jl. Merdeka No. 12, atau titip di pos security."></textarea>
                    </div>

                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px; margin-bottom: 20px;">
                        <div style="font-size: 12px; font-weight: 700; color: #166534;">Metode Pembayaran:</div>
                        <div style="font-size: 14px; color: #15803d; font-weight: 600; margin-top: 2px;">
                            ✔ Pembayaran Langsung / COD / Transfer
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <span style="font-weight: 700; font-size: 15px;">TOTAL TAGIHAN:</span>
                            <span style="font-weight: 800; font-size: 24px; color: var(--primary);">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success" style="width: 100%; padding: 13px; font-size: 16px; font-weight: 800;">
                        ✔ Konfirmasi & Selesaikan Pesanan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
