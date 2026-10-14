<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\RegisterOwnerController;
use App\Http\Controllers\UserController;

// Rute Halaman Utama (Publik)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rute untuk Tamu (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register-owner', [AuthController::class, 'showRegisterOwner'])->name('register-owner');
    Route::post('/register-owner', [RegisterOwnerController::class, 'register'])->name('register.owner');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'home'])->name('dashboard');
    Route::get('/laundries/{laundry}', [UserController::class, 'showLaundry'])->name('laundry.show');
    Route::post('/laundries/{laundry}/orders', [UserController::class, 'storeOrder'])->name('orders.store');
    Route::post('/orders/{transaction}/confirm-qris', [UserController::class, 'confirmOrderQris'])->name('orders.confirm-qris');
    Route::post('/orders/{transaction}/pay', [UserController::class, 'payOrder'])->name('orders.pay');
    Route::get('/orders/{transaction}', [UserController::class, 'showOrder'])->name('orders.show');
    Route::get('/orders/{transaction}/messages', [UserController::class, 'orderMessages'])->name('orders.messages');
    Route::post('/orders/{transaction}/messages', [UserController::class, 'sendOrderMessage'])->name('orders.messages.store');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/topups', [UserController::class, 'storeTopup'])->name('profile.topups.store');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('owner')->name('owner.')->middleware('auth')->controller(OwnerController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/transactions', 'transactions')->name('transactions');
    Route::get('/transactions/{transaction}', 'transactionDetail')->name('transactions.show');
    Route::post('/transactions/{transaction}/delivery-status', 'updateDeliveryStatus')->name('transactions.delivery-status');
    Route::post('/transactions/{transaction}/weigh', 'weighTransaction')->name('transactions.weigh');
    Route::post('/transactions/{transaction}/status', 'updateTransactionStatus')->name('transactions.status');
    Route::get('/services', 'services')->name('services');
    Route::post('/services', 'storeService')->name('services.store');
    Route::put('/services/{service}', 'updateService')->name('services.update');
    Route::delete('/services/{service}', 'deleteService')->name('services.delete');
    Route::get('/finance', 'finance')->name('finance');
    Route::post('/finance/expenses', 'storeExpense')->name('expenses.store');
    Route::get('/reviews', 'reviews')->name('reviews');
    Route::post('/reviews/{review}/reply', 'replyReview')->name('reviews.reply');
    Route::delete('/reviews/{review}', 'deleteReview')->name('reviews.delete');
    Route::get('/chat', 'chat')->name('chat');
    Route::get('/chat/messages', 'chatMessages')->name('chat.messages');
    Route::post('/chat/reply', 'replyChat')->name('chat.reply');
    Route::get('/settings', 'settings')->name('settings');
    Route::put('/settings', 'updateSettings')->name('settings.update');
});

Route::prefix('admin')->name('admin.')->middleware('auth')->controller(AdminController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/owner-applications', 'ownerApplications')->name('applications');
    Route::post('/owner-applications/{user}/approve', 'approveOwner')->name('applications.approve');
    Route::post('/owner-applications/{user}/reject', 'rejectOwner')->name('applications.reject');
    Route::get('/accounts', 'accounts')->name('accounts');
    Route::put('/accounts/{user}', 'updateAccount')->name('accounts.update');
    Route::delete('/accounts/{user}', 'deleteAccount')->name('accounts.delete');
    Route::get('/catalog', 'catalog')->name('catalog');
    Route::delete('/catalog/{service}', 'deleteService')->name('catalog.delete');
    Route::get('/transactions', 'transactions')->name('transactions');
    Route::get('/reviews', 'reviews')->name('reviews');
    Route::delete('/reviews/{review}', 'deleteReview')->name('reviews.delete');
    Route::get('/messages', 'messages')->name('messages');
    Route::get('/schedules', 'schedules')->name('schedules');
    Route::get('/topups', 'topups')->name('topups');
    Route::post('/topups/{topup}/approve', 'processTopup')->defaults('decision', 'approve')->name('topups.approve');
    Route::post('/topups/{topup}/reject', 'processTopup')->defaults('decision', 'reject')->name('topups.reject');
});

