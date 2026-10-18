<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\QcEvent;
use App\Models\DeletedItem;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    // 1. Halaman Dashboard Utama
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('no_seri', 'like', '%' . $request->search . '%');
        }

        $items = $query->latest()->get();
        $qcEvents = QcEvent::orderBy('event_date', 'asc')->get();
        $now = Carbon::now('Asia/Jakarta');
        $prayerSchedule = $this->getPrayerSchedule($now);

        return view('inventory.index', compact('items', 'qcEvents', 'now', 'prayerSchedule'));
    }

    // 2. Halaman Data Inventaris (Figma Kanan)
    public function data(Request $request)
    {
        $query = Item::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('no_seri', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $items = $query->latest()->paginate(10);

        return view('inventory.data', compact('items'));
    }

    // 3. Halaman Laporan (History Report)
    public function laporan(Request $request)
    {
        $query = Item::query();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        $items = $query->latest()->get();

        return view('inventory.laporan', compact('items'));
    }

    // 4. Halaman Jadwal Shalat & Agenda QC
    public function jadwal(Request $request)
    {
        $qcEvents = QcEvent::orderBy('event_date', 'asc')->get();
        $now = Carbon::now('Asia/Jakarta');
        $validated = $request->validate([
            'date' => ['sometimes', 'date_format:Y-m-d'],
        ]);
        $selectedDate = $validated['date'] ?? $now->toDateString();
        $prayerSchedule = $this->getPrayerSchedule(Carbon::createFromFormat('Y-m-d', $selectedDate, 'Asia/Jakarta'));

        return view('inventory.jadwal', compact('qcEvents', 'now', 'prayerSchedule', 'selectedDate'));
    }

    public function prayerSchedule(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
        ]);
        $date = Carbon::createFromFormat('Y-m-d', $validated['date'], 'Asia/Jakarta');
        $schedule = $this->getPrayerSchedule($date);

        if ($schedule === null) {
            return response()->json([
                'message' => 'Jadwal shalat untuk tanggal ini tidak tersedia. Silakan coba lagi.',
            ], 503);
        }

        return response()->json([
            'date' => $date->toDateString(),
            ...$schedule,
        ]);
    }

    // 5. Halaman Data Sampah (Trash Bin)
    public function trash()
    {
        $deletedItems = DeletedItem::latest()->get();
        return view('inventory.trash', compact('deletedItems'));
    }

    public function restore($id)
    {
        $restored = DB::transaction(function () use ($id) {
            $deletedItem = DeletedItem::findOrFail($id);

            if (Item::where('no_seri', $deletedItem->no_seri)->exists()) {
                return false;
            }

            Item::create($deletedItem->only([
                'nama_barang',
                'no_seri',
                'barcode',
                'jumlah',
                'keterangan',
                'tanggal',
                'status',
            ]));

            $deletedItem->delete();

            return true;
        });

        if (!$restored) {
            return redirect()->route('inventory.trash')->with('error', 'Barang tidak dapat dipulihkan karena nomor seri tersebut sudah digunakan.');
        }

        return redirect()->route('inventory.trash')->with('success', 'Data barang berhasil dipulihkan!');
    }

    // 6. Halaman Profil Akun
    public function profile(Request $request)
    {
        $profile = $request->user();

        return view('inventory.profile', compact('profile'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'photo' => ['sometimes', 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if (! $request->hasAny(['name', 'location', 'phone']) && ! $request->hasFile('photo')) {
            abort(422, 'Tidak ada perubahan profil yang dikirim.');
        }

        $profile = $request->user();
        $oldPhotoPath = $profile->photo_path;
        $updates = collect($validated)->only(['name', 'location', 'phone'])->all();

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profiles', 'public');
            if ($photoPath === false) {
                throw new \RuntimeException('Foto profil gagal disimpan.');
            }
            $updates['photo_path'] = $photoPath;
        }

        $profile->update($updates);

        if (isset($updates['photo_path']) && $oldPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return redirect()->route('inventory.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    // 7. Simpan Barang Baru (Tambah)
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'no_seri'     => 'required|string|unique:items,no_seri',
            'jumlah'      => 'required|integer|min:1',
            'tanggal'     => 'required|date',
            'status'      => 'required|in:Belum dicek,Siap Kirim,Bermasalah',
        ]);

        Item::create([
            'nama_barang' => $request->nama_barang,
            'no_seri'     => $request->no_seri,
            'barcode'     => $request->no_seri,
            'jumlah'      => $request->jumlah,
            'keterangan'  => $request->keterangan,
            'tanggal'     => $request->tanggal,
            'status'      => $request->status,
        ]);

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan!');
    }

    // 8. Update Barang
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'no_seri'     => 'required|string|unique:items,no_seri,' . $id,
            'barcode'     => 'nullable|string|max:255',
            'jumlah'      => 'required|integer|min:1',
            'tanggal'     => 'required|date',
            'status'      => 'required|in:Belum dicek,Siap Kirim,Bermasalah',
            'keterangan'  => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->route('inventory.show', $item->id)->with('success', 'Data barang berhasil diperbarui!');
    }

    // 9. Hapus Barang (Pindah ke Trash)
    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        DeletedItem::create([
            'nama_barang' => $item->nama_barang,
            'no_seri'     => $item->no_seri,
            'barcode'     => $item->barcode,
            'jumlah'      => $item->jumlah,
            'keterangan'  => $item->keterangan,
            'tanggal'     => $item->tanggal,
            'status'      => $item->status,
        ]);

        $item->delete();

        return redirect()->back()->with('success', 'Barang berhasil dihapus dan dipindahkan ke riwayat sampah!');
    }

    // 10. Simpan Agenda QC
    public function storeQcEvent(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
        ]);

        QcEvent::create([
            ...$validated,
            'created_by' => $request->user()->name,
        ]);

        return redirect()->route('inventory.jadwal', ['date' => $validated['event_date']])
            ->with('success', 'Agenda QC berhasil ditambahkan!');
    }

    public function updateQcEvent(Request $request, QcEvent $event)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date_format:Y-m-d'],
            'event_time' => ['nullable', 'date_format:H:i'],
        ]);

        $event->update($validated);

        return redirect()->route('inventory.jadwal', ['date' => $validated['event_date']])
            ->with('success', 'Agenda QC berhasil diperbarui!');
    }

    public function destroyQcEvent(QcEvent $event)
    {
        $eventDate = $event->event_date;
        $event->delete();

        return redirect()->route('inventory.jadwal', ['date' => $eventDate])
            ->with('success', 'Agenda QC berhasil dihapus.');
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);

        return view('inventory.show', compact('item'));
    }

    private function getPrayerSchedule(Carbon $date): ?array
    {
        $cacheKey = 'prayer-times-karawang-' . $date->toDateString();

        return Cache::remember($cacheKey, now('Asia/Jakarta')->addDays(30), function () use ($date) {
            try {
                $response = Http::acceptJson()
                    ->timeout(5)
                    ->get('https://api.aladhan.com/v1/timings/' . $date->format('d-m-Y'), [
                        'latitude' => -6.3063,
                        'longitude' => 107.3025,
                        'method' => 20,
                        'timezonestring' => 'Asia/Jakarta',
                    ]);
            } catch (ConnectionException) {
                return null;
            }

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json('data');
            $apiTimings = $data['timings'] ?? null;
            if (!is_array($apiTimings)) {
                return null;
            }

            $timeKeys = [
                'Imsak' => 'Imsak',
                'Subuh' => 'Fajr',
                'Dzuhur' => 'Dhuhr',
                'Ashar' => 'Asr',
                'Maghrib' => 'Maghrib',
                'Isya' => 'Isha',
            ];
            $times = [];

            foreach ($timeKeys as $label => $key) {
                $time = preg_replace('/\s*\([^)]*\)/', '', (string) ($apiTimings[$key] ?? ''));
                if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
                    return null;
                }

                $times[$label] = $time;
            }

            return [
                'times' => $times,
                'hijri_date' => $data['date']['hijri']['date'] ?? null,
            ];
        });
    }
}