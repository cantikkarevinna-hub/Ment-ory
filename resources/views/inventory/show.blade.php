@extends('layouts.app')

@section('title', 'Detail & Update Barang')

@section('content')
<!-- Style Tambahan untuk Efek Animasi Tombol -->
<style>
    .btn-hover-effect {
        transition: all 0.2s ease;
    }
    .btn-hover-effect:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
        box-shadow: 0 .4rem .8rem rgba(0,0,0,.15) !important;
    }
    .btn-hover-effect:active {
        transform: translateY(0);
        filter: brightness(0.95);
    }
</style>

<!-- Header Banner Utama -->
<div class="main-banner d-flex justify-content-between align-items-center mb-4 p-4 rounded-4" style="background-color: #557cb8;">
    <h2 class="fw-bold m-0 text-white text-decoration-underline" style="text-underline-offset: 8px;">Detail & Update Barang</h2>
    
    <a href="{{ route('inventory.profile') }}" class="btn rounded-pill px-3 py-2 text-white d-flex align-items-center gap-2 border-0 shadow-sm" style="background-color: rgba(255, 255, 255, 0.25);">
        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 30px; height: 30px; font-size: 14px;">
            <i class="bi bi-person-fill"></i>
        </div>
        <span class="fw-semibold small">Chantikka Revinna</span>
    </a>
</div>

<!-- Form Update & Detail Informasi -->
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="p-4 rounded-4 bg-white shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h4 class="fw-bold text-dark m-0">Form Perbaikan / Detail Data</h4>
                    <div class="text-secondary small mt-1">Ubah data di bawah ini jika terdapat kesalahan input, perubahan akan langsung tersimpan secara real-time.</div>
                </div>
                <div>
                    @if($item->status == 'Siap Kirim')
                        <span class="badge px-3 py-2 fw-bold" style="background-color: #dcfce7; color: #15803d !important; border-radius: 6px; font-size: 13px;">Siap Kirim</span>
                    @elseif($item->status == 'Bermasalah')
                        <span class="badge px-3 py-2 fw-bold" style="background-color: #fee2e2; color: #b91c1c !important; border-radius: 6px; font-size: 13px;">Bermasalah</span>
                    @else
                        <span class="badge px-3 py-2 fw-bold" style="background-color: #fef08a; color: #a16207 !important; border-radius: 6px; font-size: 13px;">Belum dicek</span>
                    @endif
                </div>
            </div>

            <!-- Form Update Data -->
            <form id="update-item-form" action="{{ route('inventory.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control rounded-3" value="{{ $item->nama_barang }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold small text-secondary">Nomor Seri</label>
                        <input type="text" name="no_seri" class="form-control rounded-3" value="{{ $item->no_seri }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold small text-secondary">Barcode</label>
                        <input type="text" name="barcode" class="form-control rounded-3" value="{{ $item->barcode }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold small text-secondary">Jumlah Kuantitas</label>
                        <input type="number" name="jumlah" class="form-control rounded-3" min="1" value="{{ $item->jumlah }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold small text-secondary">Tanggal Masuk</label>
                        <input type="date" name="tanggal" class="form-control rounded-3" value="{{ $item->tanggal }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Status Barang</label>
                    <select name="status" class="form-select rounded-3" required>
                        <option value="Belum dicek" {{ $item->status == 'Belum dicek' ? 'selected' : '' }}>Belum dicek</option>
                        <option value="Siap Kirim" {{ $item->status == 'Siap Kirim' ? 'selected' : '' }}>Siap Kirim</option>
                        <option value="Bermasalah" {{ $item->status == 'Bermasalah' ? 'selected' : '' }}>Bermasalah</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold small text-secondary">Keterangan / Deskripsi Barang</label>
                    <textarea name="keterangan" class="form-control rounded-3" rows="3">{{ $item->keterangan }}</textarea>
                </div>

                <!-- Informasi Waktu Pembuatan & Penanggung Jawab Akun Aktif -->
                <div class="p-3 rounded-3 bg-light mb-4 border" style="font-size: 14px;">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary"><i class="bi bi-clock me-1"></i> Waktu & Jam Dibuat:</span>
                        <strong class="text-dark">{{ $item->created_at ? $item->created_at->format('d-m-Y H:i:s') : $item->tanggal . ' 09:47:27 WIB' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary"><i class="bi bi-person-badge me-1"></i> Penanggung Jawab (Akun Aktif):</span>
                        <strong class="text-primary">Chantikka Revinna</strong>
                    </div>
                </div>
            </form>

            <!-- Tombol Aksi di Dalam Halaman Detail (Update & Delete Bersandingan) -->
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('inventory.data') }}" class="btn btn-light rounded-pill px-4 fw-semibold border shadow-sm">
                    &larr; Kembali
                </a>
                <div class="d-flex gap-2">
                    <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pindahkan data ini ke Trash Bin?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm btn-hover-effect">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </form>
                    <button type="submit" form="update-item-form" class="btn text-white rounded-pill px-4 fw-bold shadow-sm btn-hover-effect" style="background-color: #557cb8;">
                        <i class="bi bi-check-circle me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection