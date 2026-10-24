@extends('layouts.app')

@section('title', 'Tambah Supplier Baru')
@section('page-title', 'Tambah Mitra Pemasok')

@section('content')
<div style="max-width: 650px; margin: 0 auto;">
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">🏭 Form Tambah Supplier</span>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali</a>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('suppliers.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Nama Perusahaan / Supplier <span style="color: red;">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        placeholder="Contoh: PT Sumber Makmur Mandiri"
                        value="{{ old('name') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Nomor Telepon / WhatsApp</label>
                    <input
                        type="text"
                        name="phone"
                        id="phone"
                        class="form-control"
                        placeholder="Contoh: 081234567890"
                        value="{{ old('phone') }}"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email Supplier</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        placeholder="Contoh: sales@sumbermakmur.com"
                        value="{{ old('email') }}"
                    >
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
