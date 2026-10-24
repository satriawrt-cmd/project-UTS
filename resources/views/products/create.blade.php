@extends('layouts.app')

@section('title', 'Tambah Produk Baru')
@section('page-title', 'Tambah Master Produk')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">🏷️ Form Tambah Produk Baru</span>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali</a>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('products.store') }}">
                @csrf

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="sku">Kode SKU (Stock Keeping Unit) <span style="color: red;">*</span></label>
                        <input
                            type="text"
                            name="sku"
                            id="sku"
                            class="form-control"
                            placeholder="Contoh: PRD-ELK-001"
                            value="{{ old('sku') }}"
                            required
                        >
                        <span class="form-help">Harus unik dan menjadi pengenal identitas produk.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category_id">Kategori Produk <span style="color: red;">*</span></label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                        placeholder="Contoh: Kopi Arabika Toraja 250g"
                        value="{{ old('name') }}"
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
                            placeholder="Contoh: 35000"
                            value="{{ old('purchase_price') }}"
                            required
                        >
                        <span class="form-help">Harga modal standar saat membeli dari supplier.</span>
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
                            placeholder="Contoh: 50000"
                            value="{{ old('selling_price') }}"
                            required
                        >
                        <span class="form-help">Harga jual resmi yang digunakan saat transaksi kasir.</span>
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
                            placeholder="Contoh: 10"
                            value="{{ old('minimum_stock', 5) }}"
                            required
                        >
                        <span class="form-help">Sistem akan memberi peringatan jika stok &le; angka ini.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="stock">Stok Awal Sistem</label>
                        <input
                            type="number"
                            min="0"
                            name="stock"
                            id="stock"
                            class="form-control"
                            placeholder="0"
                            value="{{ old('stock', 0) }}"
                        >
                        <span class="form-help">Direkomendasikan mulai dari 0, lalu gunakan Pembelian (Stock-In).</span>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Master Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
