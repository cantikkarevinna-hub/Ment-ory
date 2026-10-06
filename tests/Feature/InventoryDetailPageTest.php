<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\QcEvent;
use App\Models\DeletedItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InventoryDetailPageTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_calendar_page_loads_with_qc_events(): void
    {
        $today = now('Asia/Jakarta');
        Cache::forget('prayer-times-karawang-' . $today->toDateString());
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
        $response->assertSee('11:57 WIB');
        $response->assertSee('14-04-1448');

        $dashboard = $this->get(route('inventory.index'));
        $dashboard->assertOk();
        $dashboard->assertSee('11:57');
        Http::assertSent(fn ($request) => str_contains($request->url(), 'api.aladhan.com/v1/timings/'));
    }

    public function test_update_item_redirects_to_data_page_with_success_message(): void
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

        $response->assertRedirect(route('inventory.data'));
        $response->assertSessionHas('success', 'Data barang berhasil diperbarui!');
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
