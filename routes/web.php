<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SearchMefController;
use App\Http\Controllers\ConsultaHogarController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('search');
})->name('home');

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/search/process', [SearchController::class, 'process'])->name('search.process');

Route::get('/searchMef', [SearchMefController::class, 'index'])->name('searchMef');
Route::post('/searchMef/process', [SearchMefController::class, 'process'])->name('searchMef.process');

Route::post('/search/details', [SearchController::class, 'getDetails'])->name('search.details');

// Nuevas rutas para ConsultaHogar
Route::get('/consultaHogar', [ConsultaHogarController::class, 'index'])->name('consultaHogar');
Route::post('/consultaHogar/process', [ConsultaHogarController::class, 'process'])->name('consultaHogar.process');
Route::post('/consultaHogar/details', [ConsultaHogarController::class, 'getDetails'])->name('consultaHogar.details');
Route::get('/consultaHogar/download', [ConsultaHogarController::class, 'download'])->name('consultaHogar.download');
Route::post('/consultaHogar/download-ficha', [ConsultaHogarController::class, 'downloadFicha'])->name('consultaHogar.downloadFicha');

// Rutas para los logros
Route::post('/consulta-hogar/logros-integrante', [ConsultaHogarController::class, 'getLogrosIntegrante'])->name('consultaHogar.logros-integrante');
Route::post('/consulta-hogar/detalle-logro', [ConsultaHogarController::class, 'getDetalleLogro'])->name('consultaHogar.detalle-logro');
Route::get('/getDimensiones', [ConsultaHogarController::class, 'getDimensiones'])->name('getDimensiones');

// Ruta de logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('home');
})->name('logout');
