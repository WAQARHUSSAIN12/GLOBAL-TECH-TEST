<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// All authenticated routes go here
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================
    // PURCHASE MODULE ROUTES
    // ==========================================
    
    // 1. View Purchases (Accessible by both Admin and standard User)
    Route::get('/purchases', function () {
        // Fetch purchases to list them on a simple page
        $purchases = \App\Models\Purchase::with('items.item', 'items.brand')->latest()->get();
        return view('purchases.index', compact('purchases'));
    })->name('purchases.index');

    // 2. Create Purchase Entry (Strictly protected for Admins using our gate)
    Route::get('/purchases/create', function () {
        return view('purchases.create');
    })->middleware('can:access-admin')->name('purchases.create');


   
    // Admin Restricted Operational Routes
    Route::middleware(['can:access-admin'])->group(function () {
        Route::get('/purchases/{id}/edit', [PurchaseController::class, 'edit']);
        Route::put('/purchases/{id}', [PurchaseController::class, 'update']);
        Route::delete('/purchases/{id}', [PurchaseController::class, 'destroy']);
    });
 
});

require __DIR__.'/auth.php';