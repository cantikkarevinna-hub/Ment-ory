@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Style Tambahan untuk Efek Animasi Tombol / Hover -->
<style>
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    }
    .hover-card:active {
        transform: translateY(-1px);
        box-shadow: 0 .25rem .5rem rgba(0,0,0,.1) !important;
    }
    .hover-link {
        transition: color 0.2s ease;
    }
    .hover-link:hover {
        color: #1e40af !important;
        text-decoration: underline !important;
    }
    .clickable-row > td {
        transition: background-color 0.18s ease;
    }
    .clickable-row:hover > td {
        background-color: #eef0f2 !important;
    }
</style>

<!-- 1. Header Banner Dashboard -->
<div class="main-banner d-flex justify-content-between align-items-center mb-4 p-4 rounded-4" style="background-color: #557cb8;">
    <h2 class="fw-bold m-0 text-white text-decoration-underline" style="text-underline-offset: 8px;">Dashboard</h2>
    
    <!-- Button Profile Kapsul -->
    <a href="{{ route('inventory.profile') }}" class="btn rounded-pill px-3 py-2 text-white d-flex align-items-center gap-2 border-0 shadow-sm" style="background-color: rgba(255, 255, 255, 0.25);">
        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 30px; height: 30px; font-size: 14px;">
            <i class="bi bi-person-fill"></i>
        </div>
        <span class="fw-semibold small">Chantikka Revinna</span>
    </a>
</div>

<!-- 2. Widget Jadwal Shalat -->
<a href="{{ route('inventory.jadwal') }}" class="text-decoration-none d-block mb-4">
    <div class="row g-0 rounded-4 overflow-hidden shadow-sm border bg-white hover-card">
        <div class="col-md-5 p-4 bg-white border-end d-flex flex-column justify-content-center">
            <h3 class="fw-bold text-dark m-0" id="dashboard-current-date">{{ $now->locale('id')->translatedFormat('j F Y') }}</h3>
            <div class="text-secondary small mt-1">
                <i class="bi bi-geo-alt me-1"></i> Karawang Barat, Karawang – Indonesia
            </div>
        </div>
        <div class="col-md-7 p-4 d-flex flex-column justify-content-center" style="background-color: #eaeaea;">
            @if($prayerSchedule)
                <h4 class="fw-bold text-dark m-0" id="dashboard-next-prayer">Memuat jadwal shalat...</h4>
                <div class="text-secondary small mt-1" id="dashboard-next-prayer-time"></div>
            @else
                <h4 class="fw-bold text-dark m-0">Jadwal shalat tidak tersedia</h4>
                <div class="text-secondary small mt-1">Periksa koneksi internet lalu muat ulang halaman.</div>
            @endif
        </div>
    </div>
</a>

<script>
    (() => {
        const renderedDate = @json($now->toDateString());
        const prayerTimes = @json($prayerSchedule['times'] ?? []);
        const prayers = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];
        const dateFormatter = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta', day: 'numeric', month: 'long', year: 'numeric'
        });
        const datePartsFormatter = new Intl.DateTimeFormat('en-CA', {
            timeZone: 'Asia/Jakarta', year: 'numeric', month: '2-digit', day: '2-digit'
        });

        function updatePrayerWidget() {
            const currentTime = new Date();
            const parts = Object.fromEntries(datePartsFormatter.formatToParts(currentTime).map((part) => [part.type, part.value]));
            const currentDate = `${parts.year}-${parts.month}-${parts.day}`;
            if (currentDate !== renderedDate) {
                window.location.reload();
                return;
            }

            document.getElementById('dashboard-current-date').textContent = dateFormatter.format(currentTime);

            const currentParts = Object.fromEntries(new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hourCycle: 'h23'
            }).formatToParts(currentTime).map((part) => [part.type, part.value]));
            const currentMinutes = Number(currentParts.hour) * 60 + Number(currentParts.minute);
            const nextPrayer = prayers.find((name) => {
                const [hour, minute] = (prayerTimes[name] || '00:00').split(':').map(Number);
                return hour * 60 + minute > currentMinutes;
            });

            const title = document.getElementById('dashboard-next-prayer');
            const time = document.getElementById('dashboard-next-prayer-time');
            if (title && nextPrayer) {
                title.textContent = `Waktu ${nextPrayer} Berikutnya`;
                time.textContent = `Jadwal shalat hari ini aktif · Pukul ${prayerTimes[nextPrayer]} WIB`;
            } else if (title) {
                title.textContent = 'Waktu shalat hari ini selesai';
                time.textContent = 'Jadwal shalat hari ini aktif';
            }
        }

        updatePrayerWidget();
        window.setInterval(updatePrayerWidget, 15000);
    })();
