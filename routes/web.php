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
});
