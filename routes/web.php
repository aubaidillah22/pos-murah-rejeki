<?php

use App\Livewire\Category\CategoryList;
use App\Livewire\Customer\CustomerList;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Expense\ExpenseList;
use App\Livewire\Pos\Index as PosIndex;
use App\Livewire\Product\ProductList;
use App\Livewire\Purchase\PurchaseList;
use App\Livewire\Report\ReportIndex;
use App\Livewire\Supplier\SupplierList;
use App\Livewire\Transaction\TransactionList;
use App\Livewire\StockOpnameList;
use App\Livewire\Unit\UnitList;
use App\Livewire\User\UserList;
use App\Livewire\Outlet\OutletList;
use App\Livewire\Setting\Index as SettingIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authentication routes (using Laravel's built-in auth)
use App\Http\Controllers\AuthController;

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');

    // POS / Cashier
    Route::get('/pos', PosIndex::class)->name('pos');

    // Products (with permissions)
    Route::get('/products', ProductList::class)->name('products')
        ->middleware('permission:view-products');

    // Categories
    Route::get('/categories', CategoryList::class)->name('categories')
        ->middleware('permission:view-categories');

    // Units
    Route::get('/units', UnitList::class)->name('units')
        ->middleware('permission:view-units');

    // Customers
    Route::get('/customers', CustomerList::class)->name('customers')
        ->middleware('permission:view-customers');

    // Suppliers
    Route::get('/suppliers', SupplierList::class)->name('suppliers')
        ->middleware('permission:view-suppliers');

    // Purchase Orders
    Route::get('/purchases', PurchaseList::class)->name('purchases')
        ->middleware('permission:view-purchase-orders');

    // Transactions
    Route::get('/transactions', TransactionList::class)->name('transactions')
        ->middleware('permission:view-transactions');
    Route::get('/transactions/{id}/print', function ($id) {
        $transaction = \App\Models\Transaction::with(['details.product', 'customer', 'user'])
            ->findOrFail($id);

        activity()->log('Cetak ulang struk: ' . $transaction->invoice_number);

        return view('exports.receipt-print', compact('transaction'));
    })->name('transactions.print')->middleware('permission:view-transactions');

    // Reports
    Route::get('/reports', ReportIndex::class)->name('reports')
        ->middleware('permission:view-reports');

    // Stock Opname
    Route::get('/stock-opname', StockOpnameList::class)->name('stock-opname')
        ->middleware('permission:view-stock-opname');

    // Expenses
    Route::get('/expenses', ExpenseList::class)->name('expenses')
        ->middleware('permission:view-expenses');

    // Settings (Admin only)
    Route::get('/settings', SettingIndex::class)->name('settings')
        ->middleware('role:Admin');

    // Users management (Admin only)
    Route::get('/users', UserList::class)->name('users')
        ->middleware('role:Admin');

    // Outlets (Admin only)
    Route::get('/outlets', OutletList::class)->name('outlets')
        ->middleware('role:Admin');
});