</script>

<!-- 3. 4 Kartu Statistik (Menghitung Total Akumulasi Kuantitas / Jumlah Barang) -->
<div class="row g-3 mb-4">
    <!-- Total Barang -> Akumulasi Seluruh Kolom Jumlah -->
    <div class="col-md-3">
        <a href="{{ route('inventory.laporan') }}" class="text-decoration-none d-block h-100">
            <div class="bg-white p-3 rounded-4 shadow-sm h-100 border-0 hover-card" style="border-left: 6px solid #2563eb !important;">
                <div class="text-secondary small fw-bold text-uppercase tracking-wider">TOTAL BARANG</div>
                <div class="fs-1 fw-bold text-dark mt-2">{{ $items->sum('jumlah') }}</div>
            </div>
        </a>
    </div>
    <!-- Siap Kirim -> Akumulasi Jumlah Status Siap Kirim -->
    <div class="col-md-3">
        <a href="{{ route('inventory.laporan', ['status' => 'Siap Kirim']) }}" class="text-decoration-none d-block h-100">
            <div class="bg-white p-3 rounded-4 shadow-sm h-100 border-0 hover-card" style="border-left: 6px solid #22c55e !important;">
                <div class="text-secondary small fw-bold text-uppercase tracking-wider">SIAP KIRIM</div>
                <div class="fs-1 fw-bold text-success mt-2">{{ $items->where('status', 'Siap Kirim')->sum('jumlah') }}</div>
            </div>
        </a>
    </div>
    <!-- Belum di Cek -> Akumulasi Jumlah Status Belum dicek -->
    <div class="col-md-3">
        <a href="{{ route('inventory.laporan', ['status' => 'Belum dicek']) }}" class="text-decoration-none d-block h-100">
            <div class="bg-white p-3 rounded-4 shadow-sm h-100 border-0 hover-card" style="border-left: 6px solid #eab308 !important;">
                <div class="text-secondary small fw-bold text-uppercase tracking-wider">BELUM DI CEK</div>
                <div class="fs-1 fw-bold text-warning mt-2">{{ $items->where('status', 'Belum dicek')->sum('jumlah') }}</div>
            </div>
        </a>
    </div>
    <!-- Bermasalah -> Akumulasi Jumlah Status Bermasalah -->
    <div class="col-md-3">
        <a href="{{ route('inventory.laporan', ['status' => 'Bermasalah']) }}" class="text-decoration-none d-block h-100">
            <div class="bg-white p-3 rounded-4 shadow-sm h-100 border-0 hover-card" style="border-left: 6px solid #ef4444 !important;">
                <div class="text-secondary small fw-bold text-uppercase tracking-wider">BERMASALAH</div>
                <div class="fs-1 fw-bold text-danger mt-2">{{ $items->where('status', 'Bermasalah')->sum('jumlah') }}</div>
            </div>
        </a>
    </div>
</div>

<!-- 4. Tabel Riwayat Input Terbaru -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <h5 class="fw-bold text-dark m-0">Riwayat Input Terbaru</h5>
        
        <form action="{{ route('inventory.index') }}" method="GET" class="position-relative">
            <input type="text" name="search" class="form-control rounded-pill px-3 py-2 pe-4 text-dark bg-white shadow-sm border" placeholder="Cari barang atau NO Seri..." value="{{ request('search') }}" style="width: 420px; font-size: 14px; border-color: #d1d5db !important;">
            <i class="bi bi-search position-absolute end-0 top-50 translate-middle-y me-3 text-muted fs-6"></i>
        </form>
    </div>

    <!-- Teks Lihat Semua Data dengan Efek Hover Berubah Warna -->
    <a href="{{ route('inventory.data') }}" class="text-primary fw-semibold small text-decoration-none hover-link">
        Lihat Semua Data &rarr;
    </a>
</div>

<!-- Box Abu-abu Utama -->
<div class="px-4 pt-2 pb-4 rounded-4" style="background-color: #d1d5db; min-height: 350px;">
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
                @forelse($items->take(5) as $item)
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
                    <td colspan="7" class="py-4 text-muted" style="border-radius: 10px;">Belum ada barang masuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection