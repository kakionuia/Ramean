<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    private function customer(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'user' && Auth::user()->status === 'active', 403);
    }

    public function home(Request $request)
    {
        $this->customer();

        $orderStats = DB::table('laundry_transactions')
            ->where('user_id', Auth::id())
            ->selectRaw("COUNT(CASE WHEN status IN ('pending', 'processing') THEN 1 END) as active, COUNT(CASE WHEN status = 'processing' THEN 1 END) as processing, COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed")
            ->first();

        $orders = DB::table('laundry_transactions')
            ->join('laundries', 'laundries.id', '=', 'laundry_transactions.laundry_id')
            ->where('laundry_transactions.user_id', Auth::id())
            ->select('laundry_transactions.*', 'laundries.store_name')
            ->latest('laundry_transactions.created_at');

        $recentOrders = (clone $orders)->limit(5)->get();
        $activeOrder = (clone $orders)->whereIn('laundry_transactions.status', ['pending', 'processing'])->first()
            ?? (clone $orders)->where('laundry_transactions.status', 'completed')->first();
        $notifications = (clone $orders)->reorder()
            ->orderByRaw("CASE WHEN laundry_transactions.delivery_status = 'ready' THEN 0 ELSE 1 END")
            ->latest('laundry_transactions.created_at')
            ->limit(3)->get();
        $readyOrders = (clone $orders)->where('laundry_transactions.delivery_status', 'ready')->limit(5)->get();

        $homePromo = DB::table('owner_services')
            ->join('laundries', 'laundries.id', '=', 'owner_services.laundry_id')
            ->join('users as owners', 'owners.id', '=', 'laundries.user_id')
            ->where('owners.role', 'owner')
            ->where('owners.status', 'active')
            ->where('owner_services.is_active', true)
            ->whereNotNull('owner_services.original_price')
            ->whereColumn('owner_services.original_price', '>', 'owner_services.price')
            ->where(function ($query) {
                $query->whereNull('owner_services.promo_until')
                    ->orWhereDate('owner_services.promo_until', '>=', today());
            })
            ->select(
                'owner_services.id as service_id',
                'laundries.id as laundry_id',
                'owner_services.name',
                'owner_services.price',
                'owner_services.original_price',
                'owner_services.promo_label',
                'laundries.store_name'
            )
            ->latest('owner_services.updated_at')
            ->first();

        return view('home', compact('recentOrders', 'activeOrder', 'orderStats', 'notifications', 'readyOrders', 'homePromo'));
    }

    public function discover(Request $request)
    {
        $this->customer();

        $validated = $request->validate([
            'area' => ['nullable', 'string', 'max:120'],
            'sort' => ['nullable', 'in:recommended,price,rating'],
        ]);
        $area = trim($validated['area'] ?? '');
        $sort = $validated['sort'] ?? 'recommended';

        $laundries = DB::table('laundries')
            ->join('users as owners', 'owners.id', '=', 'laundries.user_id')
            ->where('owners.role', 'owner')
            ->where('owners.status', 'active')
            ->where('laundries.operational_status', '!=', 'banned')
            ->select('laundries.*')
            ->selectRaw('COALESCE(laundries.service_district, owners.kecamatan) as kecamatan')
            ->selectRaw('COALESCE(laundries.service_city, owners.sub_domisili, owners.domisili) as domisili')
            ->get()
            ->map(function ($laundry) {
                $laundry->services = DB::table('owner_services')
                    ->where('laundry_id', $laundry->id)
                    ->where('is_active', true)
                    ->orderBy('price')
                    ->get();
                $reviews = DB::table('laundry_reviews')->where('laundry_id', $laundry->id);
                $laundry->rating_average = (float) (clone $reviews)->avg('rating');
                $laundry->rating_count = (int) (clone $reviews)->count();
                $laundry->starting_price = $laundry->services->min('price');
                $laundry->pickup_available = (bool) $laundry->pickup_available;

                return $laundry;
            })
            ->filter(function ($laundry) use ($area) {
                if ($area === '') return true;
                return str_contains(mb_strtolower(implode(' ', array_filter([
                    $laundry->store_name, $laundry->address, $laundry->kecamatan, $laundry->domisili,
                ]))), mb_strtolower($area));
            });

        $laundries = (match ($sort) {
            'price' => $laundries->sortBy(fn ($item) => $item->starting_price ?? PHP_FLOAT_MAX),
            'rating' => $laundries->sortByDesc('rating_average'),
            default => $laundries->sortBy('store_name'),
        })->values();

        return view('user.discover', compact('laundries', 'area', 'sort'));
    }

    public function showLaundry(int $laundry)
    {
        $this->customer();

        $laundry = DB::table('laundries')
            ->join('users as owners', 'owners.id', '=', 'laundries.user_id')
            ->select('laundries.*', 'owners.name as owner_name')
            ->where('laundries.id', $laundry)
            ->where('owners.role', 'owner')
            ->where('owners.status', 'active')
            ->where('laundries.operational_status', '!=', 'banned')
            ->first();
        abort_unless($laundry, 404);

        $services = DB::table('owner_services')->where('laundry_id', $laundry->id)->where('is_active', true)->orderBy('price')->get();
        $wallet = DB::table('user_wallets')->where('user_id', Auth::id())->first();

        return view('user.laundry-detail', [
            'laundry' => $laundry,
            'services' => $services,
            'walletBalance' => $wallet->balance ?? 0,
            'pickupFee' => (float) ($laundry->pickup_fee ?? 0),
        ]);
    }

    public function storeOrder(Request $request, int $laundry)
    {
        $this->customer();
        $validated = $request->validate([
            'service_id' => ['required', 'integer'],
            'garment_type' => ['required', 'in:kiloan,pakaian,selimut,sepatu,lainnya'],
            'wash_type' => ['required', 'in:cuci,setrika,cuci_setrika'],
            'delivery_method' => ['required', 'in:dropoff,pickup'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $service = DB::table('owner_services')
            ->where('id', $validated['service_id'])
            ->where('laundry_id', $laundry)
            ->where('is_active', true)
            ->first();
        abort_unless($service, 404, 'Layanan tidak tersedia.');

        $laundryRecord = DB::table('laundries')
            ->join('users as owners', 'owners.id', '=', 'laundries.user_id')
            ->where('laundries.id', $laundry)
            ->where('owners.role', 'owner')
            ->where('owners.status', 'active')
            ->select('laundries.*')
            ->first();
        abort_unless($laundryRecord, 404);
        if ($laundryRecord->operational_status !== 'open') {
            throw ValidationException::withMessages(['service_id' => 'Outlet sedang tutup sementara dan belum menerima pesanan.']);
        }
        if ($validated['delivery_method'] === 'pickup' && ! $laundryRecord->pickup_available) {
            throw ValidationException::withMessages(['delivery_method' => 'Outlet ini tidak menyediakan layanan antar-jemput.']);
        }

        $washLabels = [
            'cuci' => 'Cuci',
            'setrika' => 'Setrika',
            'cuci_setrika' => 'Cuci dan setrika',
        ];
        $garmentLabels = [
            'kiloan' => 'Pakaian kiloan',
            'pakaian' => 'Pakaian satuan',
            'selimut' => 'Selimut',
            'sepatu' => 'Sepatu',
            'lainnya' => 'Lainnya',
        ];

        $pickupFee = $validated['delivery_method'] === 'pickup' ? (float) $laundryRecord->pickup_fee : 0;
        DB::transaction(function () use ($validated, $service, $laundry, $washLabels, $garmentLabels, $pickupFee) {
            DB::table('laundry_transactions')->insert([
                'laundry_id' => $laundry,
                'user_id' => Auth::id(),
                'service_id' => $service->id,
                'customer_name' => Auth::user()->name,
                'service_name' => $service->name.' · '.$washLabels[$validated['wash_type']],
                'order_details' => implode(' | ', array_filter([
                    'Jenis cucian: '.$garmentLabels[$validated['garment_type']],
                    'Jenis layanan: '.$washLabels[$validated['wash_type']],
                    'Pengantaran: '.($validated['delivery_method'] === 'pickup' ? 'Antar-jemput' : 'Drop-off ke laundry'),
                    'Catatan: '.($validated['notes'] ?? ''),
                ])),
                'quantity' => 1,
                'total_amount' => 0,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => null,
                'payment_reference' => null,
                'delivery_method' => $validated['delivery_method'],
                'pickup_fee' => $pickupFee,
                'service_rate' => $service->price,
                'delivery_status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('profile', ['tab' => 'orders'])->with('success', 'Pesanan dibuat. Laundry akan menimbang cucian sebelum pembayaran dibuka.');
    }

    public function profile(Request $request)
    {
        $this->customer();
        $user = Auth::user();
        $wallet = DB::table('user_wallets')->where('user_id', $user->id)->first();
        $activeTab = in_array($request->query('tab'), ['account', 'orders', 'wallet', 'addresses'], true)
            ? $request->query('tab')
            : 'account';
        $statusFilter = in_array($request->query('status'), ['active', 'completed', 'cancelled'], true)
            ? $request->query('status')
            : 'all';

        $transactionQuery = DB::table('laundry_transactions')
            ->join('laundries', 'laundries.id', '=', 'laundry_transactions.laundry_id')
            ->where('laundry_transactions.user_id', $user->id)
            ->select('laundry_transactions.*', 'laundries.store_name');

        if ($statusFilter === 'active') {
            $transactionQuery->whereIn('laundry_transactions.status', ['pending', 'processing']);
        } elseif ($statusFilter !== 'all') {
            $transactionQuery->where('laundry_transactions.status', $statusFilter);
        }

        return view('user.profile', [
            'user' => $user,
            'activeTab' => $activeTab,
            'statusFilter' => $statusFilter,
            'addresses' => DB::table('user_addresses')->where('user_id', $user->id)->orderByDesc('is_primary')->latest()->get(),
            'primaryAddress' => DB::table('user_addresses')->where('user_id', $user->id)->orderByDesc('is_primary')->latest()->first(),
            'balance' => $wallet->balance ?? 0,
            'transactions' => $transactionQuery->latest('laundry_transactions.created_at')->paginate(10, ['*'], 'orders_page')->withQueryString(),
            'topups' => DB::table('user_wallet_topups')->where('user_id', $user->id)->latest()->limit(10)->get(),
        ]);
    }

    public function updateProfilePhoto(Request $request)
    {
        $this->customer();
        $validated = $request->validate(['photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']]);
        $user = Auth::user();
        $path = $validated['photo']->store('profile-photos', 'public');

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->forceFill(['profile_photo_path' => $path])->save();

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function storeAddress(Request $request)
    {
        $this->customer();
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'recipient_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:2000'],
            'area' => ['nullable', 'string', 'max:160'],
        ]);
        $userId = Auth::id();
        $isPrimary = $request->boolean('is_primary') || !DB::table('user_addresses')->where('user_id', $userId)->exists();

        DB::transaction(function () use ($validated, $userId, $isPrimary) {
            if ($isPrimary) {
                DB::table('user_addresses')->where('user_id', $userId)->update(['is_primary' => false]);
            }

            DB::table('user_addresses')->insert($validated + [
                'user_id' => $userId,
                'is_primary' => $isPrimary,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($isPrimary) {
                DB::table('users')->where('id', $userId)->update(['address' => $validated['address'], 'updated_at' => now()]);
            }
        });

        return redirect()->route('profile', ['tab' => 'addresses'])->with('success', 'Alamat berhasil disimpan.');
    }

    public function setPrimaryAddress(int $address)
    {
        $this->customer();
        $userId = Auth::id();
        $record = DB::table('user_addresses')->where('id', $address)->where('user_id', $userId)->first();
        abort_unless($record, 404);

        DB::transaction(function () use ($record, $userId) {
            DB::table('user_addresses')->where('user_id', $userId)->update(['is_primary' => false]);
            DB::table('user_addresses')->where('id', $record->id)->update(['is_primary' => true, 'updated_at' => now()]);
            DB::table('users')->where('id', $userId)->update(['address' => $record->address, 'updated_at' => now()]);
        });

        return redirect()->route('profile', ['tab' => 'addresses'])->with('success', 'Alamat utama berhasil diubah.');
    }

    public function deleteAddress(int $address)
    {
        $this->customer();
        $record = DB::table('user_addresses')->where('id', $address)->where('user_id', Auth::id())->first();
        abort_unless($record, 404);
        DB::table('user_addresses')->where('id', $record->id)->delete();

        if ($record->is_primary) {
            $nextPrimary = DB::table('user_addresses')->where('user_id', Auth::id())->latest()->first();
            if ($nextPrimary) {
                DB::table('user_addresses')->where('id', $nextPrimary->id)->update(['is_primary' => true]);
                DB::table('users')->where('id', Auth::id())->update(['address' => $nextPrimary->address, 'updated_at' => now()]);
            }
        }

        return redirect()->route('profile', ['tab' => 'addresses'])->with('success', 'Alamat berhasil dihapus.');
    }

    public function showOrder(int $transaction)
    {
        $this->customer();

        $order = $this->ownedOrder($transaction);
        $wallet = DB::table('user_wallets')->where('user_id', Auth::id())->first();
        $messages = DB::table('laundry_messages')
            ->where('laundry_id', $order->laundry_id)
            ->where('user_id', Auth::id())
            ->orderBy('id')
            ->get();
        DB::table('laundry_messages')
            ->where('laundry_id', $order->laundry_id)
            ->where('user_id', Auth::id())
            ->whereIn('sender', ['owner', 'admin'])
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('user.order-detail', [
            'order' => $order,
            'messages' => $messages,
            'walletBalance' => (float) ($wallet->balance ?? 0),
            'review' => DB::table('laundry_reviews')->where('transaction_id', $order->id)->where('user_id', Auth::id())->first(),
        ]);
    }

    public function confirmOrderReceived(int $transaction)
    {
        $this->customer();

        DB::transaction(function () use ($transaction) {
            $order = DB::table('laundry_transactions')->where('id', $transaction)
                ->where('user_id', Auth::id())->lockForUpdate()->first();
            abort_unless($order, 404);
            abort_unless($order->status === 'completed' && $order->payment_status === 'paid' && $order->delivery_status === 'ready', 422);

            DB::table('laundry_transactions')->where('id', $order->id)->update([
                'delivery_status' => 'returned',
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('orders.show', $transaction)->with('success', 'Terima kasih sudah mengonfirmasi penerimaan cucian.');
    }

    public function storeOrderReview(Request $request, int $transaction)
    {
        $this->customer();
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($transaction, $validated) {
            $order = DB::table('laundry_transactions')->where('id', $transaction)
                ->where('user_id', Auth::id())->lockForUpdate()->first();
            abort_unless($order, 404);
            abort_unless($order->status === 'completed' && $order->payment_status === 'paid' && $order->delivery_status === 'returned', 422);
            abort_unless(! DB::table('laundry_reviews')->where('transaction_id', $order->id)->exists(), 422, 'Ulasan untuk pesanan ini sudah pernah dikirim.');

            DB::table('laundry_reviews')->insert([
                'laundry_id' => $order->laundry_id,
                'user_id' => Auth::id(),
                'transaction_id' => $order->id,
                'customer_name' => Auth::user()->name,
                'rating' => $validated['rating'],
                'comment' => trim($validated['comment'] ?? ''),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('orders.show', $transaction)->with('success', 'Terima kasih! Rating dan ulasanmu sudah terkirim.');
    }

    public function updateOrderReview(Request $request, int $transaction)
    {
        $this->customer();
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($transaction, $validated) {
            $order = DB::table('laundry_transactions')->where('id', $transaction)
                ->where('user_id', Auth::id())->lockForUpdate()->first();
            abort_unless($order, 404);
            abort_unless($order->status === 'completed' && $order->payment_status === 'paid' && $order->delivery_status === 'returned', 422);

            $review = DB::table('laundry_reviews')->where('transaction_id', $order->id)
                ->where('user_id', Auth::id());
            abort_unless($review->exists(), 404);

            $review->update([
                'rating' => $validated['rating'],
                'comment' => trim($validated['comment'] ?? ''),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('orders.show', $transaction)->with('success', 'Perubahan rating dan ulasan berhasil disimpan.');
    }

    private function ownedOrder(int $transaction): object
    {
        $order = DB::table('laundry_transactions')
            ->join('laundries', 'laundries.id', '=', 'laundry_transactions.laundry_id')
            ->where('laundry_transactions.id', $transaction)
            ->where('laundry_transactions.user_id', Auth::id())
            ->select('laundry_transactions.*', 'laundries.store_name', 'laundries.address as laundry_address', 'laundries.store_phone')
            ->first();
        abort_unless($order, 404);

        return $order;
    }

    public function orderMessages(Request $request, int $transaction): JsonResponse
    {
        $this->customer();
        $order = $this->ownedOrder($transaction);
        $validated = $request->validate(['after' => ['nullable', 'integer', 'min:0']]);

        $messages = DB::table('laundry_messages')
            ->where('laundry_id', $order->laundry_id)
            ->where('user_id', Auth::id())
            ->where('id', '>', $validated['after'] ?? 0)
            ->orderBy('id')
            ->get();

        DB::table('laundry_messages')
            ->where('laundry_id', $order->laundry_id)
            ->where('user_id', Auth::id())
            ->whereIn('sender', ['owner', 'admin'])
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['messages' => $messages]);
    }

    public function sendOrderMessage(Request $request, int $transaction)
    {
        $this->customer();
        $order = $this->ownedOrder($transaction);
        $validated = $request->validate(['message' => ['required', 'string', 'max:2000']]);

        DB::table('laundry_messages')->insert([
            'laundry_id' => $order->laundry_id,
            'user_id' => Auth::id(),
            'customer_name' => Auth::user()->name,
            'sender' => 'customer',
            'message' => $validated['message'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('orders.show', $transaction)->with('success', 'Pesan berhasil dikirim ke laundry.');
    }

    public function storeTopup(Request $request)
    {
        $this->customer();
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10000', 'max:5000000'],
            'payment_method' => ['required', 'in:qris'],
        ]);

        DB::table('user_wallet_topups')->insert([
            'user_id' => Auth::id(),
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_reference' => 'TOP-'.now()->format('ymdHis').'-'.strtoupper(bin2hex(random_bytes(3))),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('profile', ['tab' => 'wallet'])->with('success', 'Kode QRIS simulasi dibuat. Top-up akan masuk setelah admin menyetujui permintaan.');
    }

    public function confirmOrderQris(int $transaction)
    {
        $this->customer();

        DB::transaction(function () use ($transaction) {
            $order = DB::table('laundry_transactions')
                ->where('id', $transaction)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->first();

            abort_unless($order && $order->weighed_at && $order->payment_method === 'qris' && $order->payment_status === 'unpaid', 404);

            DB::table('laundry_transactions')->where('id', $order->id)->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('profile', ['tab' => 'orders'])->with('success', 'Pembayaran QRIS simulasi berhasil dikonfirmasi.');
    }

    public function payOrder(Request $request, int $transaction)
    {
        $this->customer();
        $validated = $request->validate(['payment_method' => ['required', 'in:wallet,qris,cash']]);

        DB::transaction(function () use ($transaction, $validated) {
            $order = DB::table('laundry_transactions')
                ->where('id', $transaction)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->first();
            abort_unless($order && $order->status === 'pending' && $order->payment_status === 'unpaid' && $order->weighed_at, 404);

            if ($validated['payment_method'] === 'qris') {
                DB::table('laundry_transactions')->where('id', $order->id)->update([
                    'payment_method' => 'qris',
                    'payment_reference' => $order->payment_reference ?: 'ORD-'.now()->format('ymdHis').'-'.strtoupper(bin2hex(random_bytes(3))),
                    'updated_at' => now(),
                ]);

                return;
            }

            if (in_array($validated['payment_method'], ['cash', 'wallet'], true)) {
                if ($validated['payment_method'] === 'wallet') {
                    $wallet = DB::table('user_wallets')->where('user_id', Auth::id())->lockForUpdate()->first();
                    if (! $wallet || $wallet->balance < $order->total_amount) {
                        throw ValidationException::withMessages([
                            'payment_method' => 'Saldo tidak cukup. Silakan top-up atau pilih QRIS atau tunai di outlet.',
                        ]);
                    }
                }

                DB::table('laundry_transactions')->where('id', $order->id)->update([
                    'payment_method' => $validated['payment_method'],
                    'payment_reference' => null,
                    'updated_at' => now(),
                ]);

                return;
            }
        });

        return redirect()->route('orders.show', $transaction)->with('success', match ($validated['payment_method']) {
            'wallet' => 'Permintaan pembayaran saldo dikirim. Saldo dipotong setelah owner mengonfirmasi.',
            'cash' => 'Metode tunai dipilih. Bayar langsung ke laundry; owner akan mencatat pembayaran di website.',
            default => 'Kode QRIS simulasi dibuat. Selesaikan pembayaran lalu konfirmasi.',
        });
    }
}
