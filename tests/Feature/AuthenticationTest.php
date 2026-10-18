<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_and_can_view_registration(): void
    {
        $this->get(route('inventory.index'))->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk()->assertSee('Masuk ke akun');
        $this->get(route('register'))->assertOk()->assertSee('Daftar akun anggota');
    }

    public function test_registration_creates_member_account_and_logs_them_in(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Anggota Baru',
            'email' => 'anggota@example.test',
            'password' => 'password-anggota-123',
            'password_confirmation' => 'password-anggota-123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('inventory.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Anggota Baru',
            'email' => 'anggota@example.test',
            'role' => 'member',
        ]);
        $this->assertNotNull(User::where('email', 'anggota@example.test')->firstOrFail()->last_login_at);
        $this->assertDatabaseMissing('users', ['role' => 'admin']);
    }

    public function test_member_cannot_open_admin_dashboard(): void
    {
        $member = User::create([
            'name' => 'Anggota',
            'email' => 'member@example.test',
            'password' => 'password-anggota-123',
            'role' => 'member',
        ]);

        $this->actingAs($member)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_member_login_does_not_redirect_to_an_intended_admin_page(): void
    {
        User::create([
            'name' => 'Anggota',
            'email' => 'member@example.test',
            'password' => 'password-member-123',
            'role' => 'member',
        ]);

        $this->withSession(['url.intended' => route('admin.dashboard')])
            ->post(route('login.store'), [
                'email' => 'member@example.test',
                'password' => 'password-member-123',
            ])
            ->assertRedirect(route('inventory.index'));
    }

    public function test_admin_can_monitor_registered_accounts_and_inventory(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password-admin-123',
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Anggota Terdaftar',
            'email' => 'member@example.test',
            'password' => 'password-member-123',
            'role' => 'member',
        ]);

        $response = $this->actingAs(User::where('role', 'admin')->first())
            ->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Panel Admin QC')
            ->assertSeeInOrder(['AKUN TERDAFTAR', 'ADMIN', 'ANGGOTA', 'AGENDA QC'])
            ->assertSee('href="'.route('inventory.jadwal').'"', false)
            ->assertSee('href="'.route('admin.dashboard').'"', false)
            ->assertSee('href="'.route('admin.dashboard', ['role' => 'admin']).'"', false)
            ->assertSee('href="'.route('admin.dashboard', ['role' => 'member']).'"', false)
            ->assertSee('2', false)
            ->assertSee('1', false)
            ->assertSee('admin-filter-card')
            ->assertSee('Admin')
            ->assertSee('Akun yang terdaftar')
            ->assertSee('Anggota Terdaftar')
            ->assertSee('member@example.test');
        $this->assertStringNotContainsString('>Administrator</span>', $response->getContent());
    }

    public function test_admin_account_timestamps_are_displayed_in_western_indonesia_time(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password-admin-123',
            'role' => 'admin',
        ]);
        $member = User::create([
            'name' => 'Anggota',
            'email' => 'member@example.test',
            'password' => 'password-member-123',
            'role' => 'member',
        ]);
        $member->forceFill([
            'created_at' => Carbon::parse('2026-10-18 03:40:00', 'UTC'),
            'last_login_at' => Carbon::parse('2026-10-18 03:57:00', 'UTC'),
        ])->saveQuietly();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('18-10-2026 10:40 WIB')
            ->assertSee('18-10-2026 10:57 WIB');
    }

    public function test_admin_summary_card_filters_the_account_table_to_admins(): void
    {
        User::create([
            'name' => 'Admin Lain',
            'email' => 'admin-lain@example.test',
            'password' => 'password-admin-123',
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Anggota Terdaftar',
            'email' => 'member-terdaftar@example.test',
            'password' => 'password-member-123',
            'role' => 'member',
        ]);

        $response = $this->actingAs(User::where('role', 'admin')->first())
            ->get(route('admin.dashboard', ['role' => 'admin']));

        $response->assertOk()
            ->assertSee('Akun admin')
            ->assertSee('Admin Lain')
            ->assertSee('admin-lain@example.test')
            ->assertSee('Lihat semua akun')
            ->assertDontSee('member-terdaftar@example.test');
    }

    public function test_member_summary_card_filters_the_account_table_to_members(): void
    {
        User::create([
            'name' => 'Admin Lain',
            'email' => 'admin-lain@example.test',
            'password' => 'password-admin-123',
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Anggota Terdaftar',
            'email' => 'member-terdaftar@example.test',
            'password' => 'password-member-123',
            'role' => 'member',
        ]);

        $response = $this->actingAs(User::where('role', 'admin')->first())
            ->get(route('admin.dashboard', ['role' => 'member']));

        $response->assertOk()
            ->assertSee('Akun anggota')
            ->assertSee('Anggota Terdaftar')
            ->assertSee('member-terdaftar@example.test')
            ->assertSee('Lihat semua akun')
            ->assertDontSee('admin-lain@example.test');
    }

    public function test_login_and_logout_work(): void
    {
        User::create([
            'name' => 'Anggota',
            'email' => 'member@example.test',
            'password' => 'password-member-123',
            'role' => 'member',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'member@example.test',
            'password' => 'password-member-123',
        ]);
        $response->assertRedirect(route('inventory.index'));
        $this->assertAuthenticated();

        $this->post(route('logout'))
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
