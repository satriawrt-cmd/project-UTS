@extends('layouts.app')

@section('title', 'Edit Pengguna: ' . $user->name)
@section('page-title', 'Edit Akun Pengguna')

@section('content')
<div style="max-width: 650px; margin: 0 auto;">
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">✏️ Edit Data Pengguna: {{ $user->name }}</span>
            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali</a>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span style="color: red;">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="{{ old('name', $user->name) }}"
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
                        value="{{ old('email', $user->email) }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Hak Akses (Role) <span style="color: red;">*</span></label>
                    <select name="role" id="role" class="form-control" required {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>🛍️ Pelanggan / User (Belanja di Toko Online)</option>
                        <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>👷 Staff (Transaksi Beli & Jual Kasir, Lihat Produk)</option>
                        <option value="owner" {{ old('role', $user->role) === 'owner' ? 'selected' : '' }}>👑 Owner / Admin (Akses Penuh)</option>
                    </select>

                    @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <span class="form-help">Anda tidak dapat mengubah role akun Anda sendiri saat sedang login.</span>
                    @endif
                </div>

                <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 8px; padding: 14px; margin: 20px 0;">
                    <div style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Ganti Password (Opsional)
                    </div>
                    <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">
                        Biarkan kedua field password berikut kosong jika tidak ingin mengubah password lama.
                    </p>

                    <div class="grid-2">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="password">Password Baru</label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                placeholder="Kosongkan jika tetap"
                            >
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="password_confirmation">Ulangi Password Baru</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control"
                                placeholder="Kosongkan jika tetap"
                            >
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
