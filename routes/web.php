<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Client;
use App\Http\Controllers\Provider;
use App\Http\Controllers\Admin;

// Publiques
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/providers', [ServiceController::class, 'providers'])->name('providers.index');
Route::get('/provider/{id}', [ServiceController::class, 'providerProfile'])->name('provider.profile');
Route::get('/categories', [ServiceController::class, 'categories'])->name('categories.index');

Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog');
Route::get('/blog/create', [\App\Http\Controllers\BlogController::class, 'create'])->name('blog.create')->middleware('auth');
Route::post('/blog', [\App\Http\Controllers\BlogController::class, 'store'])->name('blog.store')->middleware('auth');
Route::get('/blog/detail/{id}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{id}/like', [\App\Http\Controllers\BlogController::class, 'like'])->name('blog.like')->middleware('auth');
Route::post('/blog/{id}/comment', [\App\Http\Controllers\BlogController::class, 'comment'])->name('blog.comment')->middleware('auth');

Route::get('/contact', function () {
    return view('public.contact');
})->name('contact');
Route::post('/contact', function (\Illuminate\Http\Request $request) {
    return redirect()->route('contact')->with('success', 'Votre message a bien été envoyé ! Nous vous répondrons sous 24h.');
});
Route::get('/faq', function () {
    return view('public.faq');
})->name('faq');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/register/prestataire', [AuthController::class, 'showRegisterPrestataire'])->name('register.prestataire');
    Route::post('/register/prestataire', [AuthController::class, 'registerPrestataire']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Notifications (pour tous les utilisateurs connectés)
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
});

// Client
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [Client\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings', [Client\BookingController::class, 'index'])->name('bookings');
    Route::post('/bookings/{service}', [Client\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/checkout/{service}', [Client\BookingController::class, 'create'])->name('checkout');
    Route::get('/favorites', [Client\DashboardController::class, 'favorites'])->name('favorites');
    Route::post('/favorites/{service}', [Client\DashboardController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::get('/profile', [Client\ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [Client\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/upgrade-prestataire', [Client\ProfileController::class, 'upgradeToProvider'])->name('upgrade');
    Route::post('/reviews/{order}', [Client\ReviewController::class, 'store'])->name('reviews.store');

    // Routes placeholder (pages en construction)
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages');
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages');
    Route::get('/wallet', function () {
        return redirect()->route('client.dashboard');
    })->name('wallet');
    Route::get('/receipt/{order}', [Client\BookingController::class, 'receipt'])->name('receipt');
});


// Prestataire
Route::middleware(['auth', 'role:prestataire'])->prefix('prestataire')->name('prestataire.')->group(function () {
    Route::get('/dashboard', [Provider\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/services', Provider\ServiceController::class);
    Route::get('/orders', [Provider\OrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/{order}/status', [Provider\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::get('/profile', [Provider\ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [Provider\ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/users', Admin\UserController::class);
    Route::resource('/services', Admin\ServiceController::class);
    Route::resource('/categories', Admin\CategoryController::class);
    Route::get('/providers', [Admin\ProviderController::class, 'index'])->name('providers.index');
    Route::put('/providers/{provider}/validate', [Admin\ProviderController::class, 'validateProvider'])->name('providers.validate');
    Route::put('/providers/{provider}/reject', [Admin\ProviderController::class, 'rejectProvider'])->name('providers.reject');
    Route::get('/statistics', [Admin\StatisticsController::class, 'index'])->name('statistics');
});

// API Internes (Communes)
Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/messages/history/{userId}', [\App\Http\Controllers\MessageController::class, 'history']);
    Route::post('/messages/send', [\App\Http\Controllers\MessageController::class, 'store']);
    
    Route::get('/user/badges', function () {
        $user = auth()->user();
        $isProvider = $user->isPrestataire();
        $favCount = $user->favoris()->count();
        $msgCount = $user->messagesReceived()->unread()->count();
        $cmdCount = $isProvider 
            ? \App\Models\Order::where('status', 'pending')->where('prestataire_id', $user->id)->count()
            : \App\Models\Order::whereIn('status', ['pending', 'in_progress', 'confirmed'])->where('client_id', $user->id)->count();
        $globalNotif = $user->unreadNotificationsCount();
        
        return response()->json(compact('favCount', 'msgCount', 'cmdCount', 'globalNotif'));
    });
});
