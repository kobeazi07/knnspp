<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

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

Route::get('/',[DashboardController::class, 'dashboard'])->name('HalamanDashboard');
Route::get('/halaman_daftar',[DashboardController::class, 'halaman_daftar'])->name('HalamanDaftar');
Route::post('/normalisasi_data',[DashboardController::class, 'normalisasi_data'])->name('NormalisasiData');
