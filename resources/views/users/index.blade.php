@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Kelola Pengguna & Hak Akses (Owner Only)')

@section('content')
<div class="panel">
    <div class="panel-header">
        <div>
            <span class="panel-title">👥 Daftar Pengguna Sistem</span>
            <span style="font-size: 13px; color: var(--text-muted); display: block;">Total {{ $users->total() }} akun pengguna terdaftar.</span>
        </div>

        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            + Tambah Pengguna Baru
        </a>
    </div>

    <!-- Search & Role Filter Bar -->
    <div class="panel-body" style="background: #f8fafc; border-bottom: 1px solid var(--border); padding: 14px 24px;">
        <form method="GET" action="{{ route('users.index') }}" style="display: flex; gap: 10px; max-width: 600px; flex-wrap: wrap;">
            <input
                type="text"
                name="search"
                class="form-control"
                style="flex: 2; min-width: 180px;"
                placeholder="Cari nama atau email..."
                value="{{ request('search') }}"
            >
            <select name="role" class="form-control" style="flex: 1; min-width: 130px;">
                <option value="">-- Semua Role --</option>
                <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>👑 Owner</option>
                <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>👷 Staff</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            @if(request()->anyFilled(['search', 'role']))
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>Nama Lengkap</th>
                    <th>Alamat Email</th>
                    <th>Role Akses</th>
                    <th>Tanggal Dibuat</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $users->firstItem() + $loop->index }}</td>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            @if($user->id === auth()->id())
                                <span style="font-size: 11px; background: #e0e7ff; color: #3730a3; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">Akun Anda</span>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="role-pill {{ $user->isOwner() ? 'role-owner' : 'role-staff' }}" style="padding: 3px 8px;">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary btn-sm">Edit</a>
                            @if($user->id !== auth()->id())
                                <form
                                    method="POST"
                                    action="{{ route('users.destroy', $user) }}"
                                    style="display: inline;"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna \'{{ $user->name }}\'?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 35px;">
                            Tidak ada akun pengguna yang sesuai kriteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="panel-body" style="padding: 14px 24px;">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
