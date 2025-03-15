<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangController;



Route::get('/', [WelcomeController::class, 'index']);

Route::get('/level', [LevelController::class, 'index'])->name('level.index');
Route::post('/level/list', [LevelController::class, 'list'])->name('level.list');
Route::get('/level/create', [LevelController::class, 'create'])->name('level.create');
Route::post('/level', [LevelController::class, 'store'])->name('level.store');
Route::get('/level/{id}/edit', [LevelController::class, 'edit'])->name('level.edit');
Route::put('/level/{id}', [LevelController::class, 'update'])->name('level.update');
Route::delete('/level/{id}', [LevelController::class, 'destroy'])->name('level.destroy');

Route::get('kategori', [KategoriController::class, 'index']);
Route::get('kategori/create', [KategoriController::class, 'create']);
Route::post('kategori', [KategoriController::class, 'store']);
Route::get('kategori/{id}/edit', [KategoriController::class, 'edit']);
Route::put('kategori/{id}', [KategoriController::class, 'update']);
Route::delete('kategori/{id}', [KategoriController::class, 'destroy']);
Route::post('kategori/list', [KategoriController::class, 'list'])->name('kategori.list');

Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
Route::get('/supplier/{id}', [SupplierController::class, 'show'])->name('supplier.show');
Route::get('/supplier/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
Route::post('/supplier/list', [SupplierController::class, 'list'])->name('supplier.list');

Route::get('barang', [App\Http\Controllers\BarangController::class, 'index']);
Route::post('barang/list', [App\Http\Controllers\BarangController::class, 'list'])->name('barang.list');
Route::get('barang/create', [App\Http\Controllers\BarangController::class, 'create']);
Route::post('barang', [App\Http\Controllers\BarangController::class, 'store']);
Route::get('barang/{id}', [App\Http\Controllers\BarangController::class, 'show']);
Route::get('barang/{id}/edit', [App\Http\Controllers\BarangController::class, 'edit']);
Route::put('barang/{id}', [App\Http\Controllers\BarangController::class, 'update']);
Route::delete('barang/{id}', [App\Http\Controllers\BarangController::class, 'destroy']);

Route::group(['prefix' => 'user'], function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/list', [UserController::class, 'list']);
    Route::get('/create', [UserController::class, 'create']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/create_ajax', [UserController::class, 'create_ajax']);
    Route::post('/ajax', [UserController::class, 'store_ajax']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::get('/{id}/edit', [UserController::class, 'edit']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']);

});

