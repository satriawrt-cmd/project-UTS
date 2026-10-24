@extends('layouts.app')

@section('title', 'Keranjang Belanja')
@section('page-title', 'Keranjang Belanja')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 22px; font-weight: 800;">🛒 Keranjang Belanja Anda</h2>
    <a href="{{ route('shop.index') }}" class="btn btn-secondary btn-sm">&larr; Lanjut Pilih Barang</a>
</div>

@if(empty($cart))
    <div class="panel" style="text-align: center; padding: 60px 20px;">
        <div style="font-size: 48px; margin-bottom: 12px;">🛍️</div>
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Keranjang belanja Anda masih kosong</h3>
        <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">
            Jelajahi katalog dan tambahkan produk yang ingin Anda beli.
        </p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary">
            Lihat Katalog Belanja Sekarang &rarr;
        </a>
    </div>
@else
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        <!-- Cart Items Table -->
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Daftar Item dalam Keranjang</span>
                <span style="font-size: 13px; color: var(--text-muted);">{{ count($cart) }} macam produk</span>
            </div>

            <form method="POST" action="{{ route('shop.cart.update') }}">
                @csrf
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th style="text-align: right;">Harga Satuan</th>
                                <th style="width: 110px; text-align: center;">Jumlah Qty</th>
                                <th style="text-align: right;">Subtotal</th>
                                <th style="width: 50px; text-align: center;">Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item['name'] }}</strong>
                                        <div style="font-size: 11px; color: var(--text-muted);">
                                            SKU: {{ $item['sku'] }} &bull; Maks: {{ $item['stock'] }} unit
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        <input
                                            type="number"
                                            name="quantities[{{ $item['id'] }}]"
                                            value="{{ $item['quantity'] }}"
                                            min="1"
                                            max="{{ $item['stock'] }}"
                                            class="form-control"
                                            style="padding: 6px; text-align: center;"
                                        >
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: var(--text-main);">
                                        Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        <button
                                            type="submit"
                                            formaction="{{ route('shop.cart.remove', $item['id']) }}"
                                            formmethod="POST"
                                            class="btn btn-danger btn-sm"
                                            style="padding: 4px 8px;"
                                            title="Hapus item"
                                        >
                                            &times;
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="panel-body" style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border-top: 1px solid var(--border); padding: 14px 20px;">
                    <a href="{{ route('shop.index') }}" style="color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 600;">
                        + Tambah Produk Lain
                    </a>
                    <button type="submit" class="btn btn-secondary btn-sm">
                        🔄 Perbarui Kuantitas Keranjang
                    </button>
                </div>
            </form>
        </div>

        <!-- Checkout Summary Card -->
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Ringkasan Pesanan</span>
            </div>

            <div class="panel-body">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--text-muted);">
                    <span>Total Item Belanja:</span>
                    <strong>{{ array_sum(array_column($cart, 'quantity')) }} Unit</strong>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 14px; color: var(--text-muted);">
                    <span>Status Ketersediaan:</span>
                    <span style="color: var(--success); font-weight: 600;">Semua Siap Kirim</span>
                </div>

                <div style="border-top: 1px dashed var(--border); padding-top: 16px; margin-top: 16px; display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 24px;">
                    <span style="font-size: 16px; font-weight: 700;">Total Pembayaran:</span>
                    <span style="font-size: 24px; font-weight: 800; color: var(--primary);">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>

                <a href="{{ route('shop.checkout') }}" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px; font-weight: 700;">
                    Lanjut ke Checkout &rarr;
                </a>

                <div style="font-size: 12px; color: var(--text-muted); text-align: center; margin-top: 12px; line-height: 1.4;">
                    🔒 Pesanan Anda langsung mengurangi stok gudang secara otomatis setelah dikonfirmasi.
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
