@extends('layouts.app')

@section('title', 'History Report')

@section('content')
<style>
    .clickable-row > td {
        transition: background-color 0.18s ease;
    }
    .clickable-row:hover > td {
        background-color: #eef0f2 !important;
    }
</style>

<!-- Header Banner Utama -->
<div class="main-banner d-flex justify-content-between align-items-center mb-4 p-4 rounded-4" style="background-color: #557cb8;">
    <h2 class="fw-bold m-0 text-white text-decoration-underline" style="text-underline-offset: 8px;">History Report</h2>
    
    <a href="{{ route('inventory.profile') }}" class="btn rounded-pill px-3 py-2 text-white d-flex align-items-center gap-2 border-0 shadow-sm" style="background-color: rgba(255, 255, 255, 0.25);">
        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 30px; height: 30px; font-size: 14px;">
            <i class="bi bi-person-fill"></i>
        </div>
        <span class="fw-semibold small">Chantikka Revinna</span>
    </a>
</div>

<!-- 1. Controls Bar (DI LUAR Box Abu-abu) -->
<form action="{{ route('inventory.laporan') }}" method="GET" class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <!-- Filter Status Kapsul -->
    <div class="d-flex gap-3 align-items-center">
        <a href="{{ route('inventory.laporan') }}" class="btn btn-sm px-4 py-2 rounded-pill fw-semibold shadow-sm {{ !request('status') ? 'text-white' : 'btn-light text-dark border' }}" style="font-size: 14px; {{ !request('status') ? 'background-color: #0284c7; border: none;' : '' }}">Semua</a>
        <a href="{{ route('inventory.laporan', ['status' => 'Belum dicek']) }}" class="btn btn-sm px-4 py-2 rounded-pill fw-semibold shadow-sm {{ request('status') == 'Belum dicek' ? 'text-white' : 'btn-light text-dark border' }}" style="font-size: 14px; {{ request('status') == 'Belum dicek' ? 'background-color: #0284c7; border: none;' : '' }}">Belum di Cek</a>
        <a href="{{ route('inventory.laporan', ['status' => 'Bermasalah']) }}" class="btn btn-sm px-4 py-2 rounded-pill fw-semibold shadow-sm {{ request('status') == 'Bermasalah' ? 'text-white' : 'btn-light text-dark border' }}" style="font-size: 14px; {{ request('status') == 'Bermasalah' ? 'background-color: #0284c7; border: none;' : '' }}">Bermasalah</a>
        <a href="{{ route('inventory.laporan', ['status' => 'Siap Kirim']) }}" class="btn btn-sm px-4 py-2 rounded-pill fw-semibold shadow-sm {{ request('status') == 'Siap Kirim' ? 'text-white' : 'btn-light text-dark border' }}" style="font-size: 14px; {{ request('status') == 'Siap Kirim' ? 'background-color: #0284c7; border: none;' : '' }}">Siap Kirim</a>
    </div>

    <!-- Filter Tanggal (Date Picker) & Tombol Filter -->
    <div class="d-flex align-items-center gap-2">
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control rounded-pill px-3 py-2 bg-white border shadow-sm text-secondary" style="font-size: 13px; width: 155px; border-color: #d1d5db !important;">
        <span class="fw-bold text-secondary px-1" style="font-size: 13px;">TO</span>
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control rounded-pill px-3 py-2 bg-white border shadow-sm text-secondary" style="font-size: 13px; width: 155px; border-color: #d1d5db !important;">
        <button type="submit" class="btn text-white rounded-pill px-3 py-2 shadow-sm d-flex align-items-center justify-content-center" style="background-color: #0284c7; width: 38px; height: 38px;"><i class="bi bi-filter fs-6"></i></button>
    </div>
</form>

<!-- 2. Box Abu-abu Utama (Hanya Membungkus Tabel & Header Kolom) -->
<div class="px-4 pt-2 pb-4 rounded-4 mb-4" style="background-color: #d1d5db; min-height: 400px;">
    <div class="table-responsive">
        <table class="table table-borderless align-middle mb-0" style="border-collapse: separate; border-spacing: 0 8px;">
            <thead style="background: transparent !important;">
                <tr class="text-dark fw-bold small text-center" style="background: transparent !important; border-bottom: 1.5px solid #a3a3a3;">
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
                    <td class="pe-3" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px;">
                        @if($item->status == 'Siap Kirim')
                            <span class="badge px-3 py-2 fw-bold" style="background-color: #dcfce7; color: #15803d !important; border-radius: 6px; font-size: 12px;">Siap Kirim</span>
                        @elseif($item->status == 'Bermasalah')
                            <span class="badge px-3 py-2 fw-bold" style="background-color: #fee2e2; color: #b91c1c !important; border-radius: 6px; font-size: 12px;">Bermasalah</span>
                        @else
                            <span class="badge px-3 py-2 fw-bold" style="background-color: #fef08a; color: #a16207 !important; border-radius: 6px; font-size: 12px;">Belum dicek</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr class="bg-white text-center shadow-sm">
                    <td colspan="7" class="py-4 text-muted" style="border-radius: 10px;">Belum ada data laporan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="text-end mt-4">
        <button onclick="window.print()" class="btn px-4 py-2 text-white fw-bold shadow-sm" style="background-color: #557cb8; border-radius: 10px;">
            Export Excel
        </button>
    </div>
</div>

<!-- Bottom Section Diagram & Total -->
<div class="row g-3">
    <div class="col-md-7">
        <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between shadow-sm" style="background-color: #d1d5db;">
            <h5 class="fw-bold text-dark mb-4">Inventory Diagram</h5>
            <div class="d-flex align-items-center justify-content-between px-3">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle d-inline-block" style="width: 14px; height: 14px; background-color: #2563eb;"></span>
                        <span class="text-secondary fw-semibold">Siap Kirim</span>
                        <strong class="text-dark fs-5 ms-2">89%</strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle d-inline-block" style="width: 14px; height: 14px; background-color: #f87171;"></span>
                        <span class="text-secondary fw-semibold">Bermasalah</span>
                        <strong class="text-dark fs-5 ms-2">1%</strong>
                    </div>
                </div>
                <div class="position-relative d-flex align-items-center justify-content-center me-4">
                    <div style="width: 140px; height: 140px; border-radius: 50%; background: conic-gradient(#2563eb 0% 89%, #f87171 89% 90%, #cbd5e1 90% 100%); display: flex; align-items: center; justify-content: center;">
                        <div style="width: 85px; height: 85px; background: #d1d5db; border-radius: 50%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5 d-flex flex-column gap-3">
        <div class="p-4 text-white rounded-4 d-flex justify-content-between align-items-center shadow-sm" style="background-color: #557cb8;">
            <div class="fw-bold small text-uppercase tracking-wider">TOTAL BARANG MASUK</div>
            <div class="display-5 fw-bold">{{ $items->sum('jumlah') }}</div>
        </div>
        <div class="p-4 text-white rounded-4 d-flex justify-content-between align-items-center shadow-sm" style="background-color: #557cb8;">
            <div class="fw-bold small text-uppercase tracking-wider">TOTAL BARANG KELUAR</div>
            <div class="display-5 fw-bold">{{ $items->where('status', 'Siap Kirim')->sum('jumlah') }}</div>
        </div>
    </div>
</div>
@endsection