@extends('layouts.app')

@section('title', 'Katalog Belanja')
@section('page-title', 'Katalog Belanja Online')

@section('styles')
<style>
    .card-wrapper {
        position: relative;
    }
    .in-cart-ribbon {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #16a34a;
        color: white;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        z-index: 10;
        box-shadow: 0 2px 6px rgba(22,163,74,0.35);
        display: flex;
        align-items: center;
        gap: 4px;
        pointer-events: none;
    }
    .btn-in-cart {
        background: #15803d !important;
        border-color: #15803d !important;
    }
    .btn-in-cart:hover {
        background: #166534 !important;
    }
</style>
@endsection

@section('content')
@php
    $cartItems = session('cart', []);
    $cartProductIds = array_keys($cartItems);
@endphp
<!-- Hero Welcome Banner -->
<div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: white; border-radius: 14px; padding: 30px; margin-bottom: 28px; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);">
    <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 8px;">
        Selamat Datang di Katalog Belanja Online! 🛍️
    </h2>
    <p style="font-size: 14px; color: #dbeafe; max-width: 650px; line-height: 1.6;">
        Temukan berbagai produk berkualitas dari distributor resmi. Jumlah stok terupdate secara *real-time* dari gudang sehingga pesanan Anda dijamin ketersediaannya.
    </p>
</div>

<!-- Filter & Search Bar -->
<div class="panel" style="margin-bottom: 24px;">
    <div class="panel-body" style="padding: 16px 20px;">
        <form method="GET" action="{{ route('shop.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 2; min-width: 220px;">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Cari produk berdasarkan nama atau SKU..."
                    value="{{ request('search') }}"
                >
            </div>

            <div style="flex: 1; min-width: 180px;">
                <select name="category_id" class="form-control">
                    <option value="">-- Semua Kategori Produk --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Filter</button>
            @if(request()->anyFilled(['search', 'category_id']))
                <a href="{{ route('shop.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Product Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; margin-bottom: 28px;">
    @forelse($products as $product)
        @php
            $inCart = in_array($product->id, $cartProductIds);
            $cartQty = $inCart ? ($cartItems[$product->id]['quantity'] ?? 0) : 0;
        @endphp
        <div class="card-wrapper" style="background: white; border-radius: 12px; border: 2px solid {{ $inCart ? '#16a34a' : 'var(--border)' }}; overflow: hidden; display: flex; flex-direction: column; box-shadow: {{ $inCart ? '0 2px 12px rgba(22,163,74,0.15)' : '0 1px 3px rgba(0,0,0,0.04)' }}; transition: transform 0.15s, box-shadow 0.15s;">

            {{-- In-cart ribbon badge --}}
            @if($inCart)
                <div class="in-cart-ribbon">
                    ✓ Di Keranjang ({{ $cartQty }})
                </div>
            @endif

            <!-- Top Product Badge -->
            <div style="padding: 14px 16px; background: {{ $inCart ? '#f0fdf4' : '#f8fafc' }}; border-bottom: 1px solid {{ $inCart ? '#bbf7d0' : 'var(--border)' }}; display: flex; justify-content: space-between; align-items: center;">
                <span class="badge badge-info" style="font-size: 11px;">
                    {{ $product->category->name ?? 'Kategori' }}
                </span>
                <code style="font-size: 11px; color: var(--text-muted);">{{ $product->sku }}</code>
            </div>

            <!-- Body -->
            <div style="padding: 18px 16px; flex: 1; display: flex; flex-direction: column;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 8px; line-height: 1.3;">
                    {{ $product->name }}
                </h3>

                <div style="margin-top: auto; padding-top: 12px;">
                    <div style="display: flex; align-items: baseline; gap: 4px; margin-bottom: 8px;">
                        <span style="font-size: 20px; font-weight: 800; color: var(--primary);">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </span>
                        <span style="font-size: 12px; color: var(--text-muted);">/ unit</span>
                    </div>

                    <div style="font-size: 12px; margin-bottom: 14px;">
                        @if($product->stock > 0)
                            <span style="color: var(--success); font-weight: 600;">
                                ✔ Tersedia {{ $product->stock }} unit di gudang
                            </span>
                        @else
                            <span style="color: var(--danger); font-weight: 700;">
                                ✖ Stok Habis
                            </span>
                        @endif
                    </div>

                    <!-- Add to Cart Form -->
                    @if($product->stock > 0)
                        <form method="POST" action="{{ route('shop.cart.add', $product) }}">
                            @csrf
                            <div style="display: flex; gap: 8px;">
                                <input
                                    type="number"
                                    name="quantity"
                                    value="1"
                                    min="1"
                                    max="{{ $product->stock }}"
                                    class="form-control"
                                    style="width: 70px; text-align: center; padding: 8px;"
                                    title="Jumlah beli"
                                >
                                @if($inCart)
                                    <button type="submit" class="btn btn-primary btn-in-cart" style="flex: 1; padding: 8px;">
                                        ✓ Tambah Lagi
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 8px;">
                                        + Keranjang
                                    </button>
                                @endif
                            </div>
                        </form>
                    @else
                        <button type="button" class="btn btn-secondary" style="width: 100%; opacity: 0.6; cursor: not-allowed;" disabled>
                            Stok Habis
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; background: white; padding: 40px; text-align: center; border-radius: 12px; border: 1px solid var(--border); color: var(--text-muted);">
            <div style="font-size: 32px; margin-bottom: 8px;">🔍</div>
            <p style="font-size: 15px; font-weight: 600;">Tidak ada produk yang cocok dengan pencarian Anda.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-secondary btn-sm" style="margin-top: 12px;">Lihat Semua Produk</a>
        </div>
    @endforelse
</div>

@if($products->hasPages())
    <div style="margin-bottom: 30px;">
        {{ $products->links() }}
    </div>
@endif
@endsection
