@extends('layouts.app')

@section('title', 'Profil Akun')

@section('content')
<div class="main-banner d-flex justify-content-between align-items-center">
    <h2 class="fw-bold m-0">Profil Akun</h2>
    <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold" style="background-color: rgba(255,255,255,0.2) !important; color: white !important;">
        {{ $profile->isAdmin() ? 'Administrator' : 'Anggota' }}
    </span>
</div>

<div class="content-card">
    <div class="row align-items-center">
        <div class="col-md-4 text-center border-end py-3">
            <div class="position-relative d-inline-block mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold fs-1 mx-auto overflow-hidden"
                     style="width: 140px; height: 140px; background-color: #557cb8;">
                    @if($profile->photo_path)
                        <img id="profile-photo-preview" src="{{ asset('storage/'.$profile->photo_path) }}" alt="Foto {{ $profile->name }}" class="w-100 h-100" style="object-fit: cover;">
                    @else
                        <span id="profile-initials">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($profile->name, 0, 2)) }}</span>
                        <img id="profile-photo-preview" src="" alt="Pratinjau foto profil" class="w-100 h-100 d-none" style="object-fit: cover;">
                    @endif
                </div>
                <button type="button" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 border border-white"
                        style="width: 36px; height: 36px; padding: 0;" aria-label="Ganti foto profil"
                        onclick="document.getElementById('profile-photo').click()">
                    <i class="bi bi-camera-fill"></i>
                </button>
                <form id="profile-photo-form" action="{{ route('inventory.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="file" name="photo" id="profile-photo" class="d-none" accept="image/jpeg,image/png,image/webp">
                </form>
            </div>
            <h4 class="fw-bold m-0 text-dark">{{ $profile->name }}</h4>
            <div class="text-muted small mb-2">{{ $profile->email }}</div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-semibold">
                {{ $profile->isAdmin() ? 'Administrator' : 'Anggota' }}
            </span>
        </div>

        <div class="col-md-8 px-4 py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark m-0">Informasi Profil</h5>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3"
                        data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="bi bi-pencil me-1"></i> Edit Profil
                </button>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="text-secondary small fw-bold text-uppercase">Nama</div>
                    <div class="fw-bold text-dark fs-6 mt-1">{{ $profile->name }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-secondary small fw-bold text-uppercase">Lokasi Tugas</div>
                    <div class="fw-bold text-dark fs-6 mt-1">{{ $profile->location ?: 'Belum diisi' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-secondary small fw-bold text-uppercase">Email Login (tidak dapat diubah)</div>
                    <div class="fw-bold text-dark fs-6 mt-1">{{ $profile->email }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-secondary small fw-bold text-uppercase">Nomor Telepon</div>
                    <div class="fw-bold text-primary fs-6 mt-1">{{ $profile->phone ?: 'Belum diisi' }}</div>
                </div>
            </div>

            <div class="text-end mt-5">
                <a href="{{ $profile->isAdmin() ? route('admin.dashboard') : route('inventory.index') }}" class="btn btn-light border rounded-3 px-4 fw-bold">
                    {{ $profile->isAdmin() ? 'Kembali ke Admin' : 'Kembali ke Dashboard' }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('inventory.profile.update') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editProfileModalLabel">Edit Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="mb-3">
                    <label for="profile-name-input" class="form-label">Nama</label>
                    <input id="profile-name-input" type="text" name="name" class="form-control" value="{{ old('name', $profile->name) }}" required>
                </div>
                <div class="mb-3">
                    <label for="profile-location-input" class="form-label">Lokasi Tugas</label>
                    <input id="profile-location-input" type="text" name="location" class="form-control" value="{{ old('location', $profile->location) }}">
                </div>
                <div class="mb-3">
                    <label for="profile-phone-input" class="form-label">Nomor Telepon</label>
                    <input id="profile-phone-input" type="tel" name="phone" class="form-control" value="{{ old('phone', $profile->phone) }}">
                </div>
                <div class="small text-muted">Email mengikuti akun login dan tidak dapat diubah dari halaman profil.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #557cb8; border-color: #557cb8;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('profile-photo').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (!file) {
            return;
        }

        const preview = document.getElementById('profile-photo-preview');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('d-none');

        const initials = document.getElementById('profile-initials');
        if (initials) {
            initials.classList.add('d-none');
        }

        document.getElementById('profile-photo-form').submit();
    });
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editProfileModal')).show();
        });
    @endif
</script>
@endsection
