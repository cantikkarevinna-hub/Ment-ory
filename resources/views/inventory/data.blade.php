@extends('layouts.app')

@section('title', 'Data Inventaris')

@section('content')
<!-- Style Tambahan untuk Efek Animasi Tombol / Hover -->
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
    .clickable-row > td {
        transition: background-color 0.18s ease;
    }
    .clickable-row:hover > td {
        background-color: #eef0f2 !important;
    }
</style>

<!-- Header Banner Utama -->
<div class="main-banner d-flex justify-content-between align-items-center mb-4 p-4 rounded-4" style="background-color: #557cb8;">
    <h2 class="fw-bold m-0 text-white text-decoration-underline" style="text-underline-offset: 8px;">Inventaris System</h2>
    
    <a href="{{ route('inventory.profile') }}" class="btn rounded-pill px-3 py-2 text-white d-flex align-items-center gap-2 border-0 shadow-sm" style="background-color: rgba(255, 255, 255, 0.25);">
        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 30px; height: 30px; font-size: 14px;">
            <i class="bi bi-person-fill"></i>
        </div>
        <span class="fw-semibold small">Chantikka Revinna</span>
    </a>
</div>

<!-- 1. Controls Bar (Top Control) -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <!-- Searchbar Diperpanjang -->
    <form action="{{ route('inventory.data') }}" method="GET" class="position-relative">
        <input type="text" name="search" class="form-control rounded-pill px-3 py-2 pe-4 text-dark bg-white shadow-sm border" placeholder="Cari barang atau NO Seri..." value="{{ request('search') }}" style="width: 420px; font-size: 14px; border-color: #d1d5db !important;">
        <i class="bi bi-search position-absolute end-0 top-50 translate-middle-y me-3 text-muted fs-6"></i>
    </form>

    <!-- Filter Status dengan Biru Khusus (#0284c7) -->
    <div class="d-flex gap-3 align-items-center">
        <a href="{{ route('inventory.data') }}" class="btn btn-sm px-4 py-2 rounded-pill fw-semibold shadow-sm {{ !request('status') ? 'text-white' : 'btn-light text-dark border' }}" style="font-size: 14px; {{ !request('status') ? 'background-color: #0284c7; border: none;' : '' }}">Semua</a>
        <a href="{{ route('inventory.data', ['status' => 'Belum dicek']) }}" class="btn btn-sm px-4 py-2 rounded-pill fw-semibold shadow-sm {{ request('status') == 'Belum dicek' ? 'text-white' : 'btn-light text-dark border' }}" style="font-size: 14px; {{ request('status') == 'Belum dicek' ? 'background-color: #0284c7; border: none;' : '' }}">Belum di Cek</a>
        <a href="{{ route('inventory.data', ['status' => 'Bermasalah']) }}" class="btn btn-sm px-4 py-2 rounded-pill fw-semibold shadow-sm {{ request('status') == 'Bermasalah' ? 'text-white' : 'btn-light text-dark border' }}" style="font-size: 14px; {{ request('status') == 'Bermasalah' ? 'background-color: #0284c7; border: none;' : '' }}">Bermasalah</a>
        <a href="{{ route('inventory.data', ['status' => 'Siap Kirim']) }}" class="btn btn-sm px-4 py-2 rounded-pill fw-semibold shadow-sm {{ request('status') == 'Siap Kirim' ? 'text-white' : 'btn-light text-dark border' }}" style="font-size: 14px; {{ request('status') == 'Siap Kirim' ? 'background-color: #0284c7; border: none;' : '' }}">Siap Kirim</a>
    </div>

    <!-- Tombol + Tambah dengan Pemicu JS Manual -->
    <button type="button" class="btn px-4 py-2 fw-bold text-white border-0 shadow-sm btn-hover-effect" onclick="bukaModalTambah()" style="background-color: #557cb8; border-radius: 12px; font-size: 15px;">
        + Tambah
    </button>
</div>

<!-- 2. Box Abu-abu Utama -->
<div class="px-4 pt-2 pb-4 rounded-4" style="background-color: #d1d5db; min-height: 480px;">
    <div class="table-responsive">
        <table class="table table-borderless align-middle mb-0" style="border-collapse: separate; border-spacing: 0 8px;">
            <thead style="background: transparent !important;">
                <tr class="text-dark fw-bold small text-center" style="background: transparent !important;">
                    <th class="text-start ps-4 pt-2 pb-2" style="width: 20%; background: transparent !important;">Nama Barang</th>
                    <th class="pt-2 pb-2" style="width: 15%; background: transparent !important;">NO. Seri</th>
                    <th class="pt-2 pb-2" style="width: 15%; background: transparent !important;">Barcode</th>
                    <th class="pt-2 pb-2" style="width: 10%; background: transparent !important;">Jumlah</th>
                    <th class="pt-2 pb-2" style="width: 15%; background: transparent !important;">Keterangan</th>
                    <th class="pt-2 pb-2" style="width: 13%; background: transparent !important;">Tanggal</th>
                    <th class="pt-2 pb-2" style="width: 12%; background: transparent !important;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="bg-white text-center shadow-sm clickable-row" style="border-radius: 10px; cursor: pointer;" onclick="window.location='{{ route('inventory.show', $item->id) }}'">
                    <td class="fw-bold text-start ps-4 py-3" style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">{{ $item->nama_barang }}</td>
                    <td>{{ $item->no_seri }}</td>
                    <td>{{ $item->barcode }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>
                        @if($item->status == 'Siap Kirim')
                            <span class="badge px-3 py-2 fw-bold" style="background-color: #dcfce7; color: #15803d !important; border-radius: 6px; font-size: 12px;">Siap Kirim</span>
                        @elseif($item->status == 'Bermasalah')
                            <span class="badge px-3 py-2 fw-bold" style="background-color: #fee2e2; color: #b91c1c !important; border-radius: 6px; font-size: 12px;">Bermasalah</span>
                        @else
                            <span class="badge px-3 py-2 fw-bold" style="background-color: #fef08a; color: #a16207 !important; border-radius: 6px; font-size: 12px;">Belum dicek</span>
                        @endif
                    </td>
                    <td class="text-end pe-3" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px;" onclick="event.stopPropagation();">
                        <div class="d-flex justify-content-end">
                            <!-- Tombol Delete ke Trash Bin -->
                            <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Pindahkan data {{ $item->nama_barang }} ke Trash Bin?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm p-1.5" title="Hapus ke Trash Bin">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="bg-white text-center shadow-sm">
                    <td colspan="8" class="py-4 text-muted" style="border-radius: 10px;">Belum ada data barang.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3 d-flex justify-content-end">
        {{ $items->links() }}
    </div>
</div>

<!-- Modal Tambah Barang Masuk & Deskripsi Lengkap (Menggunakan JS Manual) -->
<div class="modal fade" id="modalTambah" tabindex="-1" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('inventory.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow-lg bg-white">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Tambah Barang Masuk</h5>
                <button type="button" class="btn-close" onclick="tutupModalTambah()"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control rounded-3" placeholder="Masukkan nama barang..." required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold small text-secondary">Nomor Seri</label>
                        <input type="text" name="no_seri" class="form-control rounded-3" placeholder="Contoh: SN-001" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold small text-secondary">Barcode</label>
                        <input type="text" name="barcode" class="form-control rounded-3" placeholder="Nomor barcode...">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold small text-secondary">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control rounded-3" min="1" placeholder="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold small text-secondary">Tanggal Masuk</label>
                        <input type="date" name="tanggal" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Status Barang</label>
                    <select name="status" class="form-select rounded-3" required>
                        <option value="Belum dicek">Belum dicek</option>
                        <option value="Siap Kirim">Siap Kirim</option>
                        <option value="Bermasalah">Bermasalah</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Deskripsi / Keterangan Barang Masuk</label>
                    <textarea name="keterangan" class="form-control rounded-3" rows="3" placeholder="Tambahkan deskripsi atau catatan kondisi barang masuk..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" onclick="tutupModalTambah()">Batal</button>
                <button type="submit" class="btn text-white rounded-pill px-4 fw-bold btn-hover-effect" style="background-color: #557cb8;">Simpan Barang</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalTambah() {
        let modal = document.getElementById('modalTambah');
        modal.style.display = 'block';
        modal.classList.add('show');
    }

    function tutupModalTambah() {
        let modal = document.getElementById('modalTambah');
        modal.style.display = 'none';
        modal.classList.remove('show');
    }
</script>
@endsection