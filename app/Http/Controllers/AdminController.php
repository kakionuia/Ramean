<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private function authorizeAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);
    }

    private function page(string $section, array $data = [])
    {
        $this->authorizeAdmin();

        return view('admin.dashboard', array_merge([
            'section' => $section,
            'admin' => Auth::user(),
            'navCounts' => [
                'pending_owners' => DB::table('users')->where('role', 'owner')->where('status', 'pending')->count(),
                'pending_topups' => DB::table('user_wallet_topups')->where('status', 'pending')->count(),
            ],
        ], $data));
    }

    public function dashboard()
    {
        $today = now()->startOfDay();
        $dailyTotals = DB::table('laundry_transactions')
            ->where('created_at', '>=', $today->copy()->subDays(6))
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'day');
        $transactionTrend = collect(range(6, 0))->map(function ($daysAgo) use ($today, $dailyTotals) {
            $date = $today->copy()->subDays($daysAgo);

            return [
                'label' => $date->format('d/m'),
                'count' => (int) ($dailyTotals->get($date->toDateString()) ?? 0),
            ];
        });
        $trendMax = max(1, (int) $transactionTrend->max('count'));
        $trendPoints = $transactionTrend->values()->map(function ($day, $index) use ($trendMax) {
            return [
                'x' => 20 + ($index * 63),
                'y' => 76 - (($day['count'] / $trendMax) * 48),
                'label' => $day['label'],
                'count' => $day['count'],
            ];
        });
        $trendLine = $trendPoints->map(fn ($point) => $point['x'] . ',' . $point['y'])->implode(' ');
        $trendArea = 'M' . $trendPoints->first()['x'] . ',84 L' . $trendLine . ' L' . $trendPoints->last()['x'] . ',84 Z';

        return $this->page('dashboard', [
            'stats' => [
                'pending_owners' => DB::table('users')->where('role', 'owner')->where('status', 'pending')->count(),
                'owners' => DB::table('users')->where('role', 'owner')->where('status', 'active')->count(),
                'customers' => DB::table('users')->where('role', 'user')->count(),
                'transactions' => DB::table('laundry_transactions')->count(),
                'pending_topups' => DB::table('user_wallet_topups')->where('status', 'pending')->count(),
                'reviews' => DB::table('laundry_reviews')->count(),
            ],
            'applications' => DB::table('users')->leftJoin('laundries', 'laundries.user_id', '=', 'users.id')
                ->where('users.role', 'owner')->where('users.status', 'pending')
                ->select('users.*', 'laundries.store_name', 'laundries.address as business_address', 'laundries.store_phone', 'laundries.description', 'laundries.verification_document_path')
                ->latest('users.created_at')->limit(6)->get(),
            'latestTransactions' => DB::table('laundry_transactions')->join('laundries', 'laundries.id', '=', 'laundry_transactions.laundry_id')->select('laundry_transactions.*', 'laundries.store_name')->latest('laundry_transactions.created_at')->limit(6)->get(),
            'transactionTrend' => $transactionTrend,
            'trendPoints' => $trendPoints,
            'trendMax' => $trendMax,
            'trendLine' => $trendLine,
            'trendArea' => $trendArea,
        ]);
    }

    public function ownerApplications()
    {
        return $this->page('applications', [
            'applications' => DB::table('users')->leftJoin('laundries', 'laundries.user_id', '=', 'users.id')
                ->where('users.role', 'owner')->where('users.status', 'pending')
                ->select('users.*', 'laundries.store_name', 'laundries.address as business_address', 'laundries.store_phone', 'laundries.description', 'laundries.verification_document_path')
                ->latest('users.created_at')->paginate(15),
        ]);
    }

    public function profile()
    {
        return $this->page('profile');
    }

    public function updateProfile(Request $request)
    {
        $this->authorizeAdmin();
        $admin = Auth::user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($admin->id)],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->phone = $validated['phone'] ?? null;
        if (!empty($validated['password'])) {
            $admin->password = $validated['password'];
        }
        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Profil admin berhasil diperbarui.');
    }

    public function updateProfilePhoto(Request $request)
    {
        $this->authorizeAdmin();
        $validated = $request->validate(['photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']]);
        $admin = Auth::user();
        $path = $validated['photo']->store('profile-photos', 'public');

        if ($admin->profile_photo_path) {
            Storage::disk('public')->delete($admin->profile_photo_path);
        }

        $admin->forceFill(['profile_photo_path' => $path])->save();

        return redirect()->route('admin.profile')->with('success', 'Foto profil admin berhasil diperbarui.');
    }

    public function downloadOwnerDocument(int $user)
    {
        $this->authorizeAdmin();

        $path = DB::table('laundries')
            ->join('users', 'users.id', '=', 'laundries.user_id')
            ->where('users.id', $user)
            ->where('users.role', 'owner')
            ->value('laundries.verification_document_path');

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'file';

        return Storage::disk('local')->download($path, 'dokumen-mitra-' . $user . '.' . $extension);
    }

    public function approveOwner(int $user)
    {
        $this->authorizeAdmin();

        $updated = DB::table('users')->where('id', $user)->where('role', 'owner')->where('status', 'pending')->update([
            'status' => 'active',
            'updated_at' => now(),
        ]);
        abort_unless($updated, 404);

        return back()->with('success', 'Permohonan owner disetujui. Owner sekarang dapat login.');
    }

    public function rejectOwner(int $user)
    {
        $this->authorizeAdmin();

        $updated = DB::table('users')->where('id', $user)->where('role', 'owner')->where('status', 'pending')->update([
            'status' => 'suspended',
            'updated_at' => now(),
        ]);
        abort_unless($updated, 404);

        return back()->with('success', 'Permohonan owner ditolak.');
    }

    public function accounts(Request $request)
    {
        $query = DB::table('users')->whereIn('role', ['user', 'owner']);
        $search = trim((string) $request->query('q', ''));
        $role = (string) $request->query('role', '');

        if ($search !== '') {
            $query->where(function ($users) use ($search) {
                $users->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%');
            });
        }
        if (in_array($role, ['user', 'owner'], true)) {
            $query->where('role', $role);
        }

        return $this->page('accounts', [
            'accounts' => $query->latest()->paginate(15)->withQueryString(),
            'search' => $search,
            'roleFilter' => $role,
        ]);
    }

    public function updateAccount(Request $request, int $user)
    {
        $this->authorizeAdmin();

        $account = DB::table('users')->where('id', $user)->whereIn('role', ['user', 'owner'])->first();
        abort_unless($account, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user)],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['pending', 'active', 'suspended'])],
        ]);

        DB::table('users')->where('id', $user)->update($validated + ['updated_at' => now()]);

        return back()->with('success', 'Data akun berhasil diperbarui.');
    }

    public function deleteAccount(int $user)
    {
        $this->authorizeAdmin();

        abort_if($user === Auth::id(), 403, 'Admin tidak dapat menghapus akunnya sendiri.');
        $deleted = DB::table('users')->where('id', $user)->whereIn('role', ['user', 'owner'])->delete();
        abort_unless($deleted, 404);

        return back()->with('success', 'Akun dan data laundry terkait berhasil dihapus.');
    }

    public function storeOwnerAccount(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'store_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $userId = DB::table('users')->insertGetId([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'password' => Hash::make($validated['password']),
                'role' => 'owner',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('laundries')->insert([
                'user_id' => $userId,
                'store_name' => $validated['store_name'],
                'store_phone' => $validated['phone'],
                'address' => $validated['address'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', 'Akun owner dan outlet berhasil dibuat. Owner dapat langsung login.');
    }

    public function catalog()
    {
        return $this->page('catalog', [
            'services' => DB::table('owner_services')->join('laundries', 'laundries.id', '=', 'owner_services.laundry_id')->select('owner_services.*', 'laundries.store_name')->orderBy('laundries.store_name')->orderBy('owner_services.name')->paginate(20),
            'laundries' => DB::table('laundries')->select('id', 'store_name')->orderBy('store_name')->get(),
        ]);
    }

    public function storeService(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'laundry_id' => ['required', 'integer', 'exists:laundries,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'duration_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'promo_label' => ['nullable', 'string', 'max:50'],
            'original_price' => ['nullable', 'numeric', 'gt:price', 'max:999999999'],
            'promo_until' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        DB::table('owner_services')->insert($validated + [
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Layanan berhasil ditambahkan ke katalog.');
    }

    public function updateService(Request $request, int $service)
    {
        $this->authorizeAdmin();

        $existing = DB::table('owner_services')->where('id', $service)->exists();
        abort_unless($existing, 404);

        $validated = $request->validate([
            'laundry_id' => ['required', 'integer', 'exists:laundries,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'duration_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'promo_label' => ['nullable', 'string', 'max:50'],
            'original_price' => ['nullable', 'numeric', 'gt:price', 'max:999999999'],
            'promo_until' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        DB::table('owner_services')->where('id', $service)->update($validated + [
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Layanan berhasil diperbarui.');
    }

    public function deleteService(int $service)
    {
        $this->authorizeAdmin();

        $deleted = DB::table('owner_services')->where('id', $service)->delete();
        abort_unless($deleted, 404);

        return back()->with('success', 'Layanan dihapus dari katalog.');
    }

    public function transactions()
    {
        $this->authorizeAdmin();

        $filters = request()->validate([
            'from' => ['nullable', 'date'],
            'until' => ['nullable', 'date', 'after_or_equal:from'],
            'status' => ['nullable', Rule::in(['pending', 'processing', 'completed', 'cancelled'])],
            'payment_status' => ['nullable', Rule::in(['unpaid', 'paid', 'refunded'])],
            'laundry_id' => ['nullable', 'integer', 'exists:laundries,id'],
        ]);

        $query = DB::table('laundry_transactions')
            ->join('laundries', 'laundries.id', '=', 'laundry_transactions.laundry_id')
            ->select('laundry_transactions.*', 'laundries.store_name');
        $applyFilters = function ($builder) use ($filters) {
            if (!empty($filters['from'])) {
                $builder->whereDate('laundry_transactions.created_at', '>=', $filters['from']);
            }
            if (!empty($filters['until'])) {
                $builder->whereDate('laundry_transactions.created_at', '<=', $filters['until']);
            }
            if (!empty($filters['status'])) {
                $builder->where('laundry_transactions.status', $filters['status']);
            }
            if (!empty($filters['payment_status'])) {
                $builder->where('laundry_transactions.payment_status', $filters['payment_status']);
            }
            if (!empty($filters['laundry_id'])) {
                $builder->where('laundry_transactions.laundry_id', $filters['laundry_id']);
            }
        };
        $applyFilters($query);
        $summary = (clone $query)->selectRaw(
            "COUNT(*) as transaction_count, COALESCE(SUM(total_amount), 0) as gross_total, COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END), 0) as paid_total, COALESCE(SUM(CASE WHEN payment_status = 'unpaid' THEN total_amount ELSE 0 END), 0) as unpaid_total"
        )->first();

        return $this->page('transactions', [
            'transactions' => $query->latest('laundry_transactions.created_at')->paginate(20)->withQueryString(),
            'transactionSummary' => $summary,
            'transactionFilters' => $filters,
            'laundries' => DB::table('laundries')->select('id', 'store_name')->orderBy('store_name')->get(),
        ]);
    }

    public function latestTransactions()
    {
        $this->authorizeAdmin();

        return response()->json([
            'transactions' => DB::table('laundry_transactions')
                ->join('laundries', 'laundries.id', '=', 'laundry_transactions.laundry_id')
                ->select('laundry_transactions.id', 'laundry_transactions.customer_name', 'laundry_transactions.service_name', 'laundry_transactions.total_amount', 'laundry_transactions.status', 'laundry_transactions.created_at', 'laundries.store_name')
                ->latest('laundry_transactions.created_at')
                ->limit(6)
                ->get(),
        ]);
    }

    public function reviews()
    {
        $this->authorizeAdmin();

        return $this->page('reviews', [
            'reviews' => DB::table('laundry_reviews')->join('laundries', 'laundries.id', '=', 'laundry_reviews.laundry_id')->select('laundry_reviews.*', 'laundries.store_name')->latest('laundry_reviews.created_at')->paginate(20),
        ]);
    }

    public function deleteReview(int $review)
    {
        $this->authorizeAdmin();

        $deleted = DB::table('laundry_reviews')->where('id', $review)->delete();
        abort_unless($deleted, 404);

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }

    public function messages()
    {
        $this->authorizeAdmin();

        $threads = DB::table('laundry_messages')
            ->join('laundries', 'laundries.id', '=', 'laundry_messages.laundry_id')
            ->leftJoin('users', 'users.id', '=', 'laundry_messages.user_id')
            ->select('laundry_messages.laundry_id', 'laundry_messages.user_id', 'laundry_messages.customer_name', 'laundries.store_name', 'users.email')
            ->selectRaw('MAX(laundry_messages.id) as latest_id, COUNT(laundry_messages.id) as message_count')
            ->groupBy('laundry_messages.laundry_id', 'laundry_messages.user_id', 'laundry_messages.customer_name', 'laundries.store_name', 'users.email')
            ->orderByDesc('latest_id')
            ->paginate(12);

        $threads->getCollection()->transform(function ($thread) {
            $messageQuery = DB::table('laundry_messages')
                ->where('laundry_id', $thread->laundry_id)
                ->where('customer_name', $thread->customer_name);
            $thread->user_id === null
                ? $messageQuery->whereNull('user_id')
                : $messageQuery->where('user_id', $thread->user_id);
            $thread->messages = $messageQuery->orderBy('id')->get();

            $unreadQuery = DB::table('laundry_messages')
                ->where('laundry_id', $thread->laundry_id)
                ->where('customer_name', $thread->customer_name)
                ->where('sender', 'customer')
                ->whereNull('read_at');
            $thread->user_id === null
                ? $unreadQuery->whereNull('user_id')
                : $unreadQuery->where('user_id', $thread->user_id);
            $unreadQuery->update(['read_at' => now()]);

            return $thread;
        });

        return $this->page('messages', ['threads' => $threads]);
    }

    public function replyMessage(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'laundry_id' => ['required', 'integer', 'exists:laundries,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $thread = DB::table('laundry_messages')
            ->where('laundry_id', $validated['laundry_id'])
            ->where('customer_name', $validated['customer_name']);
        isset($validated['user_id'])
            ? $thread->where('user_id', $validated['user_id'])
            : $thread->whereNull('user_id');
        abort_unless($thread->exists(), 404);

        DB::table('laundry_messages')->insert([
            'laundry_id' => $validated['laundry_id'],
            'user_id' => $validated['user_id'] ?? null,
            'customer_name' => $validated['customer_name'],
            'sender' => 'admin',
            'message' => $validated['message'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Balasan dari Admin Washly berhasil dikirim.');
    }

    public function schedules()
    {
        $this->authorizeAdmin();

        return $this->page('schedules', [
            'schedules' => DB::table('laundry_work_hours')->join('laundries', 'laundries.id', '=', 'laundry_work_hours.laundry_id')->select('laundry_work_hours.*', 'laundries.store_name')->orderBy('laundries.store_name')->orderBy('laundry_work_hours.day_of_week')->paginate(35),
        ]);
    }

    public function topups()
    {
        $this->authorizeAdmin();

        return $this->page('topups', [
            'topups' => DB::table('user_wallet_topups')->join('users', 'users.id', '=', 'user_wallet_topups.user_id')->select('user_wallet_topups.*', 'users.name', 'users.email')->latest('user_wallet_topups.created_at')->paginate(20),
        ]);
    }

    public function processTopup(int $topup, string $decision)
    {
        $this->authorizeAdmin();

        abort_unless(in_array($decision, ['approve', 'reject'], true), 404);

        DB::transaction(function () use ($topup, $decision) {
            $request = DB::table('user_wallet_topups')->where('id', $topup)->lockForUpdate()->first();
            abort_unless($request && $request->status === 'pending', 404);

            if ($decision === 'approve') {
                DB::table('users')->where('id', $request->user_id)->lockForUpdate()->first();
                $wallet = DB::table('user_wallets')->where('user_id', $request->user_id)->lockForUpdate()->first();
                if ($wallet) {
                    DB::table('user_wallets')->where('id', $wallet->id)->increment('balance', $request->amount, ['updated_at' => now()]);
                } else {
                    DB::table('user_wallets')->insert([
                        'user_id' => $request->user_id,
                        'balance' => $request->amount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::table('user_wallet_topups')->where('id', $topup)->update([
                'status' => $decision === 'approve' ? 'approved' : 'rejected',
                'processed_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', $decision === 'approve' ? 'Top-up disetujui dan saldo user diperbarui.' : 'Permintaan top-up ditolak.');
    }
}
