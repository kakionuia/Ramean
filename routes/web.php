<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\RegisterOwnerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rute Halaman Utama (Publik)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// URL alternatif yang sering digunakan untuk membuka halaman awal.
Route::redirect('/home', '/#home')->name('welcome');

// Rute untuk Tamu (Guest Only)
Route::middleware('guest:web')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/register-owner', [AuthController::class, 'showRegisterOwner'])->name('register-owner');
    Route::post('/register-owner', [RegisterOwnerController::class, 'register'])->name('register.owner');
});

// Login stays accessible when another role is already signed in on a different tab.
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:web')->group(function () {
    Route::get('/dashboard', [UserController::class, 'home'])->name('dashboard');
    Route::get('/discover', [UserController::class, 'discover'])->name('laundries.discover');
    Route::get('/laundries/{laundry}', [UserController::class, 'showLaundry'])->name('laundry.show');
    Route::post('/laundries/{laundry}/orders', [UserController::class, 'storeOrder'])->name('orders.store');
    Route::post('/orders/{transaction}/confirm-qris', [UserController::class, 'confirmOrderQris'])->name('orders.confirm-qris');
    Route::post('/orders/{transaction}/pay', [UserController::class, 'payOrder'])->name('orders.pay');
    Route::post('/orders/{transaction}/confirm-received', [UserController::class, 'confirmOrderReceived'])->name('orders.confirm-received');
    Route::post('/orders/{transaction}/review', [UserController::class, 'storeOrderReview'])->name('orders.review.store');
    Route::put('/orders/{transaction}/review', [UserController::class, 'updateOrderReview'])->name('orders.review.update');
    Route::get('/orders/{transaction}', [UserController::class, 'showOrder'])->name('orders.show');
    Route::get('/orders/{transaction}/messages', [UserController::class, 'orderMessages'])->name('orders.messages');
    Route::post('/orders/{transaction}/messages', [UserController::class, 'sendOrderMessage'])->name('orders.messages.store');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/photo', [UserController::class, 'updateProfilePhoto'])->name('profile.photo');
    Route::post('/profile/addresses', [UserController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::put('/profile/addresses/{address}/primary', [UserController::class, 'setPrimaryAddress'])->name('profile.addresses.primary');
    Route::delete('/profile/addresses/{address}', [UserController::class, 'deleteAddress'])->name('profile.addresses.delete');
    Route::post('/profile/topups', [UserController::class, 'storeTopup'])->name('profile.topups.store');

});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:web')->name('logout');

Route::prefix('owner')->name('owner.')->middleware('auth:owner')->controller(OwnerController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/transactions', 'transactions')->name('transactions');
    Route::get('/transactions/latest', 'latestTransactions')->name('transactions.latest');
    Route::get('/transactions/{transaction}', 'transactionDetail')->name('transactions.show');
    Route::post('/transactions/{transaction}/delivery-status', 'updateDeliveryStatus')->name('transactions.delivery-status');
    Route::post('/transactions/{transaction}/weigh', 'weighTransaction')->name('transactions.weigh');
    Route::post('/transactions/{transaction}/confirm-payment', 'confirmPendingPayment')->name('transactions.confirm-payment');
    Route::post('/transactions/{transaction}/status', 'updateTransactionStatus')->name('transactions.status');
    Route::get('/services', 'services')->name('services');
    Route::post('/services', 'storeService')->name('services.store');
    Route::put('/services/{service}', 'updateService')->name('services.update');
    Route::delete('/services/{service}', 'deleteService')->name('services.delete');
    Route::get('/finance', 'finance')->name('finance');
    Route::post('/finance/expenses', 'storeExpense')->name('expenses.store');
    Route::get('/reviews', 'reviews')->name('reviews');
    Route::post('/reviews/{review}/reply', 'replyReview')->name('reviews.reply');
    Route::get('/chat', 'chat')->name('chat');
    Route::get('/chat/messages', 'chatMessages')->name('chat.messages');
    Route::post('/chat/reply', 'replyChat')->name('chat.reply');
    Route::get('/settings', 'settings')->name('settings');
    Route::put('/settings', 'updateSettings')->name('settings.update');
    Route::post('/logout', [AuthController::class, 'logoutOwner'])->name('logout');
});

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->controller(AdminController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/profile', 'profile')->name('profile');
    Route::put('/profile', 'updateProfile')->name('profile.update');
    Route::post('/profile/photo', 'updateProfilePhoto')->name('profile.photo');
    Route::get('/owner-applications', 'ownerApplications')->name('applications');
    Route::get('/owner-applications/history', 'ownerApplicationHistory')->name('application-history');
    Route::get('/owner-applications/{user}/document', 'downloadOwnerDocument')->name('applications.document');
    Route::post('/owner-applications/{user}/approve', 'approveOwner')->name('applications.approve');
    Route::post('/owner-applications/{user}/reject', 'rejectOwner')->name('applications.reject');
    Route::get('/accounts', 'accounts')->name('accounts');
    Route::post('/accounts/owners', 'storeOwnerAccount')->name('accounts.owners.store');
    Route::put('/accounts/{user}', 'updateAccount')->name('accounts.update');
    Route::delete('/accounts/{user}', 'deleteAccount')->name('accounts.delete');
    Route::get('/catalog', 'catalog')->name('catalog');
    Route::post('/catalog', 'storeService')->name('catalog.store');
    Route::put('/catalog/{service}', 'updateService')->name('catalog.update');
    Route::delete('/catalog/{service}', 'deleteService')->name('catalog.delete');
    Route::get('/transactions', 'transactions')->name('transactions');
    Route::get('/transactions/latest', 'latestTransactions')->name('transactions.latest');
    Route::get('/reviews', 'reviews')->name('reviews');
    Route::delete('/reviews/{review}', 'deleteReview')->name('reviews.delete');
    Route::get('/messages', 'messages')->name('messages');
    Route::post('/messages/reply', 'replyMessage')->name('messages.reply');
    Route::get('/schedules', 'schedules')->name('schedules');
    Route::get('/topups', 'topups')->name('topups');
    Route::post('/topups/{topup}/approve', 'processTopup')->defaults('decision', 'approve')->name('topups.approve');
    Route::post('/topups/{topup}/reject', 'processTopup')->defaults('decision', 'reject')->name('topups.reject');
    Route::post('/logout', [AuthController::class, 'logoutAdmin'])->name('logout');
});
