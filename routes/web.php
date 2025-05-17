<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MutualInformationController;


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

Route::get('/halaman_daftarmi',[MutualInformationController::class, 'halaman_daftar'])->name('HalamanDaftarMI');
Route::post('/normalisasi_data_mi',[MutualInformationController::class, 'normalisasi_data'])->name('NormalisasiDataMI');
