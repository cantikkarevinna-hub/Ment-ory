@extends('layouts.app')

@section('title', 'Jadwal & Kegiatan')

@section('content')
<!-- Header Banner Utama -->
<div class="main-banner d-flex justify-content-between align-items-center mb-4 p-4 rounded-4" style="background-color: #557cb8;">
    <h2 class="fw-bold m-0 text-white text-decoration-underline" style="text-underline-offset: 8px;">Jadwal Shalat</h2>
    
    <a href="{{ route('inventory.profile') }}" class="btn rounded-pill px-3 py-2 text-white d-flex align-items-center gap-2 border-0 shadow-sm" style="background-color: rgba(255, 255, 255, 0.25);">
        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 30px; height: 30px; font-size: 14px;">
            <i class="bi bi-person-fill"></i>
        </div>
        <span class="fw-semibold small">Chantikka Revinna</span>
    </a>
</div>

<!-- Container Utama: Kalender Mini (Kiri) & Jadwal Shalat Dominan (Kanan) -->
<div class="row g-4">
    <!-- Kolom Kiri: Kalender Mini 100% Mirip Referensi & Card Agenda -->
    <div class="col-md-5 d-flex flex-column gap-3">
        <!-- Kalender Card -->
        <div class="p-4 rounded-4 bg-white shadow-sm border-0">
            <!-- Header Bulan & Tombol Navigasi -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0 text-dark" id="kalender-bulan-tahun" style="font-size: 18px;"></h5>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-light border-0 px-2.5 py-1 rounded-3 shadow-sm text-secondary fw-bold" onclick="ubahBulan(-1)" style="background-color: #f1f3f5;"><</button>
                    <button class="btn btn-sm btn-light border-0 px-2.5 py-1 rounded-3 shadow-sm text-secondary fw-bold" onclick="ubahBulan(1)" style="background-color: #f1f3f5;">></button>
                </div>
            </div>

            <!-- Garis Pemisah Halus di Bawah Bulan -->
            <hr class="text-muted opacity-25 mb-3">

            <!-- Tabel Kalender -->
            <table class="table table-borderless text-center align-middle mb-0" style="font-size: 14px;">
                <thead>
                    <tr class="text-secondary fw-bold small" style="font-size: 13px;">
                        <th class="pb-3 fw-semibold">Min</th>
                        <th class="pb-3 fw-semibold">Sen</th>
                        <th class="pb-3 fw-semibold">Sel</th>
                        <th class="pb-3 fw-semibold">Rab</th>
                        <th class="pb-3 fw-semibold">Kam</th>
                        <th class="pb-3 fw-semibold">Jum</th>
                        <th class="pb-3 fw-semibold">Sab</th>
                    </tr>
                </thead>
                <tbody id="kalender-body" class="text-dark fw-medium" style="cursor: pointer; user-select: none;">
                    <!-- Render Otomatis via JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Card Agenda QC / Kegiatan -->
        <div class="p-4 rounded-4 bg-white shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark" id="info-agenda" style="font-size: 15px;">Agenda QC</span>
                <button class="btn btn-sm text-white rounded-pill px-3 py-1.5 fw-semibold shadow-sm" style="background-color: #557cb8; font-size: 13px;" data-bs-toggle="modal" data-bs-target="#modalAcara">
                    + Acara QC
                </button>
            </div>
            <!-- Garis Pemisah Halus di Bawah Judul Agenda -->
            <hr class="text-muted opacity-25 mb-3">
            <div class="text-muted text-center py-3 small" id="teks-nama-acara" style="font-size: 13px;">
                Tidak ada agenda QC pada tanggal ini.
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Jadwal Shalat Lebih Dominan dan Besar (col-md-7) -->
    <div class="col-md-7">
        <div class="rounded-4 bg-white shadow-sm h-100 border-0 d-flex flex-column justify-content-between overflow-hidden">
            <div>
                <!-- Banner Info Status Waktu Sesuai Referensi Foto -->
            <div class="p-4 mb-4 text-white" style="background-color: #557cb8;">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <h4 class="fw-bold m-0 fs-5" id="status-waktu-shalat">Jadwal shalat</h4>
                            <div class="small mt-1 opacity-90" id="info-waktu-shalat"></div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold small"><i class="bi bi-calendar-event me-1"></i> <span id="jadwal-current-date">{{ $now->format('d-m-Y') }}</span></div>
                            <div class="small opacity-75 mt-1" style="font-size: 12px;">{{ $prayerSchedule['hijri_date'] ?? '' }}</div>
                        </div>
                    </div>
                    <hr class="my-3 opacity-50">
                    <div class="small opacity-90">
                        <i class="bi bi-geo-alt me-1"></i> Karawang Barat, Karawang – Indonesia
                    </div>
                </div>

                <!-- List Jam Shalat -->
                <div class="d-flex flex-column px-4 pb-4">
                    @if($prayerSchedule)
                        @foreach(['Imsak', 'Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'] as $prayerName)
                            <div class="d-flex justify-content-between align-items-center py-3">
                                <span class="fw-bold text-dark fs-6" style="width: 100px;">{{ $prayerName }}</span>
                                <div class="flex-grow-1 mx-3 border-bottom" style="border-style: dashed !important; border-color: #cbd5e1 !important;"></div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fw-semibold text-secondary fs-6">{{ $prayerSchedule['times'][$prayerName] }} WIB</span>
                                    <button class="btn btn-sm rounded-circle text-white d-flex align-items-center justify-content-center p-0 shadow-sm" style="width: 32px; height: 32px; background-color: #94a3b8;" onclick="toggleAlarm(this)"><i class="bi bi-plus fs-5"></i></button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">Jadwal shalat tidak tersedia. Periksa koneksi internet lalu muat ulang halaman.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buat / Tambah Acara Kalender -->
