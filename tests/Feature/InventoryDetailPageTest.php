<?php

namespace Tests\Feature;

use App\Models\DeletedItem;
use App\Models\Item;
use App\Models\QcEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InventoryDetailPageTest extends TestCase
{
    use RefreshDatabase;

    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->member = User::create([
            'name' => 'Anggota Uji',
            'email' => 'anggota@example.test',
            'password' => 'password-anggota-uji',
            'role' => 'member',
        ]);

        $this->actingAs($this->member);
    }

    public function test_inventory_detail_page_loads_for_existing_item(): void
    {
        $item = Item::create([
            'nama_barang' => 'Laptop Test',
            'no_seri' => 'SN-001',
            'barcode' => 'BC-001',
            'jumlah' => 3,
            'keterangan' => 'Barang uji coba',
            'tanggal' => '2026-10-06',
            'status' => 'Siap Kirim',
        ]);

        $response = $this->get(route('inventory.show', $item->id));

        $response->assertOk();
        $response->assertSee('Laptop Test');
    }

    public function test_profile_page_shows_profile_and_working_edit_controls(): void
    {
        $response = $this->get(route('inventory.profile'));

        $response->assertOk();
        $response->assertSee('Anggota Uji');
        $response->assertSee(route('inventory.profile.update'));
        $response->assertSee('profile-photo');
        $response->assertSee('Email Login (tidak dapat diubah)');
        $response->assertSee('data-bs-target="#editProfileModal"', false);
        $response->assertSee('id="editProfileModal"', false);
        $response->assertSee('bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', false);
        $response->assertDontSee('name="email"', false);
    }

    public function test_profile_can_be_updated_with_a_photo(): void
    {
        Storage::fake('public');

        $response = $this->put(route('inventory.profile.update'), [
            'name' => 'Nama Profil Baru',
            'location' => 'Plant 2',
            'phone' => '+628123456789',
            'email' => 'email-lain@example.com',
            'role' => 'admin',
            'photo' => UploadedFile::fake()->createWithContent(
                'profile.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/n1sAAAAASUVORK5CYII=')
            ),
        ]);

        $response->assertRedirect(route('inventory.profile'));
        $response->assertSessionHas('success', 'Profil berhasil diperbarui.');
        $profile = $this->member->fresh();

        $this->assertSame('Nama Profil Baru', $profile->name);
        $this->assertSame('anggota@example.test', $profile->email);
        $this->assertSame('member', $profile->role);
        $this->assertSame('Plant 2', $profile->location);
        $this->assertSame('+628123456789', $profile->phone);
        $this->assertNotNull($profile->photo_path);
        Storage::disk('public')->assertExists($profile->photo_path);

        $this->get(route('inventory.index'))
            ->assertOk()
            ->assertSee(asset('storage/'.$profile->photo_path));
    }

    public function test_calendar_page_loads_with_qc_events(): void
    {
        $today = now('Asia/Jakarta');
        Cache::forget('prayer-times-karawang-'.$today->toDateString());
        Http::fake([
            'api.aladhan.com/*' => Http::response([
                'code' => 200,
                'data' => [
                    'timings' => [
                        'Imsak' => '04:20 (+07)',
                        'Fajr' => '04:30 (+07)',
                        'Dhuhr' => '11:57 (+07)',
                        'Asr' => '15:16 (+07)',
                        'Maghrib' => '17:55 (+07)',
                        'Isha' => '19:05 (+07)',
                    ],
                    'date' => ['hijri' => ['date' => '14-04-1448']],
                ],
            ]),
        ]);

        QcEvent::create([
            'title' => 'QC Final Produk',
            'event_date' => $today->format('Y-m-d'),
            'event_time' => '09:00:00',
            'created_by' => 'Chantikka Revinna',
        ]);

        $response = $this->get(route('inventory.jadwal'));

        $response->assertOk();
        $response->assertSee('Jadwal Shalat');
        $response->assertSee('Agenda QC');
        $response->assertSee('11:57');
        $response->assertSee('14-04-1448');
        $response->assertSee('onclick="bukaModalAcara()"', false);
        $response->assertSee('id="modalAcara"', false);
        $response->assertSee(route('qc.store'));
        $response->assertSee('Edit Agenda QC');
        $response->assertSee('Hapus');

        $dashboard = $this->get(route('inventory.index'));
        $dashboard->assertOk();
        $dashboard->assertSee('11:57');
        Http::assertSent(fn ($request) => str_contains($request->url(), 'api.aladhan.com/v1/timings/'));
    }

    public function test_qc_event_can_be_created_from_the_calendar_form(): void
    {
        $response = $this->post(route('qc.store'), [
            'title' => 'Pengecekan Stok Gudang',
            'event_date' => '2026-10-30',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Agenda QC berhasil ditambahkan!');
        $this->assertDatabaseHas('qc_events', [
            'title' => 'Pengecekan Stok Gudang',
            'event_date' => '2026-10-30',
            'created_by' => 'Anggota Uji',
        ]);
    }

    public function test_qc_event_can_be_updated_and_permanently_deleted(): void
    {
        $event = QcEvent::create([
            'title' => 'Agenda Lama',
            'event_date' => '2026-10-30',
            'event_time' => '09:00:00',
            'created_by' => 'Anggota Uji',
        ]);

        $this->put(route('qc.update', $event), [
            'title' => 'Agenda Diperbarui',
            'event_date' => '2026-10-31',
            'event_time' => '10:30',
        ])->assertRedirect(route('inventory.jadwal', ['date' => '2026-10-31']))
            ->assertSessionHas('success', 'Agenda QC berhasil diperbarui!');

        $this->assertDatabaseHas('qc_events', [
            'id' => $event->id,
            'title' => 'Agenda Diperbarui',
            'event_date' => '2026-10-31',
            'event_time' => '10:30',
        ]);

        $this->delete(route('qc.destroy', $event))
            ->assertRedirect(route('inventory.jadwal', ['date' => '2026-10-31']))
            ->assertSessionHas('success', 'Agenda QC berhasil dihapus.');

        $this->assertDatabaseMissing('qc_events', ['id' => $event->id]);
        $this->assertDatabaseCount('deleted_items', 0);
    }

    public function test_prayer_schedule_endpoint_uses_the_requested_calendar_date(): void
    {
        Cache::forget('prayer-times-karawang-2026-10-30');
        Http::fake([
            'api.aladhan.com/*' => Http::response([
                'code' => 200,
                'data' => [
                    'timings' => [
                        'Imsak' => '04:10 (+07)',
                        'Fajr' => '04:20 (+07)',
                        'Dhuhr' => '11:45 (+07)',
                        'Asr' => '15:10 (+07)',
                        'Maghrib' => '17:50 (+07)',
                        'Isha' => '19:00 (+07)',
                    ],
                    'date' => ['hijri' => ['date' => '18-04-1448']],
                ],
            ]),
        ]);

        $response = $this->getJson(route('inventory.prayer-schedule', ['date' => '2026-10-30']));

        $response->assertOk()
            ->assertJsonPath('date', '2026-10-30')
            ->assertJsonPath('times.Dzuhur', '11:45')
            ->assertJsonPath('hijri_date', '18-04-1448');
        Http::assertSent(fn ($request) => str_contains($request->url(), '/timings/30-10-2026'));
    }

    public function test_update_item_redirects_to_updated_detail_with_success_message(): void
    {
        $item = Item::create([
            'nama_barang' => 'Laptop Lama',
            'no_seri' => 'SN-010',
            'barcode' => 'BC-010',
            'jumlah' => 2,
            'keterangan' => 'Awal',
            'tanggal' => '2026-10-06',
            'status' => 'Belum dicek',
        ]);

        $response = $this->from(route('inventory.show', $item->id))
            ->put(route('inventory.update', $item->id), [
                'nama_barang' => 'Laptop Baru',
                'no_seri' => 'SN-010',
                'jumlah' => 4,
                'tanggal' => '2026-10-06',
                'status' => 'Siap Kirim',
                'keterangan' => 'Sudah diperbarui',
            ]);

        $response->assertRedirect(route('inventory.show', $item->id));
        $response->assertSessionHas('success', 'Data barang berhasil diperbarui!');
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'nama_barang' => 'Laptop Baru',
            'jumlah' => 4,
            'keterangan' => 'Sudah diperbarui',
        ]);
    }

    public function test_item_detail_shows_last_updated_at_in_western_indonesia_time(): void
    {
        $item = Item::create([
            'nama_barang' => 'Barang Bertanggal',
            'no_seri' => 'SN-DATE-001',
            'jumlah' => 1,
            'tanggal' => '2026-10-06',
            'status' => 'Belum dicek',
        ]);
        $item->forceFill([
            'created_at' => Carbon::parse('2026-10-06 07:52:32', 'UTC'),
            'updated_at' => Carbon::parse('2026-10-06 07:52:32', 'UTC'),
        ])->saveQuietly();

        $response = $this->get(route('inventory.show', $item->id));

        $response->assertOk();
        $response->assertSee('Terakhir diperbarui:');
        $response->assertSee('06-10-2026 14:52:32 WIB');
        $response->assertSee('value="2026-10-06"', false);
    }

    public function test_deleted_item_can_be_restored_to_inventory(): void
    {
        $deletedItem = DeletedItem::create([
            'nama_barang' => 'Barang Pulih',
            'no_seri' => 'SN-RESTORE-001',
            'barcode' => 'BC-RESTORE-001',
            'jumlah' => 2,
            'keterangan' => 'Catatan pemulihan',
            'tanggal' => '2026-10-06',
            'status' => 'Siap Kirim',
        ]);

        $this->get(route('inventory.trash'))
            ->assertOk()
            ->assertSee(route('inventory.restore', $deletedItem->id))
            ->assertSee('Pulihkan');

        $response = $this->post(route('inventory.restore', $deletedItem->id));

        $response->assertRedirect(route('inventory.trash'));
        $response->assertSessionHas('success', 'Data barang berhasil dipulihkan!');
        $this->assertDatabaseHas('items', [
            'nama_barang' => 'Barang Pulih',
            'no_seri' => 'SN-RESTORE-001',
            'barcode' => 'BC-RESTORE-001',
            'jumlah' => 2,
            'status' => 'Siap Kirim',
        ]);
        $this->assertDatabaseMissing('deleted_items', ['id' => $deletedItem->id]);
    }

    public function test_restore_keeps_deleted_item_when_serial_number_is_already_used(): void
    {
        Item::create([
            'nama_barang' => 'Barang Aktif',
            'no_seri' => 'SN-DUPLICATE-001',
            'jumlah' => 1,
            'tanggal' => '2026-10-06',
            'status' => 'Belum dicek',
        ]);
        $deletedItem = DeletedItem::create([
            'nama_barang' => 'Barang Terhapus',
            'no_seri' => 'SN-DUPLICATE-001',
            'jumlah' => 2,
            'tanggal' => '2026-10-06',
            'status' => 'Belum dicek',
        ]);

        $response = $this->post(route('inventory.restore', $deletedItem->id));

        $response->assertRedirect(route('inventory.trash'));
        $response->assertSessionHas('error', 'Barang tidak dapat dipulihkan karena nomor seri tersebut sudah digunakan.');
        $this->assertDatabaseHas('deleted_items', ['id' => $deletedItem->id]);
        $this->assertDatabaseCount('items', 1);
    }
}
