<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\KontakController;
use App\Http\Controllers\Frontend\ProfilController;
use App\Http\Controllers\Frontend\KelompokTaniController;
use App\Http\Controllers\Frontend\KatalogBantuanController;
use App\Http\Controllers\Frontend\SebaranBantuanController;
use App\Http\Controllers\PublicDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Frontend routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');

Route::get('/katalog-bantuan', [KatalogBantuanController::class, 'index'])->name('katalog-bantuan.index');
Route::get('/katalog-bantuan/{slug}', [KatalogBantuanController::class, 'show'])->name('katalog-bantuan.show');

Route::get('/sebaran-bantuan', [SebaranBantuanController::class, 'index'])->name('sebaran-bantuan.index');

Route::get('/kelompok-tani', [KelompokTaniController::class, 'index'])->name('kelompok-tani.index');
Route::get('/kelompok-tani/{id}', [KelompokTaniController::class, 'show'])->name('kelompok-tani.show');

Route::get('/dashboard', [PublicDashboardController::class, 'index'])->name('public.dashboard');

Route::get('/kontak', [KontakController::class, 'index'])->name('kontak.index');
Route::post('/kontak', [KontakController::class, 'store'])->name('kontak.store');

// Routes untuk artikel/blog
use App\Http\Controllers\Frontend\ArtikelController;

Route::get('/artikel', [ArtikelController::class, 'index'])->name('artikel.index');
Route::get('/artikel/{slug}', [ArtikelController::class, 'show'])->name('artikel.show');
