<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/movimientos', [DashboardController::class, 'store'])->name('movements.store');
Route::post('/etiquetas', [DashboardController::class, 'storeCategory'])->name('categories.store');
Route::put('/etiquetas/{category}', [DashboardController::class, 'updateCategory'])->name('categories.update');
Route::post('/deudas', [DashboardController::class, 'storeDebt'])->name('debts.store');
Route::put('/deudas/{debt}', [DashboardController::class, 'updateDebt'])->name('debts.update');
Route::post('/meta-ahorro', [DashboardController::class, 'storeGoal'])->name('goal.store');
Route::post('/saldo-disponible', [DashboardController::class, 'storeBalance'])->name('balance.store');
Route::delete('/saldo-disponible', [DashboardController::class, 'destroyBalance'])->name('balance.destroy');
Route::delete('/movimientos/{movement}', [DashboardController::class, 'destroy'])->name('movements.destroy');
Route::put('/movimientos/{movement}', [DashboardController::class, 'update'])->name('movements.update');
Route::delete('/etiquetas/{category}', [DashboardController::class, 'destroyCategory'])->name('categories.destroy');
Route::patch('/deudas/{debt}/estado', [DashboardController::class, 'toggleDebt'])->name('debts.toggle');
Route::delete('/deudas/{debt}', [DashboardController::class, 'destroyDebt'])->name('debts.destroy');
