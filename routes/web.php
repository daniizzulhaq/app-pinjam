<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Karyawan;

// AUTH ROUTES (Laravel Breeze)
require __DIR__.'/auth.php';

// REDIRECT ROOT
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('karyawan.dashboard');
    }

    return redirect()->route('login');
});

// ======================================================
// ADMIN ROUTES
// ======================================================
Route::middleware(['auth', 'is_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('karyawan', Admin\KaryawanController::class);
        Route::resource('bunga', Admin\BungaController::class);
        Route::resource('tenor', Admin\TenorController::class);
        Route::resource('nasabah', Admin\NasabahController::class);
        Route::resource('pinjaman', Admin\PinjamanController::class);

        Route::post('/pinjaman/{pinjaman}/approval',
            [Admin\PinjamanController::class, 'approval'])
            ->name('pinjaman.approval');

        Route::get('/pinjaman-jatuh-tempo',
            [Admin\PinjamanController::class, 'jatuhTempo'])
            ->name('pinjaman.jatuh-tempo');

        Route::resource('pembayaran', Admin\PembayaranController::class);

        Route::get('/pembayaran-denda',
            [Admin\PembayaranController::class, 'denda'])
            ->name('pembayaran.denda');
        
        Route::get('/pembayaran/{pembayaran}/invoice',
            [Admin\PembayaranController::class, 'invoice'])
            ->name('pembayaran.invoice');

        // ==========================
        // PROFIL ADMIN
        // ==========================

        Route::get('/profil', [Admin\ProfilController::class, 'index'])
            ->name('profil.index');

        Route::put('/profil', [Admin\ProfilController::class, 'update'])
            ->name('profil.update');

        // ==========================
        // LAPORAN
        // ==========================

        Route::prefix('laporan')
            ->name('laporan.')
            ->group(function () {

                Route::get('/pinjaman',
                    [Admin\LaporanController::class, 'pinjaman'])
                    ->name('pinjaman');

                Route::get('/pinjaman/pdf',
                    [Admin\LaporanController::class, 'pdfPinjaman'])
                    ->name('pinjaman.pdf');

                Route::get('/pinjaman/excel',
                    [Admin\LaporanController::class, 'excelPinjaman'])
                    ->name('pinjaman.excel');

                Route::get('/pembayaran',
                    [Admin\LaporanController::class, 'pembayaran'])
                    ->name('pembayaran');

                Route::get('/pembayaran/pdf',
                    [Admin\LaporanController::class, 'pdfPembayaran'])
                    ->name('pembayaran.pdf');

                Route::get('/pembayaran/excel',
                    [Admin\LaporanController::class, 'excelPembayaran'])
                    ->name('pembayaran.excel');

                Route::get('/denda',
                    [Admin\LaporanController::class, 'denda'])
                    ->name('denda');
            });
    });


// ======================================================
// KARYAWAN ROUTES
// ======================================================
Route::middleware(['auth', 'is_karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {

        Route::get('/dashboard', [Karyawan\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('nasabah', Karyawan\NasabahController::class);
        Route::resource('pinjaman', Karyawan\PinjamanController::class);

        // Pembayaran nested di bawah pinjaman
        Route::get('/pinjaman/{pinjaman}/pembayaran/create',
            [Karyawan\PembayaranController::class, 'create'])
            ->name('pembayaran.create');

        Route::post('/pinjaman/{pinjaman}/pembayaran',
            [Karyawan\PembayaranController::class, 'store'])
            ->name('pembayaran.store');

        Route::get('/pinjaman/{pinjaman}/pembayaran/{pembayaran}/invoice',
            [Karyawan\PembayaranController::class, 'invoice'])
            ->name('pembayaran.invoice');
    });