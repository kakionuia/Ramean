<?php

namespace Tests\Feature;

use App\Models\Laundry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserStorefrontTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private Laundry $laundry;

    private int $serviceId;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::create([
            'name' => 'Laundry Owner',
            'email' => 'laundry-owner@example.com',
            'address' => 'Jl. Pemilik, Cibinong',
            'phone' => '081100000001',
            'password' => 'password123',
            'role' => 'owner',
            'status' => 'active',
        ]);
        $this->laundry = Laundry::create([
            'user_id' => $owner->id,
            'store_name' => 'Laundry Segar',
            'store_phone' => '081100000002',
            'address' => 'Jl. Mawar, Cibinong, Bogor',
            'description' => 'Cuci bersih dekat rumah.',
        ]);
        $this->customer = User::create([
            'name' => 'Pelanggan Test',
            'email' => 'customer@example.com',
            'address' => 'Jl. Pelanggan, Cibinong',
            'phone' => null,
            'password' => 'password123',
            'role' => 'user',
            'status' => 'active',
        ]);
        $this->serviceId = DB::table('owner_services')->insertGetId([
            'laundry_id' => $this->laundry->id,
            'name' => 'Cuci Kiloan',
            'description' => 'Cuci dan kering.',
            'price' => 8000,
            'original_price' => 10000,
            'promo_label' => 'Hemat 20%',
            'promo_until' => now()->addDays(7)->toDateString(),
            'duration_hours' => 24,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_customer_can_browse_home_and_laundry_detail(): void
    {
        $this->actingAs($this->customer);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Laundry Segar')
            ->assertSee('Hemat 20%')
            ->assertSee('Cuci Kiloan');

        $this->get(route('dashboard', ['q' => 'Cibinong']))
            ->assertOk()
            ->assertSee('Laundry Segar');

        $this->get(route('laundry.show', $this->laundry->id))
            ->assertOk()
            ->assertSee('Detail cucianmu')
            ->assertSee('Pakaian kiloan');
    }

    public function test_owner_cannot_open_customer_home(): void
    {
        $owner = User::where('role', 'owner')->firstOrFail();

        $this->actingAs($owner)->get(route('dashboard'))->assertForbidden();
    }

    public function test_pending_owner_laundry_is_not_visible_to_customers(): void
    {
        $pendingOwner = User::create([
            'name' => 'Owner Pending',
            'email' => 'pending-owner@example.com',
            'address' => 'Jl. Pending, Cibinong',
            'phone' => '081100000099',
            'password' => 'password123',
            'role' => 'owner',
            'status' => 'pending',
        ]);
        Laundry::create([
            'user_id' => $pendingOwner->id,
            'store_name' => 'Laundry Belum Disetujui',
            'store_phone' => '081100000098',
            'address' => 'Jl. Pending, Cibinong',
        ]);

        $this->actingAs($this->customer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Laundry Belum Disetujui');
    }

    public function test_customer_can_order_service_and_see_it_in_profile_history(): void
    {
        $this->actingAs($this->customer);
        $this->post(route('orders.store', $this->laundry->id), [
            'service_id' => $this->serviceId,
            'garment_type' => 'kiloan',
            'wash_type' => 'cuci_setrika',
            'delivery_method' => 'dropoff',
            'notes' => 'Pisahkan pakaian putih',
        ])->assertRedirect(route('profile', ['tab' => 'orders']));

        $this->assertDatabaseHas('laundry_transactions', [
            'laundry_id' => $this->laundry->id,
            'user_id' => $this->customer->id,
            'service_id' => $this->serviceId,
            'total_amount' => 0,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => null,
            'delivery_method' => 'dropoff',
            'delivery_status' => 'waiting',
        ]);

        $this->get(route('profile', ['tab' => 'orders']))
            ->assertOk()
            ->assertSee('Laundry Segar')
            ->assertSee('Pakaian kiloan')
            ->assertSee('Pisahkan pakaian putih');

        $orderId = DB::table('laundry_transactions')->where('user_id', $this->customer->id)->value('id');
        $this->get(route('orders.show', $orderId))
            ->assertOk()
            ->assertSee('STATUS CUCIAN')
            ->assertSee('Menunggu ditimbang laundry')
            ->assertSee('Tagihan belum tersedia');

        $otherCustomer = User::create([
            'name' => 'Pelanggan Lain',
            'email' => 'other-customer@example.com',
            'address' => 'Jl. Lain, Cibinong',
            'phone' => null,
            'password' => 'password123',
            'role' => 'user',
            'status' => 'active',
        ]);
        $this->actingAs($otherCustomer)->get(route('orders.show', $orderId))->assertNotFound();
    }

    public function test_customer_can_confirm_simulated_qris_order_once(): void
    {
        DB::table('owner_services')->where('id', $this->serviceId)->update(['price' => 10000]);
        $this->actingAs($this->customer);
        $this->post(route('orders.store', $this->laundry->id), [
            'service_id' => $this->serviceId,
            'garment_type' => 'pakaian',
            'wash_type' => 'cuci',
            'delivery_method' => 'dropoff',
        ])->assertRedirect(route('profile', ['tab' => 'orders']));

        $order = DB::table('laundry_transactions')->where('user_id', $this->customer->id)->first();
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertNull($order->payment_method);
        $this->assertNull($order->payment_reference);

        $owner = User::where('email', 'laundry-owner@example.com')->firstOrFail();
        $this->actingAs($owner)->post(route('owner.transactions.delivery-status', $order->id), ['delivery_status' => 'received'])->assertRedirect();
        $this->post(route('owner.transactions.weigh', $order->id), ['weight_kg' => 3.5])->assertRedirect();
        $weighedOrder = DB::table('laundry_transactions')->where('id', $order->id)->first();
        $this->assertEquals(3.5, (float) $weighedOrder->weight_kg);
        $this->assertEquals(35000, (float) $weighedOrder->total_amount);
        $this->assertNotNull($weighedOrder->weighed_at);

        $this->actingAs($this->customer)->get(route('orders.show', $order->id))->assertOk()->assertSee('3,50 kg')->assertSee('Rp 35.000');
        $this->post(route('orders.pay', $order->id), ['payment_method' => 'qris'])->assertRedirect(route('orders.show', $order->id));
        $order = DB::table('laundry_transactions')->where('id', $order->id)->first();
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame('qris', $order->payment_method);
        $this->assertNotEmpty($order->payment_reference);

        $this->get(route('orders.show', $order->id))->assertOk()->assertSee('Bayar sekarang')->assertSee($order->payment_reference);
        $this->post(route('orders.confirm-qris', $order->id))->assertRedirect(route('profile', ['tab' => 'orders']));
        $this->assertDatabaseHas('laundry_transactions', ['id' => $order->id, 'payment_status' => 'paid']);
        $this->post(route('orders.confirm-qris', $order->id))->assertNotFound();

        $owner = User::where('email', 'laundry-owner@example.com')->firstOrFail();
        $this->actingAs($owner)->post(route('owner.transactions.status', $order->id), ['status' => 'processing'])->assertRedirect();
        $this->post(route('owner.transactions.status', $order->id), ['status' => 'completed'])->assertRedirect();
        $this->post(route('owner.transactions.delivery-status', $order->id), ['delivery_status' => 'returned'])->assertRedirect();
        $this->actingAs($this->customer)->get(route('orders.show', $order->id))
            ->assertOk()
            ->assertSee('Sudah dikembalikan / siap diambil');
    }

    public function test_order_cannot_be_paid_from_insufficient_wallet_balance(): void
    {
        $this->actingAs($this->customer);
        DB::table('user_wallets')->insert([
            'user_id' => $this->customer->id,
            'balance' => 1000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post(route('orders.store', $this->laundry->id), [
            'service_id' => $this->serviceId,
            'garment_type' => 'kiloan',
            'wash_type' => 'cuci',
            'delivery_method' => 'dropoff',
        ])->assertRedirect(route('profile', ['tab' => 'orders']));

        $orderId = (int) DB::table('laundry_transactions')->where('user_id', $this->customer->id)->value('id');
        $owner = User::where('email', 'laundry-owner@example.com')->firstOrFail();
        $this->actingAs($owner)->post(route('owner.transactions.delivery-status', $orderId), ['delivery_status' => 'received'])->assertRedirect();
        $this->post(route('owner.transactions.weigh', $orderId), ['weight_kg' => 2])->assertRedirect();
        $this->actingAs($this->customer)->from(route('orders.show', $orderId))
            ->post(route('orders.pay', $orderId), ['payment_method' => 'wallet'])
            ->assertRedirect(route('orders.show', $orderId))
            ->assertSessionHasErrors('payment_method');

        $this->assertDatabaseHas('laundry_transactions', ['id' => $orderId, 'payment_status' => 'unpaid', 'total_amount' => 16000]);
        $this->assertDatabaseHas('user_wallets', ['user_id' => $this->customer->id, 'balance' => 1000]);
    }

    public function test_customer_can_pay_final_weighed_invoice_from_wallet(): void
    {
        $this->actingAs($this->customer)->post(route('orders.store', $this->laundry->id), [
            'service_id' => $this->serviceId,
            'garment_type' => 'kiloan',
            'wash_type' => 'cuci_setrika',
            'delivery_method' => 'dropoff',
        ])->assertRedirect();

        $orderId = (int) DB::table('laundry_transactions')->where('user_id', $this->customer->id)->value('id');
        $owner = User::where('email', 'laundry-owner@example.com')->firstOrFail();
        $this->actingAs($owner)->post(route('owner.transactions.delivery-status', $orderId), ['delivery_status' => 'received'])->assertRedirect();
        $this->post(route('owner.transactions.weigh', $orderId), ['weight_kg' => 2])->assertRedirect();

        DB::table('user_wallets')->insert([
            'user_id' => $this->customer->id,
            'balance' => 20000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs($this->customer)->post(route('orders.pay', $orderId), ['payment_method' => 'wallet'])
            ->assertRedirect(route('orders.show', $orderId));

        $this->assertDatabaseHas('laundry_transactions', [
            'id' => $orderId,
            'total_amount' => 16000,
            'payment_method' => 'wallet',
            'payment_status' => 'paid',
        ]);
        $this->assertDatabaseHas('user_wallets', ['user_id' => $this->customer->id, 'balance' => 4000]);
    }

    public function test_order_chat_connects_customer_and_owning_laundry(): void
    {
        $this->actingAs($this->customer);
        $this->post(route('orders.store', $this->laundry->id), [
            'service_id' => $this->serviceId,
            'garment_type' => 'kiloan',
            'wash_type' => 'cuci',
            'delivery_method' => 'pickup',
        ])->assertRedirect();

        $orderId = (int) DB::table('laundry_transactions')->where('user_id', $this->customer->id)->value('id');
        $this->post(route('orders.messages.store', $orderId), [
            'message' => 'Kapan cucian saya selesai?',
        ])->assertRedirect(route('orders.show', $orderId));

        $owner = User::where('email', 'laundry-owner@example.com')->firstOrFail();
        $this->actingAs($owner)
            ->get(route('owner.chat', ['customer' => $this->customer->name, 'user_id' => $this->customer->id]))
            ->assertOk()
            ->assertSee('Kapan cucian saya selesai?');

        $this->postJson(route('owner.chat.reply'), [
            'customer_name' => $this->customer->name,
            'user_id' => $this->customer->id,
            'message' => 'Cucian sedang diproses.',
        ])->assertCreated()->assertJsonPath('message', 'Cucian sedang diproses.');

        $this->actingAs($this->customer)
            ->getJson(route('orders.messages', ['transaction' => $orderId, 'after' => 1]))
            ->assertOk()
            ->assertJsonPath('messages.0.message', 'Cucian sedang diproses.');

        $this->get(route('orders.show', $orderId))->assertSee('Cucian sedang diproses.');
    }

    public function test_top_up_request_stays_pending_until_payment_is_confirmed(): void
    {
        $this->actingAs($this->customer);

        $this->post(route('profile.topups.store'), [
            'amount' => 50000,
            'payment_method' => 'qris',
        ])->assertRedirect(route('profile', ['tab' => 'wallet']));

        $this->assertDatabaseHas('user_wallet_topups', [
            'user_id' => $this->customer->id,
            'amount' => 50000,
            'payment_method' => 'qris',
            'status' => 'pending',
        ]);

        $topup = DB::table('user_wallet_topups')->where('user_id', $this->customer->id)->first();
        $this->assertNotEmpty($topup->payment_reference);

        $this->get(route('profile', ['tab' => 'wallet']))
            ->assertOk()
            ->assertSee('Rp 50.000')
            ->assertSee('Pending')
            ->assertSee('QRIS simulasi')
            ->assertSee($topup->payment_reference);
    }
}
