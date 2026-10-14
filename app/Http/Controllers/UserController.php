<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $search = trim((string) $request->query('q', ''));
        $location = trim((string) $request->query('location', ''));

        $laundryQuery = DB::table('laundries')
            ->join('users as owners', 'owners.id', '=', 'laundries.user_id')
            ->where('owners.role', 'owner')
            ->where('owners.status', 'active')
            ->select('laundries.*', 'owners.name as owner_name', 'owners.kecamatan', 'owners.domisili');

        if ($search !== '') {
            $laundryQuery->where(function ($query) use ($search) {
                $query->where('laundries.store_name', 'like', '%' . $search . '%')
                    ->orWhere('laundries.address', 'like', '%' . $search . '%')
                    ->orWhere('laundries.description', 'like', '%' . $search . '%')
                    ->orWhereExists(function ($services) use ($search) {
                        $services->selectRaw('1')->from('owner_services')
                            ->whereColumn('owner_services.laundry_id', 'laundries.id')
                            ->where('owner_services.is_active', true)
                            ->where('owner_services.name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($location !== '') {
            $laundryQuery->where('laundries.address', 'like', '%' . $location . '%');
        }

        $laundries = $laundryQuery->orderBy('laundries.store_name')->get()->map(function ($laundry) {
            $laundry->services = DB::table('owner_services')
                ->where('laundry_id', $laundry->id)
                ->where('is_active', true)
                ->orderBy('price')
                ->get();

            return $laundry;
        });

        $promos = DB::table('owner_services')
            ->join('laundries', 'laundries.id', '=', 'owner_services.laundry_id')
            ->where('owner_services.is_active', true)
            ->whereNotNull('owner_services.original_price')
            ->whereColumn('owner_services.original_price', '>', 'owner_services.price')
            ->where(function ($query) {
                $query->whereNull('owner_services.promo_until')->orWhereDate('owner_services.promo_until', '>=', today());
            })
            ->select('owner_services.*', 'laundries.store_name', 'laundries.address')
            ->latest('owner_services.updated_at')
            ->limit(8)
            ->get();

        $locations = DB::table('laundries')->pluck('address')
            ->flatMap(fn ($address) => array_slice(array_map('trim', explode(',', $address)), -2))
            ->filter(fn ($part) => mb_strlen($part) >= 3)
            ->unique()
            ->take(8)
            ->values();

        $notifications = DB::table('laundry_transactions')
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(3)
            ->get();

        return view('home', compact('laundries', 'promos', 'locations', 'notifications', 'search', 'location'));
    }

    public function showLaundry(int $laundry)
    {
        $this->customer();

        $laundry = DB::table('laundries')
            ->join('users as owners', 'owners.id', '=', 'laundries.user_id')
            ->select('laundries.*', 'owners.name as owner_name', 'owners.kecamatan', 'owners.domisili')
            ->where('laundries.id', $laundry)
            ->where('owners.role', 'owner')
            ->where('owners.status', 'active')
            ->first();
        abort_unless($laundry, 404);

        $services = DB::table('owner_services')->where('laundry_id', $laundry->id)->where('is_active', true)->orderBy('price')->get();
        $wallet = DB::table('user_wallets')->where('user_id', Auth::id())->first();

        return view('user.laundry-detail', [
            'laundry' => $laundry,
            'services' => $services,
            'walletBalance' => $wallet->balance ?? 0,
        ]);
    }

    public function storeOrder(Request $request, int $laundry)
    {
        $this->customer();
        $validated = $request->validate([
            'service_id' => ['required', 'integer'],
            'garment_type' => ['required', 'in:kiloan,pakaian,selimut,sepatu,lainnya'],
            'wash_type' => ['required', 'in:cuci,setrika,cuci_setrika'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:wallet,qris'],
        ]);

        $service = DB::table('owner_services')
            ->where('id', $validated['service_id'])
            ->where('laundry_id', $laundry)
            ->where('is_active', true)
            ->first();
        abort_unless($service, 404, 'Layanan tidak tersedia.');

        $laundryRecord = DB::table('laundries')->where('id', $laundry)->first();
        abort_unless($laundryRecord, 404);

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

        $totalAmount = $service->price * $validated['quantity'];
        $paymentReference = $validated['payment_method'] === 'qris'
            ? 'ORD-' . now()->format('ymdHis') . '-' . strtoupper(bin2hex(random_bytes(3)))
            : null;

        DB::transaction(function () use ($validated, $service, $laundry, $washLabels, $garmentLabels, $totalAmount, $paymentReference) {
            $wallet = null;
            if ($validated['payment_method'] === 'wallet') {
                $wallet = DB::table('user_wallets')->where('user_id', Auth::id())->lockForUpdate()->first();
                if (!$wallet || $wallet->balance < $totalAmount) {
                    throw ValidationException::withMessages([
                        'payment_method' => 'Saldo tidak cukup. Silakan top-up atau pilih pembayaran QRIS.',
                    ]);
                }
            }

            DB::table('laundry_transactions')->insert([
                'laundry_id' => $laundry,
                'user_id' => Auth::id(),
                'service_id' => $service->id,
                'customer_name' => Auth::user()->name,
                'service_name' => $service->name . ' · ' . $washLabels[$validated['wash_type']],
                'order_details' => implode(' | ', array_filter([
                    'Jenis cucian: ' . $garmentLabels[$validated['garment_type']],
                    'Jenis layanan: ' . $washLabels[$validated['wash_type']],
                    'Catatan: ' . ($validated['notes'] ?? ''),
                ])),
                'quantity' => $validated['quantity'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => $validated['payment_method'] === 'wallet' ? 'paid' : 'unpaid',
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $paymentReference,
                'paid_at' => $validated['payment_method'] === 'wallet' ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($wallet) {
                DB::table('user_wallets')->where('id', $wallet->id)->decrement('balance', $totalAmount, ['updated_at' => now()]);
            }
        });

        $message = $validated['payment_method'] === 'wallet'
            ? 'Pesanan dibuat dan dibayar menggunakan saldo.'
            : 'Pesanan dibuat. Selesaikan konfirmasi QRIS simulasi dari riwayat transaksi.';

        return redirect()->route('profile', ['tab' => 'orders'])->with('success', $message);
    }

    public function profile(Request $request)
    {
        $this->customer();
        $user = Auth::user();
        $wallet = DB::table('user_wallets')->where('user_id', $user->id)->first();

        return view('user.profile', [
            'user' => $user,
            'activeTab' => $request->query('tab', 'account'),
            'balance' => $wallet->balance ?? 0,
            'transactions' => DB::table('laundry_transactions')
                ->join('laundries', 'laundries.id', '=', 'laundry_transactions.laundry_id')
                ->where('laundry_transactions.user_id', $user->id)
                ->select('laundry_transactions.*', 'laundries.store_name')
                ->latest('laundry_transactions.created_at')
                ->paginate(10, ['*'], 'orders_page'),
            'topups' => DB::table('user_wallet_topups')->where('user_id', $user->id)->latest()->limit(10)->get(),
        ]);
    }

    public function showOrder(int $transaction)
    {
        $this->customer();

        $order = $this->ownedOrder($transaction);
        $messages = DB::table('laundry_messages')
            ->where('laundry_id', $order->laundry_id)
            ->where('user_id', Auth::id())
            ->orderBy('id')
            ->get();
        DB::table('laundry_messages')
            ->where('laundry_id', $order->laundry_id)
            ->where('user_id', Auth::id())
            ->where('sender', 'owner')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('user.order-detail', compact('order', 'messages'));
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
            ->where('sender', 'owner')
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
            'payment_reference' => 'TOP-' . now()->format('ymdHis') . '-' . strtoupper(bin2hex(random_bytes(3))),
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

            abort_unless($order && $order->payment_method === 'qris' && $order->payment_status === 'unpaid', 404);

            DB::table('laundry_transactions')->where('id', $order->id)->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('profile', ['tab' => 'orders'])->with('success', 'Pembayaran QRIS simulasi berhasil dikonfirmasi.');
    }
}