<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Marketplace\PropertyController as MarketplacePropertyController;
use App\Http\Controllers\Marketplace\ReservationController as MarketplaceReservationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Anfitriao\DashboardController as AnfitriaoDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('marketplace.index');
});

// Marketplace público
Route::prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/', [MarketplacePropertyController::class, 'index'])->name('index');
    Route::get('/busca', [MarketplacePropertyController::class, 'search'])->name('search');
    Route::get('/imovel/{property}', [MarketplacePropertyController::class, 'show'])->name('property.show');
    Route::post('/calcular-preco', [MarketplacePropertyController::class, 'calculatePrice'])->name('calculate.price');
    Route::post('/reservar', [MarketplaceReservationController::class, 'store'])->name('reservation.store');
    Route::get('/localizar-reserva', [MarketplaceReservationController::class, 'locateForm'])->name('locate-reservation');
    Route::get('/reserva/codigo/{codigo}', [MarketplaceReservationController::class, 'showByCode'])->name('reservation.by-code');
    Route::get('/reserva/{reservation}', [MarketplaceReservationController::class, 'show'])->name('reservation.show');
});

// Perfil (Breeze)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Redirecionar /dashboard conforme tipo de usuário
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    $owner = \App\Models\Owner::where('user_id', $user->id)->first();
    if ($owner) {
        return redirect()->route('anfitriao.dashboard');
    }
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Área do anfitrião (dono que conecta Stays e vê seus imóveis)
Route::middleware(['auth', 'verified'])->prefix('anfitriao')->name('anfitriao.')->group(function () {
    Route::get('/dashboard', [AnfitriaoDashboardController::class, 'index'])->name('dashboard');
    Route::get('/conectar-stays', [AnfitriaoDashboardController::class, 'conectarStays'])->name('conectar-stays');
    Route::put('/conectar-stays', [AnfitriaoDashboardController::class, 'salvarStays'])->name('conectar-stays.salvar');
    Route::post('/sincronizar', [AnfitriaoDashboardController::class, 'sincronizar'])->name('sincronizar');
    Route::get('/imoveis', [AnfitriaoDashboardController::class, 'imoveis'])->name('imoveis');
    Route::get('/reservas', [AnfitriaoDashboardController::class, 'reservas'])->name('reservas');
});

// Admin (requer autenticação + role admin ou superadmin)
Route::middleware(['auth', 'verified', 'admin.role'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('owners', OwnerController::class);
    Route::post('owners/{owner}/test-connection', [OwnerController::class, 'testConnection'])->name('owners.test-connection');
    Route::post('owners/{owner}/sync', [OwnerController::class, 'sync'])->name('owners.sync');

    Route::resource('properties', PropertyController::class);
    Route::post('properties/{property}/toggle-status', [PropertyController::class, 'toggleStatus'])->name('properties.toggle-status');

    Route::resource('reservations', ReservationController::class);

    Route::resource('markup-rules', \App\Http\Controllers\Admin\MarkupRuleController::class);
});

require __DIR__.'/auth.php';
