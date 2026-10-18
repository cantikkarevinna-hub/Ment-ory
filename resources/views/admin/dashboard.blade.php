@extends('layouts.app')

@section('title', 'Panel Admin QC')

@section('content')
<style>
    .admin-filter-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .admin-filter-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    }
    .admin-filter-card:active {
        transform: translateY(-1px);
        box-shadow: 0 .25rem .5rem rgba(0,0,0,.1) !important;
    }
    .admin-filter-card.is-active {
        border: 2px solid #557cb8;
    }
    .admin-summary-card {
        min-height: 0;
        padding: 16px 24px;
    }
</style>

<div class="main-banner d-flex justify-content-between align-items-center mb-4 p-4 rounded-4">
    <div>
        <h2 class="fw-bold m-0 text-white text-decoration-underline" style="text-underline-offset: 8px;">Panel Admin QC</h2>
    </div>
    @include('partials.profile-button')
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-reset d-block">
            <div class="content-card admin-summary-card admin-filter-card shadow-sm {{ $roleFilter === null ? 'is-active' : '' }}" @if($roleFilter === null) aria-current="page" @endif>
                <div class="text-secondary small fw-bold">AKUN TERDAFTAR</div>
                <div class="fs-2 fw-bold text-dark">{{ $accountCount }}</div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.dashboard', ['role' => 'admin']) }}" class="text-decoration-none text-reset d-block">
            <div class="content-card admin-summary-card admin-filter-card shadow-sm {{ $roleFilter === 'admin' ? 'is-active' : '' }}" @if($roleFilter === 'admin') aria-current="page" @endif>
                <div class="text-secondary small fw-bold">ADMIN</div>
                <div class="fs-2 fw-bold text-dark">{{ $adminCount }}</div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.dashboard', ['role' => 'member']) }}" class="text-decoration-none text-reset d-block">
            <div class="content-card admin-summary-card admin-filter-card shadow-sm {{ $roleFilter === 'member' ? 'is-active' : '' }}" @if($roleFilter === 'member') aria-current="page" @endif>
                <div class="text-secondary small fw-bold">ANGGOTA</div>
                <div class="fs-2 fw-bold text-dark">{{ $memberCount }}</div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('inventory.jadwal') }}" class="text-decoration-none text-reset d-block">
            <div class="content-card admin-summary-card admin-filter-card shadow-sm">
                <div class="text-secondary small fw-bold">AGENDA QC</div>
                <div class="fs-2 fw-bold text-dark">{{ $eventCount }}</div>
            </div>
        </a>
    </div>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                @if($roleFilter === 'admin')
                    Akun admin
                @elseif($roleFilter === 'member')
                    Akun anggota
                @else
                    Akun yang terdaftar
                @endif
            </h4>
            <p class="text-secondary small mb-0">
                @if($roleFilter === 'admin')
                    Menampilkan akun dengan peran admin.
                @elseif($roleFilter === 'member')
                    Menampilkan akun dengan peran anggota.
                @else
                    Akun anggota dibuat saat mendaftar. Email dipakai sebagai identitas login.
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            @if($roleFilter !== null)
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">Lihat semua akun</a>
            @endif
            <a href="{{ route('inventory.data') }}" class="btn btn-outline-primary btn-sm">Pantau inventaris</a>
            <a href="{{ route('inventory.laporan') }}" class="btn btn-outline-primary btn-sm">Lihat laporan</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr class="text-secondary small">
                    <th>Nama</th>
                    <th>Email login</th>
                    <th>Peran</th>
                    <th>Lokasi tugas</th>
                    <th>Nomor telepon</th>
                    <th>Terdaftar</th>
                    <th>Login terakhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->isAdmin() ? 'text-bg-primary' : 'text-bg-secondary' }}">
                                {{ $user->isAdmin() ? 'Admin' : 'Anggota' }}
                            </span>
                        </td>
                        <td>{{ $user->location ?: 'Belum diisi' }}</td>
                        <td>{{ $user->phone ?: 'Belum diisi' }}</td>
                        <td>{{ $user->created_at?->copy()->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '-' }} WIB</td>
                        <td>{{ $user->last_login_at?->copy()->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') ?? 'Belum pernah login' }}{{ $user->last_login_at ? ' WIB' : '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">Belum ada akun terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>
@endsection
