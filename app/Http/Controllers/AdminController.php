<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        ], $data));
    }

    public function dashboard()
    {
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
                ->select('users.*', 'laundries.store_name', 'laundries.address as business_address', 'laundries.store_phone', 'laundries.description')
                ->latest('users.created_at')->limit(6)->get(),
            'latestTransactions' => DB::table('laundry_transactions')->join('laundries', 'laundries.id', '=', 'laundry_transactions.laundry_id')->select('laundry_transactions.*', 'laundries.store_name')->latest('laundry_transactions.created_at')->limit(6)->get(),
        ]);
    }

    public function ownerApplications()
    {
        return $this->page('applications', [
            'applications' => DB::table('users')->leftJoin('laundries', 'laundries.user_id', '=', 'users.id')
                ->where('users.role', 'owner')->where('users.status', 'pending')
                ->select('users.*', 'laundries.store_name', 'laundries.address as business_address', 'laundries.store_phone', 'laundries.description')
                ->latest('users.created_at')->paginate(15),
        ]);
    }

    public function approveOwner(int $user)
    {
        $updated = DB::table('users')->where('id', $user)->where('role', 'owner')->where('status', 'pending')->update([
            'status' => 'active',
            'updated_at' => now(),
        ]);
        abort_unless($updated, 404);

        return back()->with('success', 'Permohonan owner disetujui. Owner sekarang dapat login.');
    }

    public function rejectOwner(int $user)
    {
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
        $account = DB::table('users')->where('id', $user)->whereIn('role', ['user', 'owner'])->first();
        abort_unless($account, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user)],
            'address' => ['nullable', 'string', 'max:2000'],
            'role' => ['required', Rule::in(['user', 'owner'])],
            'status' => ['required', Rule::in(['pending', 'active', 'suspended'])],
        ]);

        DB::table('users')->where('id', $user)->update($validated + ['updated_at' => now()]);

        return back()->with('success', 'Data akun berhasil diperbarui.');
    }

    public function deleteAccount(int $user)
    {
        abort_if($user === Auth::id(), 403, 'Admin tidak dapat menghapus akunnya sendiri.');
        $deleted = DB::table('users')->where('id', $user)->whereIn('role', ['user', 'owner'])->delete();
        abort_unless($deleted, 404);

        return back()->with('success', 'Akun dan data laundry terkait berhasil dihapus.');
    }

    public function catalog()
    {
        return $this->page('catalog', [
            'services' => DB::table('owner_services')->join('laundries', 'laundries.id', '=', 'owner_services.laundry_id')->select('owner_services.*', 'laundries.store_name')->orderBy('laundries.store_name')->orderBy('owner_services.name')->paginate(20),
        ]);
    }

    public function deleteService(int $service)
    {
        $deleted = DB::table('owner_services')->where('id', $service)->delete();
        abort_unless($deleted, 404);

        return back()->with('success', 'Layanan dihapus dari katalog.');
    }

    public function transactions()
    {
        return $this->page('transactions', [
            'transactions' => DB::table('laundry_transactions')->join('laundries', 'laundries.id', '=', 'laundry_transactions.laundry_id')->select('laundry_transactions.*', 'laundries.store_name')->latest('laundry_transactions.created_at')->paginate(20),
        ]);
    }

    public function reviews()
    {
        return $this->page('reviews', [
            'reviews' => DB::table('laundry_reviews')->join('laundries', 'laundries.id', '=', 'laundry_reviews.laundry_id')->select('laundry_reviews.*', 'laundries.store_name')->latest('laundry_reviews.created_at')->paginate(20),
        ]);
    }

    public function deleteReview(int $review)
    {
        $deleted = DB::table('laundry_reviews')->where('id', $review)->delete();
        abort_unless($deleted, 404);

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }

    public function messages()
    {
        return $this->page('messages', [
            'messages' => DB::table('laundry_messages')->join('laundries', 'laundries.id', '=', 'laundry_messages.laundry_id')->select('laundry_messages.*', 'laundries.store_name')->latest('laundry_messages.created_at')->paginate(25),
        ]);
    }

    public function schedules()
    {
        return $this->page('schedules', [
            'schedules' => DB::table('laundry_work_hours')->join('laundries', 'laundries.id', '=', 'laundry_work_hours.laundry_id')->select('laundry_work_hours.*', 'laundries.store_name')->orderBy('laundries.store_name')->orderBy('laundry_work_hours.day_of_week')->paginate(35),
        ]);
    }

    public function topups()
    {
        return $this->page('topups', [
            'topups' => DB::table('user_wallet_topups')->join('users', 'users.id', '=', 'user_wallet_topups.user_id')->select('user_wallet_topups.*', 'users.name', 'users.email')->latest('user_wallet_topups.created_at')->paginate(20),
        ]);
    }

    public function processTopup(int $topup, string $decision)
    {
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