<div class="modal fade" id="modalAcara" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('qc.store') }}" method="POST" class="modal-content rounded-4 border-0">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Buat Jadwal Acara / Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Terpilih</label>
                    <input type="text" id="input-tgl-terpilih" name="event_date" class="form-control bg-light" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Acara / Kegiatan</label>
                    <input type="text" id="input-nama-acara" name="title" class="form-control" placeholder="Contoh: Pengecekan Stok Gudang" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #557cb8; border: none;">Simpan Acara</button>
            </div>
        </form>
    </div>
</div>

@php
    $qcEventData = $qcEvents->map(function ($event) {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'date' => $event->event_date,
            'time' => $event->event_time,
        ];
    })->all();
@endphp

<script>
    const qcEvents = @json($qcEventData);

    const namaBulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    function getJakartaDateKey() {
        const parts = new Intl.DateTimeFormat('en-CA', {
            timeZone: 'Asia/Jakarta', year: 'numeric', month: '2-digit', day: '2-digit'
        }).formatToParts(new Date());
        const values = Object.fromEntries(parts.map((part) => [part.type, part.value]));
        return `${values.year}-${values.month}-${values.day}`;
    }

    const renderedToday = @json($now->toDateString());
    let todayKey = getJakartaDateKey();
    const prayerTimes = @json($prayerSchedule['times'] ?? []);

    const [todayYear, todayMonth] = todayKey.split('-').map(Number);
    let tahunAktif = todayYear;
    let bulanAktif = todayMonth - 1;
    let tanggalTerpilih = todayKey;
    let daftarAgenda = {};

    qcEvents.forEach((event) => {
        if (!daftarAgenda[event.date]) {
            daftarAgenda[event.date] = [];
        }
        daftarAgenda[event.date].push(event);
    });

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function buildDateCell(dateValue, dayNumber, isCurrentMonth) {
        const cell = document.createElement('td');
        const isSelected = dateValue === tanggalTerpilih;
        const isToday = dateValue === todayKey;
        const hasAgenda = !!(daftarAgenda[dateValue] && daftarAgenda[dateValue].length);

        cell.className = `py-2 ${!isCurrentMonth ? 'text-muted' : ''}`;
        cell.style.cursor = 'pointer';

        const circleClass = isSelected
            ? 'text-white fw-bold shadow-sm'
            : isToday
            ? 'text-dark fw-bold'
                : 'text-dark';

        const circleStyle = isSelected
            ? 'width: 32px; height: 32px; background-color: #557cb8; border-radius: 50%;'
            : isToday
                ? 'width: 32px; height: 32px; background-color: #eef0f2; border-radius: 50%;'
                : 'width: 32px; height: 32px; border-radius: 50%;';

        const dot = hasAgenda
            ? '<span class="position-absolute bottom-0 start-50 translate-middle-x" style="width: 6px; height: 6px; background: #557cb8; border-radius: 50%;"></span>'
            : '';

        cell.innerHTML = `
            <div class="position-relative d-inline-flex align-items-center justify-content-center ${isSelected || isToday ? 'mx-auto' : 'mx-auto'}" style="${circleStyle}">
                <span class="${circleClass}" style="font-size: 13px;">${dayNumber}</span>
                ${dot}
            </div>
        `;

        cell.onclick = function() {
            pilihTanggal(dateValue);
        };

        return cell;
    }

    function renderKalender() {
        const firstDay = new Date(tahunAktif, bulanAktif, 1).getDay();
        const daysInMonth = new Date(tahunAktif, bulanAktif + 1, 0).getDate();
        const daysPrevMonth = new Date(tahunAktif, bulanAktif, 0).getDate();

        document.getElementById('kalender-bulan-tahun').innerText = `${namaBulan[bulanAktif]} ${tahunAktif}`;

        const tbody = document.getElementById('kalender-body');
        tbody.innerHTML = '';

        let row = document.createElement('tr');

        for (let i = 0; i < firstDay; i++) {
            const prevDate = daysPrevMonth - firstDay + i + 1;
            const prevDateValue = formatDate(new Date(tahunAktif, bulanAktif - 1, prevDate));
            row.appendChild(buildDateCell(prevDateValue, prevDate, false));
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dateValue = formatDate(new Date(tahunAktif, bulanAktif, day));
            if ((day + firstDay - 1) % 7 === 0) {
                tbody.appendChild(row);
                row = document.createElement('tr');
            }
            row.appendChild(buildDateCell(dateValue, day, true));
        }

        let nextMonthDay = 1;
        while (row.children.length < 7) {
            const nextDateValue = formatDate(new Date(tahunAktif, bulanAktif + 1, nextMonthDay));
            row.appendChild(buildDateCell(nextDateValue, nextMonthDay, false));
            nextMonthDay++;
        }

        tbody.appendChild(row);

        const agendaList = daftarAgenda[tanggalTerpilih] || [];
        document.getElementById('info-agenda').innerText = `Agenda QC (${tanggalTerpilih})`;

        if (agendaList.length) {
            document.getElementById('teks-nama-acara').innerHTML = agendaList.map((event) => `
                <div class="p-3 rounded-3 shadow-sm border-start border-4 border-primary bg-light text-start mb-2">
                    <div class="fw-bold text-dark" style="font-size: 14px;">${event.title}</div>
                    <div class="text-muted mt-1" style="font-size: 11px;">
                        ${event.time ? 'Jam ' + event.time + ' • ' : ''}${tanggalTerpilih}
                    </div>
                </div>
            `).join('');
        } else {
            document.getElementById('teks-nama-acara').innerHTML = '<span class="text-muted">Tidak ada agenda QC pada tanggal ini.</span>';
        }

        document.getElementById('input-tgl-terpilih').value = tanggalTerpilih;
    }

    function ubahBulan(dir) {
        bulanAktif += dir;
        if (bulanAktif > 11) {
            bulanAktif = 0;
            tahunAktif++;
        } else if (bulanAktif < 0) {
            bulanAktif = 11;
            tahunAktif--;
        }
        renderKalender();
    }

    function pilihTanggal(fullDate) {
        tanggalTerpilih = fullDate;
        renderKalender();
    }

    function toggleAlarm(btn) {
        const icon = btn.querySelector('i');
        if (icon.classList.contains('bi-plus')) {
            icon.classList.remove('bi-plus');
            icon.classList.add('bi-check');
            btn.style.backgroundColor = '#64748b';
        } else {
            icon.classList.remove('bi-check');
            icon.classList.add('bi-plus');
            btn.style.backgroundColor = '#94a3b8';
        }
    }

    function updatePrayerStatus() {
        const dateFormatter = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta', day: 'numeric', month: 'long', year: 'numeric'
        });
        const timeFormatter = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hourCycle: 'h23'
        });
        const current = new Date();
        const currentDateKey = getJakartaDateKey();
        if (currentDateKey !== renderedToday) {
            window.location.reload();
            return;
        }

        document.getElementById('jadwal-current-date').textContent = dateFormatter.format(current);
        const currentTime = timeFormatter.format(current);
        const [hour, minute] = currentTime.split(':').map(Number);
        const currentMinutes = hour * 60 + minute;
        const prayers = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];
        const nextPrayer = prayers.find((name) => {
            const [prayerHour, prayerMinute] = (prayerTimes[name] || '00:00').split(':').map(Number);
            return prayerHour * 60 + prayerMinute > currentMinutes;
        });

        document.getElementById('status-waktu-shalat').textContent = nextPrayer
            ? `Waktu ${nextPrayer} berikutnya`
            : 'Jadwal shalat hari ini selesai';
        document.getElementById('info-waktu-shalat').textContent = nextPrayer
            ? `Pukul ${prayerTimes[nextPrayer]} WIB`
            : 'Jadwal berikutnya dimulai besok.';
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderKalender();
        updatePrayerStatus();
        window.setInterval(updatePrayerStatus, 15000);
    });
</script>
@endsection