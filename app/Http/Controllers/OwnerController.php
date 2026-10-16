<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OwnerController extends Controller
{
    private const DAYS = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];

    private function laundry(): object
    {
        abort_unless(Auth::user()?->role === 'owner' && Auth::user()?->status === 'active', 403);

        $laundry = DB::table('laundries')->where('user_id', Auth::id())->first();
        abort_unless($laundry, 404, 'Data laundry tidak ditemukan.');

        return $laundry;
    }

    private function page(string $section, array $data = [])
    {
        return view('owner.dashboard', array_merge([
            'laundry' => $this->laundry(),
            'section' => $section,
            'owner' => Auth::user(),
        ], $data));
    }

    public function dashboard()
    {
        if (Auth::user()?->role === 'owner' && Auth::user()?->status === 'pending') {
            return view('owner.pending', ['owner' => Auth::user()]);
        }

        $laundry = $this->laundry();

        return $this->page('dashboard', [
            'stats' => [
                'services' => DB::table('owner_services')->where('laundry_id', $laundry->id)->where('is_active', true)->count(),
                'open_transactions' => DB::table('laundry_transactions')->where('laundry_id', $laundry->id)->whereIn('status', ['pending', 'processing'])->count(),
                'income' => DB::table('laundry_transactions')->where('laundry_id', $laundry->id)->where('payment_status', 'paid')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('total_amount'),
                'unanswered_reviews' => DB::table('laundry_reviews')->where('laundry_id', $laundry->id)->whereNull('owner_reply')->count(),
            ],
            'transactions' => DB::table('laundry_transactions')->where('laundry_id', $laundry->id)->latest()->limit(6)->get(),
        ]);
    }

    public function transactions()
    {
        $laundry = $this->laundry();

        return $this->page('transactions', [
            'transactions' => DB::table('laundry_transactions')->where('laundry_id', $laundry->id)->latest()->paginate(15),
        ]);
    }

    public function transactionDetail(int $transaction)
    {
        $laundry = $this->laundry();
        $order = DB::table('laundry_transactions')
            ->leftJoin('users', 'users.id', '=', 'laundry_transactions.user_id')
            ->where('laundry_transactions.id', $transaction)
            ->where('laundry_transactions.laundry_id', $laundry->id)
            ->select('laundry_transactions.*', 'users.email as customer_email', 'users.phone as customer_phone')
            ->first();
        abort_unless($order, 404);

        return $this->page('transaction-detail', compact('order'));
    }

    public function updateDeliveryStatus(Request $request, int $transaction)
    {
        $laundry = $this->laundry();
        $validated = $request->validate(['delivery_status' => ['required', 'in:picked_up,received,returned']]);

        DB::transaction(function () use ($laundry, $transaction, $validated) {
            $order = DB::table('laundry_transactions')->where('id', $transaction)
                ->where('laundry_id', $laundry->id)->lockForUpdate()->first();
            abort_unless($order, 404);

            if ($validated['delivery_status'] === 'returned') {
                abort_unless($order->status === 'completed' && $order->payment_status === 'paid' && $order->delivery_status === 'received', 422);
                DB::table('laundry_transactions')->where('id', $order->id)->update([
                    'delivery_status' => 'returned',
                    'updated_at' => now(),
                ]);

                return;
            }

            abort_unless($order->status === 'pending' && ! $order->weighed_at, 404);

            $allowed = $order->delivery_method === 'pickup'
                ? ['waiting' => ['picked_up'], 'picked_up' => ['received']]
                : ['waiting' => ['received']];
            abort_unless(in_array($validated['delivery_status'], $allowed[$order->delivery_status] ?? [], true), 422);

            DB::table('laundry_transactions')->where('id', $order->id)->update([
                'delivery_status' => $validated['delivery_status'],
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', 'Status penerimaan cucian berhasil diperbarui.');
    }

    public function weighTransaction(Request $request, int $transaction)
    {
        $laundry = $this->laundry();
        $validated = $request->validate(['weight_kg' => ['required', 'numeric', 'gt:0', 'max:1000']]);

        DB::transaction(function () use ($laundry, $transaction, $validated) {
            $order = DB::table('laundry_transactions')->where('id', $transaction)
                ->where('laundry_id', $laundry->id)->lockForUpdate()->first();
            abort_unless($order && $order->status === 'pending' && $order->delivery_status === 'received' && ! $order->weighed_at, 422);

            $weight = round((float) $validated['weight_kg'], 2);
            $serviceRate = $order->service_rate;
            if ($serviceRate === null && $order->service_id) {
                $serviceRate = DB::table('owner_services')->where('id', $order->service_id)->value('price');
            }
            abort_unless($serviceRate !== null, 422, 'Tarif layanan tidak ditemukan. Perbarui data paket sebelum menimbang.');
            $pickupFee = (float) ($order->pickup_fee ?? 0);
            $total = round(($weight * (float) $serviceRate) + $pickupFee, 2);

            DB::table('laundry_transactions')->where('id', $order->id)->update([
                'weight_kg' => $weight,
                'service_rate' => $serviceRate,
                'pickup_fee' => $pickupFee,
                'total_amount' => $total,
                'weighed_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', 'Berat tercatat dan tagihan akhir sudah dihitung. User sekarang dapat membayar.');
    }

    public function updateTransactionStatus(Request $request, int $transaction)
    {
        $laundry = $this->laundry();
        $validated = $request->validate([
            'status' => ['required', 'in:processing,completed,cancelled'],
        ]);

        DB::transaction(function () use ($laundry, $transaction, $validated) {
            $order = DB::table('laundry_transactions')
                ->where('id', $transaction)
                ->where('laundry_id', $laundry->id)
                ->lockForUpdate()
                ->first();
            abort_unless($order, 404);

            $allowedTransitions = [
                'pending' => ['processing'],
                'processing' => ['completed'],
            ];
            if (! in_array($validated['status'], $allowedTransitions[$order->status] ?? [], true)) {
                abort(422, 'Status pesanan tidak dapat diubah ke tahap tersebut.');
            }
            if (! $order->weighed_at || $order->payment_status !== 'paid') {
                abort(422, 'Cucian harus ditimbang dan dibayar sebelum mulai diproses.');
            }

            DB::table('laundry_transactions')->where('id', $order->id)->update([
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', 'Status proses cucian berhasil diperbarui.');
    }

    public function services()
    {
        $laundry = $this->laundry();

        return $this->page('services', [
            'services' => DB::table('owner_services')->where('laundry_id', $laundry->id)->orderBy('name')->get(),
        ]);
    }

    public function storeService(Request $request)
    {
        $laundry = $this->laundry();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'duration_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'promo_label' => ['nullable', 'string', 'max:50'],
            'original_price' => ['nullable', 'numeric', 'gt:price', 'max:999999999'],
            'promo_until' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        DB::table('owner_services')->insert($validated + [
            'laundry_id' => $laundry->id,
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function updateService(Request $request, int $service)
    {
        $laundry = $this->laundry();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'duration_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'promo_label' => ['nullable', 'string', 'max:50'],
            'original_price' => ['nullable', 'numeric', 'gt:price', 'max:999999999'],
            'promo_until' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $updated = DB::table('owner_services')->where('laundry_id', $laundry->id)->where('id', $service)->update($validated + [
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
        ]);
        abort_unless($updated, 404);

        return back()->with('success', 'Layanan berhasil diperbarui.');
    }

    public function deleteService(int $service)
    {
        $deleted = DB::table('owner_services')->where('laundry_id', $this->laundry()->id)->where('id', $service)->delete();
        abort_unless($deleted, 404);

        return back()->with('success', 'Layanan berhasil dihapus.');
    }

    public function finance()
    {
        $laundry = $this->laundry();
        $income = DB::table('laundry_transactions')->where('laundry_id', $laundry->id)->where('payment_status', 'paid')->sum('total_amount');
        $expenses = DB::table('laundry_expenses')->where('laundry_id', $laundry->id)->sum('amount');

        return $this->page('finance', [
            'income' => $income,
            'expensesTotal' => $expenses,
            'balance' => $income - $expenses,
            'expenseRows' => DB::table('laundry_expenses')->where('laundry_id', $laundry->id)->latest('spent_at')->paginate(12),
            'paidRows' => DB::table('laundry_transactions')->where('laundry_id', $laundry->id)->where('payment_status', 'paid')->latest('paid_at')->limit(12)->get(),
        ]);
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'description' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:80'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'spent_at' => ['required', 'date'],
        ]);

        DB::table('laundry_expenses')->insert($validated + [
            'laundry_id' => $this->laundry()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function reviews()
    {
        return $this->page('reviews', [
            'reviews' => DB::table('laundry_reviews')->where('laundry_id', $this->laundry()->id)->latest()->paginate(12),
        ]);
    }

    public function replyReview(Request $request, int $review)
    {
        $validated = $request->validate(['owner_reply' => ['required', 'string', 'max:2000']]);
        $reviewQuery = DB::table('laundry_reviews')->where('laundry_id', $this->laundry()->id)->where('id', $review);
        abort_unless($reviewQuery->exists(), 404);
        $reviewQuery->update([
            'owner_reply' => $validated['owner_reply'],
            'replied_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Balasan ulasan berhasil disimpan.');
    }

    public function deleteReview(int $review)
    {
        $deleted = DB::table('laundry_reviews')->where('laundry_id', $this->laundry()->id)->where('id', $review)->delete();
        abort_unless($deleted, 404);

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }

    public function chat(Request $request)
    {
        $laundry = $this->laundry();
        $customers = DB::table('laundry_messages')
            ->where('laundry_id', $laundry->id)
            ->select('customer_name', 'user_id')
            ->selectRaw('MAX(id) as latest_id')
            ->groupBy('customer_name', 'user_id')
            ->orderByDesc('latest_id')
            ->get();
        $selectedCustomer = $request->query('customer');
        $selectedUserId = $request->query('user_id');

        if (! $selectedCustomer && $customers->isNotEmpty()) {
            $selectedCustomer = $customers->first()->customer_name;
            $selectedUserId = $customers->first()->user_id;
        }

        $messages = collect();
        $selectedThread = $customers->first(function ($thread) use ($selectedCustomer, $selectedUserId) {
            return $thread->customer_name === $selectedCustomer
                && (string) $thread->user_id === (string) $selectedUserId;
        });
        if ($selectedCustomer && $selectedThread) {
            $messages = $this->chatThread($laundry->id, $selectedCustomer, $selectedThread->user_id)->orderBy('id')->get();
            $this->chatThread($laundry->id, $selectedCustomer, $selectedThread->user_id)
                ->where('sender', 'customer')->whereNull('read_at')->update(['read_at' => now()]);
            $selectedUserId = $selectedThread->user_id;
        } else {
            $selectedCustomer = null;
            $selectedUserId = null;
        }

        return $this->page('chat', compact('customers', 'selectedCustomer', 'selectedUserId', 'messages'));
    }

    private function chatThread(int $laundryId, string $customerName, ?int $userId)
    {
        $query = DB::table('laundry_messages')
            ->where('laundry_id', $laundryId)
            ->where('customer_name', $customerName);

        return $userId === null ? $query->whereNull('user_id') : $query->where('user_id', $userId);
    }

    public function chatMessages(Request $request): JsonResponse
    {
        $laundry = $this->laundry();
        $validated = $request->validate([
            'customer' => ['required', 'string', 'max:120'],
            'user_id' => ['nullable', 'integer'],
            'after' => ['nullable', 'integer', 'min:0'],
        ]);
        $thread = $this->chatThread($laundry->id, $validated['customer'], isset($validated['user_id']) ? (int) $validated['user_id'] : null);
        $exists = $thread->exists();
        abort_unless($exists, 404);

        $messages = $this->chatThread($laundry->id, $validated['customer'], isset($validated['user_id']) ? (int) $validated['user_id'] : null)
            ->where('id', '>', $validated['after'] ?? 0)
            ->orderBy('id')
            ->get();

        $this->chatThread($laundry->id, $validated['customer'], isset($validated['user_id']) ? (int) $validated['user_id'] : null)
            ->where('sender', 'customer')->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['messages' => $messages]);
    }

    public function replyChat(Request $request): JsonResponse
    {
        $laundry = $this->laundry();
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'user_id' => ['nullable', 'integer'],
            'message' => ['required', 'string', 'max:2000'],
        ]);
        $threadUserId = isset($validated['user_id']) ? (int) $validated['user_id'] : null;
        $exists = $this->chatThread($laundry->id, $validated['customer_name'], $threadUserId)->exists();
        abort_unless($exists, 404);

        $id = DB::table('laundry_messages')->insertGetId([
            'laundry_id' => $laundry->id,
            'user_id' => $threadUserId,
            'customer_name' => $validated['customer_name'],
            'sender' => 'owner',
            'message' => $validated['message'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(DB::table('laundry_messages')->where('id', $id)->first(), 201);
    }

    public function settings()
    {
        $laundry = $this->laundry();
        $hours = DB::table('laundry_work_hours')->where('laundry_id', $laundry->id)->get()->keyBy('day_of_week');

        return $this->page('settings', ['hours' => $hours, 'days' => self::DAYS]);
    }

    public function updateSettings(Request $request)
    {
        $laundry = $this->laundry();
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'store_phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:2000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'pickup_fee' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'hours' => ['required', 'array', 'size:7'],
            'hours.*.is_closed' => ['nullable', 'boolean'],
            'hours.*.opens_at' => ['nullable', 'date_format:H:i'],
            'hours.*.closes_at' => ['nullable', 'date_format:H:i'],
        ]);

        foreach (self::DAYS as $day => $label) {
            $dayHours = $validated['hours'][$day] ?? null;
            if (! $dayHours) {
                throw ValidationException::withMessages(['hours' => 'Jadwal untuk '.$label.' belum lengkap.']);
            }
            if (empty($dayHours['is_closed']) && (empty($dayHours['opens_at']) || empty($dayHours['closes_at']))) {
                throw ValidationException::withMessages(['hours.'.$day.'.opens_at' => 'Isi jam buka dan tutup atau tandai hari libur.']);
            }
        }

        DB::transaction(function () use ($validated, $laundry) {
            DB::table('laundries')->where('id', $laundry->id)->update([
                'store_name' => $validated['store_name'],
                'store_phone' => $validated['store_phone'],
                'address' => $validated['address'],
                'description' => $validated['description'] ?? null,
                'pickup_fee' => $validated['pickup_fee'],
                'updated_at' => now(),
            ]);

            foreach (self::DAYS as $day => $label) {
                $dayHours = $validated['hours'][$day];
                DB::table('laundry_work_hours')->updateOrInsert(
                    ['laundry_id' => $laundry->id, 'day_of_week' => $day],
                    [
                        'is_closed' => ! empty($dayHours['is_closed']),
                        'opens_at' => ! empty($dayHours['is_closed']) ? null : $dayHours['opens_at'],
                        'closes_at' => ! empty($dayHours['is_closed']) ? null : $dayHours['closes_at'],
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });

        return back()->with('success', 'Pengaturan laundry berhasil disimpan.');
    }
}
