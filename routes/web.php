<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruPdfController;

Route::get('/', function () {
    return view('welcome');
});

// PDF Routes for Guru Panel
Route::middleware(['auth'])->prefix('guru')->group(function () {
    // PDF Generator Pages
    Route::get('/siswa/{siswa}/pdf', [GuruPdfController::class, 'showPdfPage'])->name('guru.siswa.pdf');
    Route::get('/siswa/pdf/all', function() {
        return app(GuruPdfController::class)->showPdfPage();
    })->name('guru.siswa.pdf.all');
    
    // API endpoints for PDF data
    Route::get('/siswa/{siswa}/data', [GuruPdfController::class, 'getSiswaData'])->name('guru.siswa.data');
    Route::get('/siswa/data/all', [GuruPdfController::class, 'getAllSiswaData'])->name('guru.siswa.data.all');
    
    // PDF Stream endpoints (for development/testing)
    Route::get('/siswa/{siswa}/pdf/stream', [GuruPdfController::class, 'streamPdf'])->name('guru.siswa.pdf.stream');
    Route::get('/siswa/{siswa}/pdf/preview', [GuruPdfController::class, 'showPdfPreview'])->name('guru.siswa.pdf.preview');
    
    // Laporan Hasil Belajar Routes
    Route::get('/siswa/{siswa}/laporan-hasil-belajar', [GuruPdfController::class, 'showLaporanHasilBelajar'])->name('guru.siswa.laporan-hasil-belajar');
    Route::get('/siswa/laporan-hasil-belajar/all', function() {
        return app(GuruPdfController::class)->showLaporanHasilBelajar();
    })->name('guru.siswa.laporan-hasil-belajar.all');
    Route::get('/siswa/{siswa}/laporan-hasil-belajar/preview', [GuruPdfController::class, 'showLaporanHasilBelajarPreview'])->name('guru.siswa.laporan-hasil-belajar.preview');
});
