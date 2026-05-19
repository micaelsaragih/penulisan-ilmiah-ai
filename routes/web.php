<?php

/**
 * Web Routes
 *
 * File ini mendefinisikan semua route untuk aplikasi web.
 * Route-route ini dimuat oleh RouteServiceProvider dan
 * secara otomatis mendapat middleware group "web".
 *
 * Aplikasi: Sistem Pembimbing Penulisan Artikel Ilmiah
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Static Pages Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/petunjuk', [PageController::class, 'petunjuk'])->name('petunjuk');
Route::get('/tentang', [PageController::class, 'tentang'])->name('tentang');

/*
|--------------------------------------------------------------------------
| Route Analisis Teks
|--------------------------------------------------------------------------
| Menerima POST request berisi teks artikel dan mengembalikan
| hasil analisis (statistik, struktur, keterbacaan, saran AI).
| Controller: ArticleController@analyze
|
*/
Route::post('/analyze', [ArticleController::class, 'analyze'])->name('analyze');

