<?php

namespace Tests\Feature;

use App\Models\Laundry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerPortalTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'name' => 'Owner Test',
            'email' => 'owner@example.com',
            'address' => 'Jl. Contoh No. 1',
            'domisili' => 'Bogor',
            'sub_domisili' => 'kabupaten',
            'kecamatan' => 'Cibinong',
            'wilayah_akhir' => 'Cirimekar',
            'phone' => '081234567890',
            'password' => 'password123',
            'role' => 'owner',
            'status' => 'active',
        ]);

        Laundry::create([
            'user_id' => $this->owner->id,
            'store_name' => 'Laundry Test',
            'store_phone' => '081234567891',
            'address' => 'Jl. Contoh No. 1',
        ]);
    }

    public function test_owner_can_open_all_dashboard_sections(): void
    {
        $this->actingAs($this->owner);

        foreach (['dashboard', 'transactions', 'services', 'finance', 'reviews', 'chat', 'settings'] as $section) {
            $this->get(route('owner.'.$section))->assertOk();
        }
    }

    public function test_owner_registration_creates_laundry_and_login_opens_owner_dashboard(): void
    {
        $this->get(route('register-owner'))
            ->assertOk()
            ->assertSee('name="domisili"', false)
            ->assertSee('name="sub_domisili"', false)
            ->assertSee('name="kecamatan"', false)
            ->assertSee('name="wilayah_akhir"', false);

        $registrationResponse = $this->post(route('register.owner'), [
            'name' => 'Laundry Baru Owner',
            'email' => 'new-owner@example.com',
            'phone' => '081234567899',
            'domisili' => 'Bogor',
            'sub_domisili' => 'Kota Bogor',
            'kecamatan' => 'Bogor Tengah',
            'wilayah_akhir' => 'Babakan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'store_name' => 'Laundry Baru',
            'store_phone' => '081234567898',
            'address' => 'Jl. Laundry No. 2',
            'description' => 'Cuci kiloan dan satuan',
        ])->assertRedirect(route('login'));

        $newOwner = User::where('email', 'new-owner@example.com')->firstOrFail();
        $this->assertSame('Bogor', $newOwner->domisili);
        $this->assertSame('Kota Bogor', $newOwner->sub_domisili);
        $this->assertSame('Bogor Tengah', $newOwner->kecamatan);
        $this->assertSame('Babakan', $newOwner->wilayah_akhir);
        $this->assertDatabaseHas('laundries', [
            'user_id' => $newOwner->id,
            'store_name' => 'Laundry Baru',
        ]);

        $this->post(route('login'), [
            'identity' => 'new-owner@example.com',
            'password' => 'password123',
        ])->assertSessionHasErrors('identity');
        $this->assertGuest();

        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-test@example.com',
            'address' => 'Kantor Admin',
            'phone' => '081234567800',
            'password' => 'password123',
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin)->post(route('admin.applications.approve', $newOwner->id))->assertRedirect();

        auth()->logout();
        $this->post(route('login'), [
            'identity' => 'new-owner@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('owner.dashboard'));
    }

    public function test_owner_can_create_update_and_delete_a_service(): void
    {
        $this->actingAs($this->owner);

        $this->post(route('owner.services.store'), [
            'name' => 'Cuci Kering',
            'description' => 'Cuci dan kering per kilogram',
            'price' => 8000,
            'duration_hours' => 24,
            'is_active' => '1',
        ])->assertRedirect();

        $serviceId = (int) \DB::table('owner_services')->value('id');
        $this->assertDatabaseHas('owner_services', ['id' => $serviceId, 'name' => 'Cuci Kering']);

        $this->put(route('owner.services.update', $serviceId), [
            'name' => 'Cuci Express',
            'description' => 'Selesai hari ini',
            'price' => 12000,
            'duration_hours' => 8,
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('owner_services', ['id' => $serviceId, 'name' => 'Cuci Express']);

        $this->delete(route('owner.services.delete', $serviceId))->assertRedirect();
        $this->assertDatabaseMissing('owner_services', ['id' => $serviceId]);
    }

    public function test_owner_can_review_cucian_details_and_update_paid_order_progress(): void
    {
        $laundryId = \DB::table('laundries')->where('user_id', $this->owner->id)->value('id');
        $orderId = \DB::table('laundry_transactions')->insertGetId([
            'laundry_id' => $laundryId,
            'user_id' => null,
            'customer_name' => 'Pelanggan Cucian',
            'service_name' => 'Cuci Kiloan · Cuci dan setrika',
            'order_details' => 'Jenis cucian: Pakaian kiloan | Catatan: pisahkan putih',
            'quantity' => 1,
            'total_amount' => 0,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'delivery_method' => 'pickup',
            'delivery_status' => 'waiting',
            'pickup_fee' => 5000,
            'service_rate' => 10000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs($this->owner);

        $this->get(route('owner.transactions.show', $orderId))
            ->assertOk()
            ->assertSee('Pelanggan Cucian')
            ->assertSee('Jenis cucian: Pakaian kiloan');

        $this->post(route('owner.transactions.delivery-status', $orderId), ['delivery_status' => 'picked_up'])->assertRedirect();
        $this->post(route('owner.transactions.delivery-status', $orderId), ['delivery_status' => 'received'])->assertRedirect();
        $this->post(route('owner.transactions.weigh', $orderId), ['weight_kg' => 3.5])->assertRedirect();
        $this->assertDatabaseHas('laundry_transactions', [
            'id' => $orderId,
            'weight_kg' => 3.5,
            'pickup_fee' => 5000,
            'total_amount' => 40000,
            'payment_status' => 'unpaid',
        ]);

        \DB::table('laundry_transactions')->where('id', $orderId)->update(['payment_status' => 'paid', 'paid_at' => now()]);
        $this->post(route('owner.transactions.status', $orderId), ['status' => 'processing'])->assertRedirect();
        $this->assertDatabaseHas('laundry_transactions', ['id' => $orderId, 'status' => 'processing']);

        $this->post(route('owner.transactions.status', $orderId), ['status' => 'completed'])->assertRedirect();
        $this->assertDatabaseHas('laundry_transactions', ['id' => $orderId, 'status' => 'completed']);
        $this->post(route('owner.transactions.delivery-status', $orderId), ['delivery_status' => 'returned'])->assertRedirect();
        $this->assertDatabaseHas('laundry_transactions', ['id' => $orderId, 'delivery_status' => 'returned']);

        $this->post(route('owner.transactions.status', $orderId), ['status' => 'processing'])->assertStatus(422);
    }

    public function test_owner_cannot_start_unpaid_cucian(): void
    {
        $laundryId = \DB::table('laundries')->where('user_id', $this->owner->id)->value('id');
        $orderId = \DB::table('laundry_transactions')->insertGetId([
            'laundry_id' => $laundryId,
            'customer_name' => 'Pelanggan Belum Bayar',
            'service_name' => 'Cuci Kiloan · Cuci',
            'quantity' => 1,
            'total_amount' => 8000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'qris',
            'payment_reference' => 'ORD-UNPAID-TEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->owner)
            ->post(route('owner.transactions.status', $orderId), ['status' => 'processing'])
            ->assertStatus(422);
        $this->assertDatabaseHas('laundry_transactions', ['id' => $orderId, 'status' => 'pending']);
    }

    public function test_owner_cannot_view_another_laundrys_order_detail(): void
    {
        $otherOwner = User::create([
            'name' => 'Owner Lain',
            'email' => 'other-owner@example.com',
            'address' => 'Jl. Lain',
            'phone' => '081234567899',
            'password' => 'password123',
            'role' => 'owner',
            'status' => 'active',
        ]);
        $otherLaundry = Laundry::create([
            'user_id' => $otherOwner->id,
            'store_name' => 'Laundry Lain',
            'store_phone' => '081234567898',
            'address' => 'Jl. Lain',
        ]);
        $orderId = \DB::table('laundry_transactions')->insertGetId([
            'laundry_id' => $otherLaundry->id,
            'customer_name' => 'Pelanggan Laundry Lain',
            'service_name' => 'Cuci',
            'quantity' => 1,
            'total_amount' => 8000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->owner)->get(route('owner.transactions.show', $orderId))->assertNotFound();
    }
}
