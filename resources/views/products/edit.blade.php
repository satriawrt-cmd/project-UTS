@extends('layouts.app')

@section('title', 'Edit Produk: ' . $product->name)
@section('page-title', 'Edit Data Master Produk')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">✏️ Edit Produk: {{ $product->name }}</span>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali</a>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('products.update', $product) }}">
                @csrf
                @method('PUT')

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="sku">Kode SKU <span style="color: red;">*</span></label>
                        <input
                            type="text"
                            name="sku"
                            id="sku"
                            class="form-control"
                            value="{{ old('sku', $product->sku) }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category_id">Kategori Produk <span style="color: red;">*</span></label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap Produk <span style="color: red;">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="{{ old('name', $product->name) }}"
                        required
                    >
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="purchase_price">Harga Beli Standar (Rp) <span style="color: red;">*</span></label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="purchase_price"
                            id="purchase_price"
                            class="form-control"
                            value="{{ old('purchase_price', $product->purchase_price) }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="selling_price">Harga Jual Satuan (Rp) <span style="color: red;">*</span></label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="selling_price"
                            id="selling_price"
                            class="form-control"
                            value="{{ old('selling_price', $product->selling_price) }}"
                            required
                        >
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="minimum_stock">Batas Minimum Stok <span style="color: red;">*</span></label>
                        <input
                            type="number"
                            min="0"
                            name="minimum_stock"
                            id="minimum_stock"
                            class="form-control"
                            value="{{ old('minimum_stock', $product->minimum_stock) }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stok Fisik Saat Ini (Sistem)</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $product->stock }} Unit"
                            disabled
                            style="background: #f1f5f9; color: #475569;"
                        >
                        <span class="form-help">Stok berubah otomatis melalui transaksi Pembelian atau Penjualan.</span>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
