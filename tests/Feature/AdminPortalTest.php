<?php

namespace Tests\Feature;

use App\Models\Laundry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $owner;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createUser('Admin Test', 'admin@test.example', 'admin', 'active', '081111111101');
        $this->owner = $this->createUser('Owner Test', 'owner@test.example', 'owner', 'pending', '081111111102');
        $this->customer = $this->createUser('Customer Test', 'customer@test.example', 'user', 'active', '081111111103');

        Laundry::create([
            'user_id' => $this->owner->id,
            'store_name' => 'Laundry Pending',
            'store_phone' => '081111111104',
            'address' => 'Jl. Admin No. 1',
        ]);
    }

    private function createUser(string $name, string $email, string $role, string $status, string $phone): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'address' => 'Jl. Test No. 1',
            'phone' => $phone,
            'password' => 'password123',
            'role' => $role,
            'status' => $status,
        ]);
    }

    public function test_admin_can_open_all_management_sections(): void
    {
        $this->actingAs($this->admin);

        foreach (['dashboard', 'applications', 'accounts', 'catalog', 'transactions', 'reviews', 'messages', 'schedules', 'topups'] as $section) {
            $this->get(route('admin.'.$section))->assertOk();
        }

        $this->get(route('admin.applications'))->assertSee('Laundry Pending');
    }

    public function test_non_admin_cannot_open_admin_dashboard(): void
    {
        $this->actingAs($this->customer)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($this->owner)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $this->post(route('login'), [
            'email' => $this->admin->email,
            'password' => 'password123',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_edit_and_delete_customer_and_owner_accounts(): void
    {
        $this->actingAs($this->admin);

        $this->put(route('admin.accounts.update', $this->customer->id), [
            'name' => 'Customer Edited',
            'email' => $this->customer->email,
            'phone' => $this->customer->phone,
            'address' => 'Jl. Baru No. 2',
            'role' => 'user',
            'status' => 'active',
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $this->customer->id, 'name' => 'Customer Edited']);

        $this->delete(route('admin.accounts.delete', $this->owner->id))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $this->owner->id]);
        $this->assertDatabaseMissing('laundries', ['user_id' => $this->owner->id]);
    }

    public function test_admin_approval_processes_topup_and_updates_wallet_once(): void
    {
        $this->actingAs($this->admin);
        $topup = DB::table('user_wallet_topups')->insertGetId([
            'user_id' => $this->customer->id,
            'amount' => 40000,
            'payment_method' => 'qris',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post(route('admin.topups.approve', $topup))->assertRedirect();
        $this->assertDatabaseHas('user_wallets', ['user_id' => $this->customer->id, 'balance' => 40000]);
        $this->assertDatabaseHas('user_wallet_topups', ['id' => $topup, 'status' => 'approved']);
        $this->post(route('admin.topups.approve', $topup))->assertNotFound();
        $this->assertDatabaseHas('user_wallets', ['user_id' => $this->customer->id, 'balance' => 40000]);
    }
}
