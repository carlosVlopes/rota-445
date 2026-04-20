<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TableController as AdminTableController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\Waiter\OrderController as WaiterOrderController;
use App\Http\Controllers\Waiter\TableController;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------
// Autenticação
// -------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// -------------------------------------------------------
// Área do Garçom
// -------------------------------------------------------
Route::middleware(['auth', 'active', 'role:waiter'])
    ->prefix('garcom')
    ->name('waiter.')
    ->group(function () {
        Route::get('/mesas', [TableController::class, 'index'])->name('tables');
        Route::post('/mesas/{table}/abrir', [TableController::class, 'open'])->name('tables.open');
        Route::delete('/mesas/{table}/fechar', [TableController::class, 'close'])->name('tables.close');

        Route::prefix('pedido/{order}')->name('orders.')->group(function () {
            Route::get('/', [WaiterOrderController::class, 'show'])->name('show');
            Route::post('/item', [WaiterOrderController::class, 'addItem'])->name('item.add');
            Route::delete('/item/{item}', [WaiterOrderController::class, 'removeItem'])->name('item.remove');
            Route::post('/confirmar', [WaiterOrderController::class, 'confirm'])->name('confirm');
        });
    });

// -------------------------------------------------------
// Área do Caixa
// -------------------------------------------------------
Route::middleware(['auth', 'active', 'role:cashier'])
    ->prefix('caixa')
    ->name('cashier.')
    ->group(function () {
        Route::get('/', [CashierOrderController::class, 'index'])->name('index');
        Route::get('/mesa/{table}', [CashierOrderController::class, 'show'])->name('show');
        Route::post('/mesa/{table}/fechar', [CashierOrderController::class, 'close'])->name('close');
        Route::get('/fechadas', [CashierOrderController::class, 'closed'])->name('closed');
        Route::get('/fechadas/{order}', [CashierOrderController::class, 'closedShow'])->name('closed.show');
    });

// -------------------------------------------------------
// Área Admin
// -------------------------------------------------------
Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('produtos', ProductController::class);
        Route::resource('categorias', CategoryController::class);
        Route::resource('usuarios', UserController::class);
        Route::resource('mesas', AdminTableController::class);

        // Pedidos fechados (somente leitura)
        Route::get('/pedidos-fechados', [AdminOrderController::class, 'closed'])->name('pedidos.closed');
        Route::get('/pedidos-fechados/{order}', [AdminOrderController::class, 'show'])->name('pedidos.show');

        // Ativar/desativar produto rapidamente
        Route::patch('/produtos/{product}/toggle', [ProductController::class, 'toggle'])
            ->name('produtos.toggle');
    });

// -------------------------------------------------------
// Redirect raiz → login
// -------------------------------------------------------
Route::get('/', fn () => redirect()->route('login'));
