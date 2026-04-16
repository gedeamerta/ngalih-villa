<?php

use App\Http\Controllers\VillaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [VillaController::class, 'index'])->name('home');
Route::get('/villa/{villa}', [VillaController::class, 'show'])->name('villa.show');
Route::get('/villa/{villa}/checkout', [VillaController::class, 'checkout'])->name('villa.checkout');
Route::post('/booking', [VillaController::class, 'storeBooking'])->name('booking.store');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
