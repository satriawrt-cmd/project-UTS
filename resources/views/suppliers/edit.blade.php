@extends('layouts.app')

@section('title', 'Edit Supplier: ' . $supplier->name)
@section('page-title', 'Edit Data Pemasok')

@section('content')
<div style="max-width: 650px; margin: 0 auto;">
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">✏️ Edit Supplier: {{ $supplier->name }}</span>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali</a>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="name">Nama Perusahaan / Supplier <span style="color: red;">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="{{ old('name', $supplier->name) }}"
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
                        value="{{ old('phone', $supplier->phone) }}"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email Supplier</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        value="{{ old('email', $supplier->email) }}"
                    >
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
