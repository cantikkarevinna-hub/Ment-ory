@extends('layouts.app')

@section('title', 'Jadwal & Kegiatan')

@section('content')
<!-- Header Banner Utama -->
<div class="main-banner d-flex justify-content-between align-items-center mb-4 p-4 rounded-4" style="background-color: #557cb8;">
    <h2 class="fw-bold m-0 text-white text-decoration-underline" style="text-underline-offset: 8px;">Jadwal Shalat</h2>
    
    @include('partials.profile-button')
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
                <button type="button" class="btn btn-sm text-white rounded-pill px-3 py-1.5 fw-semibold shadow-sm" style="background-color: #557cb8; font-size: 13px;" onclick="bukaModalAcara()">
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
        <div class="rounded-4 bg-white shadow-sm border-0 overflow-hidden">
            <div>
                <!-- Banner Info Status Waktu Sesuai Referensi Foto -->
            <div class="p-4 mb-4 text-white" style="background-color: #557cb8;">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <h4 class="fw-bold m-0 fs-5" id="status-waktu-shalat">Memuat jadwal shalat...</h4>
                            <div class="small mt-1 opacity-90" id="info-waktu-shalat"></div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold small"><i class="bi bi-calendar-event me-1"></i> <span id="jadwal-current-date">{{ $now->format('d-m-Y') }}</span></div>
                            <div class="small opacity-75 mt-1" style="font-size: 12px;" id="hijri-date">{{ $prayerSchedule['hijri_date'] ?? '' }}</div>
                        </div>
                    </div>
                    <hr class="my-3 opacity-50">
                    <div class="small opacity-90">
                        <i class="bi bi-geo-alt me-1"></i> Karawang Barat, Karawang – Indonesia
                    </div>
                </div>

                <!-- List Jam Shalat -->
                <div class="d-flex flex-column px-4 pb-4" id="prayer-list">
                    <div class="text-center text-muted py-4">Memuat jadwal shalat...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buat / Tambah Acara Kalender -->
<div class="modal fade" id="modalAcara" tabindex="-1" aria-labelledby="modalAcaraLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="form-acara" action="{{ route('qc.store') }}" method="POST" class="modal-content rounded-4 border-0">
            @csrf
            <input type="hidden" name="_method" id="acara-method" value="POST">
            <input type="hidden" name="edit_event_id" id="edit-event-id" value="">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalAcaraLabel">Buat Jadwal Acara / Kegiatan</h5>
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
                    <label for="input-tgl-terpilih" class="form-label fw-semibold">Tanggal Acara</label>
                    <input type="date" id="input-tgl-terpilih" name="event_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="input-nama-acara" class="form-label fw-semibold">Nama Acara / Kegiatan</label>
                    <input type="text" id="input-nama-acara" name="title" class="form-control" placeholder="Contoh: Pengecekan Stok Gudang" maxlength="255" required>
                </div>
                <div class="mb-3">
                    <label for="input-jam-acara" class="form-label fw-semibold">Jam Acara (opsional)</label>
                    <input type="time" id="input-jam-acara" name="event_time" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
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
            'update_url' => route('qc.update', $event->id),
            'delete_url' => route('qc.destroy', $event->id),
        ];
    })->all();
@endphp

