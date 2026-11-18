<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{DashboardController, ProductController, CategoryController, PosController, PurchaseController, ContactController, ExpenseController};

Route::get('/', fn() => redirect()->route('dashboard'));
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/sales', [PosController::class, 'sales'])->name('sales.index');
    Route::get('/sales/{sale}', [PosController::class, 'show'])->name('sales.show');
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
});

Route::get('/inventory', [App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
Route::get('/inventory/movements', [App\Http\Controllers\InventoryController::class, 'movements'])->name('inventory.movements');
Route::post('/inventory/adjust', [App\Http\Controllers\InventoryController::class, 'adjust'])->name('inventory.adjust');
Route::get('/contacts/{contact}/ledger', [App\Http\Controllers\AccountsController::class, 'ledger'])->name('contacts.ledger');
Route::post('/accounts/payment', [App\Http\Controllers\AccountsController::class, 'payment'])->name('accounts.payment');
Route::get('/accounts/cashbook', [App\Http\Controllers\AccountsController::class, 'cashbook'])->name('accounts.cashbook');
Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
Route::get('/returns', [App\Http\Controllers\ReturnController::class, 'index'])->name('returns.index');
Route::post('/returns', [App\Http\Controllers\ReturnController::class, 'store'])->name('returns.store');
Route::get('/masters/units', [App\Http\Controllers\MasterController::class, 'units'])->name('masters.units');
Route::post('/masters/units', [App\Http\Controllers\MasterController::class, 'saveUnit'])->name('masters.units.store');
Route::delete('/masters/units/{unit}', [App\Http\Controllers\MasterController::class, 'deleteUnit'])->name('masters.units.delete');
Route::get('/masters/businesses', [App\Http\Controllers\MasterController::class, 'businesses'])->name('masters.businesses');
Route::post('/masters/businesses', [App\Http\Controllers\MasterController::class, 'saveBusiness'])->name('masters.businesses.store');
Route::put('/masters/businesses/{business}', [App\Http\Controllers\MasterController::class, 'updateBusiness'])->name('masters.businesses.update');
Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [App\Http\Controllers\SettingController::class, 'save'])->name('settings.save');
require __DIR__ . '/auth.php';
