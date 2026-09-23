<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateController;

Route::get('/', function () { return view('pages.landing'); })->name('landing');
Route::get('/search', [CertificateController::class, 'search'])->name('certificate.search');
Route::get('/download/{id}', [CertificateController::class, 'downloadPage'])->name('certificate.download');
Route::get('/pdf-stream/{id}', [CertificateController::class, 'generatePdf'])->name('certificate.pdf.stream');

Auth::routes();

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [CertificateController::class, 'index'])->name('admin.dashboard');
    Route::get('/certificate', [CertificateController::class, 'index'])->name('admin.certificate.index');
    Route::get('/certificate/create', [CertificateController::class, 'create'])->name('admin.certificate.create');
    Route::post('/certificate/store', [CertificateController::class, 'store'])->name('admin.certificate.store');
    Route::get('/certificate/editor/{id}', [CertificateController::class, 'editor'])->name('admin.certificate.editor');
    Route::post('/certificate/editor/{id}', [CertificateController::class, 'updatePositions'])->name('admin.certificate.update_positions');
});