<script>
    const qcEvents = @json($qcEventData);
    const initialPrayerSchedule = @json($prayerSchedule);

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
    const prayerScheduleUrl = @json(route('inventory.prayer-schedule'));
    const csrfToken = @json(csrf_token());
    let currentPrayerSchedule = initialPrayerSchedule;
    let prayerRequestNumber = 0;

    let tanggalTerpilih = @json($selectedDate);
    const [selectedYear, selectedMonth] = tanggalTerpilih.split('-').map(Number);
    let tahunAktif = selectedYear;
    let bulanAktif = selectedMonth - 1;
    let daftarAgenda = {};

    qcEvents.forEach((event) => {
        if (!daftarAgenda[event.date]) {
            daftarAgenda[event.date] = [];
        }
        daftarAgenda[event.date].push(event);
    });

    function formatDate(date) {
        const year = date.getUTCFullYear();
        const month = String(date.getUTCMonth() + 1).padStart(2, '0');
        const day = String(date.getUTCDate()).padStart(2, '0');
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
        const firstDay = new Date(Date.UTC(tahunAktif, bulanAktif, 1)).getUTCDay();
        const daysInMonth = new Date(Date.UTC(tahunAktif, bulanAktif + 1, 0)).getUTCDate();
        const daysPrevMonth = new Date(Date.UTC(tahunAktif, bulanAktif, 0)).getUTCDate();

        document.getElementById('kalender-bulan-tahun').innerText = `${namaBulan[bulanAktif]} ${tahunAktif}`;

        const tbody = document.getElementById('kalender-body');
        tbody.innerHTML = '';

        let row = document.createElement('tr');

        for (let i = 0; i < firstDay; i++) {
            const prevDate = daysPrevMonth - firstDay + i + 1;
            const prevDateValue = formatDate(new Date(Date.UTC(tahunAktif, bulanAktif - 1, prevDate)));
            row.appendChild(buildDateCell(prevDateValue, prevDate, false));
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dateValue = formatDate(new Date(Date.UTC(tahunAktif, bulanAktif, day)));
            if ((day + firstDay - 1) % 7 === 0) {
                tbody.appendChild(row);
                row = document.createElement('tr');
            }
            row.appendChild(buildDateCell(dateValue, day, true));
        }

        let nextMonthDay = 1;
        while (row.children.length < 7) {
            const nextDateValue = formatDate(new Date(Date.UTC(tahunAktif, bulanAktif + 1, nextMonthDay)));
            row.appendChild(buildDateCell(nextDateValue, nextMonthDay, false));
            nextMonthDay++;
        }

        tbody.appendChild(row);

        const agendaList = daftarAgenda[tanggalTerpilih] || [];
        document.getElementById('info-agenda').innerText = `Agenda QC (${tanggalTerpilih})`;

        const agendaContainer = document.getElementById('teks-nama-acara');
        agendaContainer.replaceChildren();
        if (agendaList.length) {
            agendaList.forEach((event) => {
                const card = document.createElement('div');
                card.className = 'p-3 rounded-3 shadow-sm border-start border-4 border-primary bg-light text-start mb-2';

                const title = document.createElement('div');
                title.className = 'fw-bold text-dark';
                title.style.fontSize = '14px';
                title.textContent = event.title;

                const titleRow = document.createElement('div');
                titleRow.className = 'd-flex justify-content-between align-items-start gap-2';
                titleRow.appendChild(title);

                const details = document.createElement('div');
                details.className = 'text-muted mt-1';
                details.style.fontSize = '11px';
                details.textContent = `${event.time ? `Jam ${event.time.slice(0, 5)} • ` : ''}${event.date}`;

                const actions = document.createElement('div');
                actions.className = 'd-flex gap-1 flex-shrink-0';

                const editButton = document.createElement('button');
                editButton.type = 'button';
                editButton.className = 'btn btn-sm btn-outline-primary rounded-pill px-2 py-0';
                editButton.textContent = 'Edit';
                editButton.addEventListener('click', () => editAgenda(event));

                const deleteForm = document.createElement('form');
                deleteForm.method = 'POST';
                deleteForm.action = event.delete_url;
                deleteForm.addEventListener('submit', (submitEvent) => {
                    if (!window.confirm('Hapus agenda ini secara permanen? Agenda yang dihapus tidak masuk Trash Bin.')) {
                        submitEvent.preventDefault();
                    }
                });
                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = csrfToken;
                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                const deleteButton = document.createElement('button');
                deleteButton.type = 'submit';
                deleteButton.className = 'btn btn-sm btn-outline-danger rounded-pill px-2 py-0';
                deleteButton.textContent = 'Hapus';
                deleteForm.append(token, method, deleteButton);

                actions.append(editButton, deleteForm);
                titleRow.appendChild(actions);
                card.append(titleRow, details);
                agendaContainer.appendChild(card);
            });
        } else {
            agendaContainer.textContent = 'Tidak ada agenda QC pada tanggal ini.';
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
        const [year, month] = fullDate.split('-').map(Number);
        tahunAktif = year;
        bulanAktif = month - 1;
        renderKalender();
        loadPrayerSchedule(fullDate);
    }

    function bukaModalAcara() {
        const form = document.getElementById('form-acara');
        form.action = @json(route('qc.store'));
        document.getElementById('acara-method').value = 'POST';
        document.getElementById('edit-event-id').value = '';
        document.getElementById('modalAcaraLabel').textContent = 'Buat Jadwal Acara / Kegiatan';
        document.getElementById('input-tgl-terpilih').value = tanggalTerpilih;
        document.getElementById('input-nama-acara').value = '';
        document.getElementById('input-jam-acara').value = '';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAcara')).show();
    }

    function editAgenda(event) {
        const form = document.getElementById('form-acara');
        form.action = event.update_url;
        document.getElementById('acara-method').value = 'PUT';
        document.getElementById('edit-event-id').value = event.id;
        document.getElementById('modalAcaraLabel').textContent = 'Edit Agenda QC';
        document.getElementById('input-tgl-terpilih').value = event.date;
        document.getElementById('input-nama-acara').value = event.title;
        document.getElementById('input-jam-acara').value = event.time ? event.time.slice(0, 5) : '';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAcara')).show();
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

    function formatSelectedDate(dateKey) {
        return new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta', day: 'numeric', month: 'long', year: 'numeric'
        }).format(new Date(`${dateKey}T12:00:00+07:00`));
    }

    function renderPrayerList(schedule) {
        const prayerList = document.getElementById('prayer-list');
        prayerList.replaceChildren();
        if (!schedule || !schedule.times) {
            prayerList.innerHTML = '<div class="text-center text-muted py-4">Jadwal shalat untuk tanggal ini tidak tersedia. Silakan coba lagi.</div>';
            return;
        }

        ['Imsak', 'Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'].forEach((name) => {
            const row = document.createElement('div');
            row.className = 'd-flex justify-content-between align-items-center py-3';

            const label = document.createElement('span');
            label.className = 'fw-bold text-dark fs-6';
            label.style.width = '100px';
            label.textContent = name;

            const divider = document.createElement('div');
            divider.className = 'flex-grow-1 mx-3 border-bottom';
            divider.style.cssText = 'border-style: dashed !important; border-color: #cbd5e1 !important;';

            const timeAndAlarm = document.createElement('div');
            timeAndAlarm.className = 'd-flex align-items-center gap-3';
            const time = document.createElement('span');
            time.className = 'fw-semibold text-secondary fs-6';
            time.textContent = `${schedule.times[name]} WIB`;

            const alarm = document.createElement('button');
            alarm.type = 'button';
            alarm.className = 'btn btn-sm rounded-circle text-white d-flex align-items-center justify-content-center p-0 shadow-sm';
            alarm.style.cssText = 'width: 32px; height: 32px; background-color: #94a3b8;';
            alarm.setAttribute('aria-label', `Atur pengingat ${name}`);
            alarm.innerHTML = '<i class="bi bi-plus fs-5"></i>';
            alarm.addEventListener('click', () => toggleAlarm(alarm));
            timeAndAlarm.append(time, alarm);
            row.append(label, divider, timeAndAlarm);
            prayerList.appendChild(row);
        });
    }

    async function loadPrayerSchedule(dateKey) {
        const requestNumber = ++prayerRequestNumber;
        currentPrayerSchedule = null;
        const status = document.getElementById('status-waktu-shalat');
        const info = document.getElementById('info-waktu-shalat');
        status.textContent = 'Memuat jadwal shalat...';
        info.textContent = '';
        const prayerList = document.getElementById('prayer-list');
        prayerList.textContent = 'Memuat jadwal shalat...';
        try {
            const response = await fetch(`${prayerScheduleUrl}?date=${encodeURIComponent(dateKey)}`, {
                headers: { 'Accept': 'application/json' },
            });
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Jadwal shalat tidak tersedia.');
            }
            if (requestNumber !== prayerRequestNumber) return;

            currentPrayerSchedule = data;
            renderPrayerList(data);
            document.getElementById('hijri-date').textContent = data.hijri_date || '';
            updatePrayerStatus();
        } catch (error) {
            if (requestNumber !== prayerRequestNumber) return;
            currentPrayerSchedule = null;
            renderPrayerList(null);
            status.textContent = 'Jadwal shalat tidak tersedia';
            info.textContent = error.message;
        }
    }

    function updatePrayerStatus() {
        const timeFormatter = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hourCycle: 'h23'
        });
        const current = new Date();
        const currentDateKey = getJakartaDateKey();
        if (currentDateKey !== renderedToday) {
            window.location.reload();
            return;
        }

        document.getElementById('jadwal-current-date').textContent = formatSelectedDate(tanggalTerpilih);
        const status = document.getElementById('status-waktu-shalat');
        const info = document.getElementById('info-waktu-shalat');
        if (!currentPrayerSchedule || !currentPrayerSchedule.times) {
            status.textContent = 'Jadwal shalat tidak tersedia';
            info.textContent = 'Periksa koneksi internet lalu coba lagi.';
            return;
        }
        if (tanggalTerpilih !== currentDateKey) {
            status.textContent = `Jadwal shalat ${formatSelectedDate(tanggalTerpilih)}`;
            info.textContent = tanggalTerpilih < currentDateKey
                ? 'Tanggal ini sudah berlalu.'
                : 'Jadwal untuk tanggal yang dipilih.';
            return;
        }

        const [hour, minute] = timeFormatter.format(current).split(':').map(Number);
        const currentMinutes = hour * 60 + minute;
        const prayerNames = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];
        const prayerMinutes = prayerNames.map((name) => {
            const [prayerHour, prayerMinute] = currentPrayerSchedule.times[name].split(':').map(Number);
            return { name, time: currentPrayerSchedule.times[name], minutes: prayerHour * 60 + prayerMinute };
        });
        const passedPrayer = prayerMinutes.filter((prayer) => prayer.minutes <= currentMinutes).at(-1);
        const nextPrayer = prayerMinutes.find((prayer) => prayer.minutes > currentMinutes);

        if (passedPrayer) {
            const elapsed = currentMinutes - passedPrayer.minutes;
            status.textContent = elapsed > 0
                ? `Waktu ${passedPrayer.name} sudah lewat kurang lebih ${elapsed} menit yang lalu`
                : `Waktu ${passedPrayer.name} baru saja masuk`;
            info.textContent = nextPrayer
                ? `Waktu ${nextPrayer.name} berikutnya pukul ${nextPrayer.time} WIB`
                : 'Jadwal shalat hari ini selesai.';
        } else if (nextPrayer) {
            status.textContent = `Waktu ${nextPrayer.name} berikutnya`;
            info.textContent = `Pukul ${nextPrayer.time} WIB`;
        } else {
            status.textContent = 'Jadwal shalat hari ini selesai';
            info.textContent = 'Jadwal berikutnya dimulai besok.';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderKalender();
        renderPrayerList(currentPrayerSchedule);
        updatePrayerStatus();
        window.setInterval(updatePrayerStatus, 15000);
        @if($errors->any())
            const oldEventId = @json(old('edit_event_id'));
            const oldEvent = qcEvents.find((event) => String(event.id) === String(oldEventId));
            if (oldEvent) {
                editAgenda(oldEvent);
            } else {
                bukaModalAcara();
            }
            document.getElementById('input-tgl-terpilih').value = @json(old('event_date'));
            document.getElementById('input-nama-acara').value = @json(old('title'));
            document.getElementById('input-jam-acara').value = @json(old('event_time'));
        @endif
    });
</script>
@endsection