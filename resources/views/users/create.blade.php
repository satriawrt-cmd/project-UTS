@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')
@section('page-title', 'Daftarkan Pengguna Sistem')

@section('content')
<div style="max-width: 650px; margin: 0 auto;">
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">👥 Form Pendaftaran Akun Pengguna</span>
            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali</a>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span style="color: red;">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        placeholder="Contoh: Budi Santoso"
                        value="{{ old('name') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email Login <span style="color: red;">*</span></label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        placeholder="Contoh: budi@distribusi.com"
                        value="{{ old('email') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Hak Akses (Role) <span style="color: red;">*</span></label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>🛍️ Pelanggan / User (Belanja di Toko Online)</option>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>👷 Staff (Transaksi Beli & Jual Kasir, Lihat Produk)</option>
                        <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>👑 Owner / Admin (Akses Penuh Master Data, Role, Batalkan Transaksi)</option>
                    </select>

                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="password">Password <span style="color: red;">*</span></label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="Minimal 8 karakter"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password <span style="color: red;">*</span></label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="form-control"
                            placeholder="Ketik ulang password"
                            required
                        >
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Daftarkan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
