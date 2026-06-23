<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', fn () => redirect()->route('admin.leads.index'))->name('dashboard');

    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::delete('leads/bulk', [LeadController::class, 'destroyMany'])->name('leads.bulk-destroy');
    Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    Route::patch('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::post('leads/{lead}/conversion', [LeadController::class, 'sendConversion'])->name('leads.conversion');

    Route::get('export/csv', [ExportController::class, 'csv'])->name('export.csv');

    Route::get('settings/telegram', [SettingsController::class, 'edit'])->name('settings.telegram');
    Route::put('settings/telegram', [SettingsController::class, 'update'])->name('settings.telegram.update');
});
