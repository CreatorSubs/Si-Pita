<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\AdminUserController;

// ROUTE PUBLIK
Route::get('/', function () {
    return view('pages.landing');
})->name('landing');

Route::get('/certificate/search', [CertificateController::class, 'showAll'])->name('certificate.search');
Route::get('/cek-sertifikat', [CertificateController::class, 'showAll'])->name('public.certificate.check');

// ROUTE AUTHENTICATION
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    return redirect()->route('admin.certificate.create');
});

Route::post('/logout', function () {
    return redirect()->route('login');
})->name('logout');

Route::get('/logout', function () {
    return redirect()->route('login');
});

// ROUTE ADMIN & OWNER
Route::prefix('admin')->name('admin.')->group(function () {
    // Kelola Sertifikat
    Route::get('/certificate/create', [CertificateController::class, 'create'])->name('certificate.create');
    Route::post('/certificate/store', [CertificateController::class, 'store'])->name('certificate.store');
    Route::get('/certificate/editor/{id}', [CertificateController::class, 'editor'])->name('certificate.editor');
    Route::post('/certificate/update-positions/{id}', [CertificateController::class, 'updatePositions'])->name('certificate.update_positions');
    Route::get('/certificate/check', [CertificateController::class, 'showAll'])->name('certificate.show_all');
    Route::get('/certificate/index', [CertificateController::class, 'showAll'])->name('certificate.index');
    Route::get('/certificate/download/{id}', [CertificateController::class, 'download'])->name('certificate.download');
    Route::delete('/certificate/delete/{id}', [CertificateController::class, 'destroy'])->name('certificate.destroy');

    // Fitur Tambahan Khusus Owner & Admin
    Route::get('/certificate/history', [AdminUserController::class, 'history'])->name('certificate.history');
    Route::get('/user/index', [AdminUserController::class, 'index'])->name('user.index');
    Route::get('/user/create', [AdminUserController::class, 'create'])->name('user.create');
    Route::post('/user/store', [AdminUserController::class, 'store'])->name('user.store');
    Route::post('/user/toggle/{id}', [AdminUserController::class, 'toggleStatus'])->name('user.toggle');
});
