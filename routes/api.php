<?php

use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\TransaksiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterController::class)->name('register');
Route::post('/register1', RegisterController::class)->name('register1');
Route::post('/login', LoginController::class)->name('login');
Route::post('/logout', LogoutController::class)->name('logout');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('levels', [LevelController::class, 'index']);
Route::post('levels', [LevelController::class, 'store']);
Route::get('levels/{level}', [LevelController::class, 'show']);
Route::put('levels/{level}', [LevelController::class, 'update']);
Route::delete('levels/{level}', [LevelController::class, 'destroy']);

// User Routes
Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);
Route::get('users/{id}', [UserController::class, 'show']);
Route::put('users/{id}', [UserController::class, 'update']);
Route::delete('users/{id}', [UserController::class, 'destroy']);

// Kategori Routes
Route::get('kategoris', [KategoriController::class, 'index']);
Route::post('kategoris', [KategoriController::class, 'store']);
Route::get('kategoris/{id}', [KategoriController::class, 'show']);
Route::put('kategoris/{id}', [KategoriController::class, 'update']);
Route::delete('kategoris/{id}', [KategoriController::class, 'destroy']);

// Barang Routes
Route::get('barangs', [BarangController::class, 'index']);
Route::post('barangs', [BarangController::class, 'store']);
Route::get('barangs/{id}', [BarangController::class, 'show']);
Route::put('barangs/{id}', [BarangController::class, 'update']);
Route::delete('barangs/{id}', [BarangController::class, 'destroy']);

// Transaksi Routes
Route::get('transaksi', [TransaksiController::class, 'index']);
Route::post('transaksi', [TransaksiController::class, 'store']);
Route::get('transaksi/{id}', [TransaksiController::class, 'show']);
Route::put('transaksi/{id}', [TransaksiController::class, 'update']);
Route::delete('transaksi/{id}', [TransaksiController::class, 'destroy']);
