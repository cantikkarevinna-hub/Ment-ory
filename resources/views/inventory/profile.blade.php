@extends('layouts.app')

@section('title', 'Profil Akun')

@section('content')
<!-- Header Banner Profil Akun -->
<div class="main-banner d-flex justify-content-between align-items-center">
    <h2 class="fw-bold m-0">Profil Akun</h2>
    <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold" style="background-color: rgba(255,255,255,0.2) !important; color: white !important;">
        Status: Aktif
    </span>
</div>

<!-- Profil Card Body -->
<div class="content-card">
    <div class="row align-items-center">
        <!-- Foto Profil & Nama -->
        <div class="col-md-4 text-center border-end py-3">
            <div class="position-relative d-inline-block mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold fs-1 mx-auto" 
                     style="width: 140px; height: 140px; background-color: #557cb8;">
                    CH
                </div>
                <button class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 border border-white" style="width: 36px; height: 36px; padding: 0;">
                    <i class="bi bi-camera-fill"></i>
                </button>
            </div>
            <h4 class="fw-bold m-0 text-dark">Chantikka Revinna</h4>
            <div class="text-muted small mb-2">QC-2026-0808</div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-semibold fs-7">
                QC Inspector / Staff PKL
            </span>
        </div>

        <!-- Detail Pekerjaan & Kontak -->
        <div class="col-md-8 px-4 py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark m-0">Informasi Pekerjaan & Kontak</h5>
                <button class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-pencil me-1"></i> Edit Profil
                </button>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="text-secondary small fw-bold text-uppercase">Divisi / Departemen</div>
                    <div class="fw-bold text-dark fs-6 mt-1">Quality Control (QC)</div>
                </div>
                <div class="col-md-6">
                    <div class="text-secondary small fw-bold text-uppercase">Lokasi Tugas</div>
                    <div class="fw-bold text-dark fs-6 mt-1">Plant 1 - Karawang Barat</div>
                </div>
                <div class="col-md-6">
                    <div class="text-secondary small fw-bold text-uppercase">Email Perusahaan</div>
                    <div class="fw-bold text-dark fs-6 mt-1">chantikka.rev@company.com</div>
                </div>
                <div class="col-md-6">
                    <div class="text-secondary small fw-bold text-uppercase">Nomor Telepon</div>
                    <div class="fw-bold text-primary fs-6 mt-1">+62 85692354292</div>
                </div>
            </div>

            <div class="text-end mt-5">
                <a href="{{ route('inventory.index') }}" class="btn btn-light border rounded-3 px-4 fw-bold">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection