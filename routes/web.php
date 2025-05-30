<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\TransaksiPenjualanController;
use Illuminate\Support\Facades\Route;


// Define global pattern for ID parameters
Route::pattern('id', '[0-9]+');

// Public routes - accessible without authentication
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::post('register', [AuthController::class, 'postRegister'])->name('register');
Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
Route::get('/fix-passwords', [AuthController::class, 'fixLegacyPasswords']);

// Protected routes - require authentication
Route::middleware(['auth'])->group(function() {
    // Dashboard route
    Route::get('/', [WelcomeController::class, 'index']);

    // Logout route (inside auth middleware)
    Route::get('logout', [AuthController::class, 'logout']);

    // Level routes (Admin only)
    //Route::middleware([\App\Http\Middleware\AuthorizeUser::class.':ADM'])->prefix('level')->group(function()
    Route::middleware(['authorize:ADM'])->prefix('level')->group(function () {
        Route::get('/', [LevelController::class, 'index'])->name('level.index');
        Route::post('/list', [LevelController::class, 'list'])->name('level.list');
        Route::get('/create', [LevelController::class, 'create'])->name('level.create');
        Route::get('/create_ajax', [LevelController::class, 'create_ajax'])->name('level.create_ajax');
        Route::post('/store_ajax', [LevelController::class, 'store_ajax'])->name('level.store_ajax');
        Route::post('/', [LevelController::class, 'store'])->name('level.store');
        Route::get('/{id}', [LevelController::class, 'show'])->name('level.show');
        Route::get('/{id}/edit', [LevelController::class, 'edit'])->name('level.edit');
        Route::put('/{id}', [LevelController::class, 'update'])->name('level.update');
        Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax'])->name('level.edit_ajax');
        Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax'])->name('level.update_ajax');
        Route::delete('/{id}', [LevelController::class, 'destroy'])->name('level.destroy');
        Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax'])->name('level.confirm_ajax');
        Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax'])->name('level.delete_ajax');
        Route::get('/import', [LevelController::class, 'import'])->name('level.import');
        Route::post('/import_ajax', [LevelController::class, 'import_ajax'])->name('level.import_ajax');
        Route::get('/export_excel', [LevelController::class, 'export_excel'])->name('level.export');
        Route::get('/export_pdf', [LevelController::class, 'export_pdf'])->name('level.exportpdf');
    });

    // Kategori routes
    Route::group(['prefix' => 'kategori'], function () {
        Route::get('/', [KategoriController::class, 'index'])->name('kategori.index');
        Route::post('/list', [KategoriController::class, 'list'])->name('kategori.list');
        Route::get('/create', [KategoriController::class, 'create'])->name('kategori.create');
        Route::get('/create_ajax', [KategoriController::class, 'create_ajax'])->name('kategori.create_ajax');
        Route::post('/store_ajax', [KategoriController::class, 'store_ajax'])->name('kategori.store_ajax');
        Route::post('/', [KategoriController::class, 'store'])->name('kategori.store');
        Route::get('/{id}', [KategoriController::class, 'show'])->name('kategori.show');
        Route::get('/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax'])->name('kategori.edit_ajax');
        Route::put('/{id}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax'])->name('kategori.update_ajax');
        Route::delete('/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
        Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax'])->name('kategori.confirm_ajax');
        Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax'])->name('kategori.delete_ajax');
        Route::get('/import', [Kategoricontroller::class, 'import'])->name('user.import');
        Route::post('/import_ajax', [KategoriController::class, 'import_ajax'])->name('user.import_ajax');
        Route::get('/export_excel', [KategoriController::class, 'export_excel'])->name('kategori.export');
        Route::get('/export_pdf', [KategoriController::class, 'export_pdf'])->name('kategori.exportpdf');
    });

    // Supplier routes
    Route::group(['prefix' => 'supplier'], function () {
        Route::get('/', [SupplierController::class, 'index'])->name('supplier.index');
        Route::post('/list', [SupplierController::class, 'list'])->name('supplier.list');
        Route::get('/create', [SupplierController::class, 'create'])->name('supplier.create');
        Route::get('/create_ajax', [SupplierController::class, 'create_ajax'])->name('supplier.create_ajax');
        Route::post('/store_ajax', [SupplierController::class, 'store_ajax'])->name('supplier.store_ajax');
        Route::post('/', [SupplierController::class, 'store'])->name('supplier.store');
        Route::get('/{id}', [SupplierController::class, 'show'])->name('supplier.show');
        Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
        Route::put('/{id}', [SupplierController::class, 'update'])->name('supplier.update');
        Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
        Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax'])->name('supplier.edit_ajax');
        Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax'])->name('supplier.update_ajax');
        Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax'])->name('supplier.confirm_ajax');
        Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax'])->name('supplier.delete_ajax');
        Route::get('/{id}/show_ajax', [SupplierController::class, 'show_ajax'])->name('supplier.show_ajax');
        Route::get('/import', [Suppliercontroller::class, 'import'])->name('user.import');
        Route::post('/import_ajax', [SupplierController::class, 'import_ajax'])->name('user.import_ajax');
        Route::get('/export_excel', [SupplierController::class, 'export_excel'])->name('supplier.export');
        Route::get('/export_pdf', [SupplierController::class, 'export_pdf'])->name('supplier.exportpdf');
    });

    // Barang routes
        Route::middleware(['authorize:ADM,MNG'])->prefix('barang')->group(function () {
        Route::get('/', [BarangController::class, 'index'])->name('barang.index');
        Route::post('/list', [BarangController::class, 'list'])->name('barang.list');
        Route::get('/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/create_ajax', [BarangController::class, 'create_ajax'])->name('barang.create_ajax');
        Route::post('/store_ajax', [BarangController::class, 'store_ajax'])->name('barang.store_ajax');
        Route::get('/{id}', [BarangController::class, 'show'])->name('barang.show');
        Route::get('/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/{id}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
        Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax'])->name('barang.edit_ajax');
        Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax'])->name('barang.update_ajax');
        Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax'])->name('barang.confirm_ajax');
        Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax'])->name('barang.delete_ajax');
        Route::get('/import', [BarangController::class, 'import'])->name('barang.import');
        Route::post('/import_ajax', [BarangController::class, 'import_ajax'])->name('barang.import_ajax');
        Route::get('/export_excel', [BarangController::class, 'export_excel'])->name('barang.export');
        Route::get('/export_pdf', [BarangController::class, 'export_pdf'])->name('barang.exportpdf');
    });

    // User routes
        Route::middleware(['authorize:ADM'])->prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::post('/list', [UserController::class, 'list'])->name('user.list');
        Route::get('/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/', [UserController::class, 'store'])->name('user.store');
        Route::get('/create_ajax', [UserController::class, 'create_ajax'])->name('user.create_ajax');
        Route::post('/ajax', [UserController::class, 'store_ajax'])->name('user.store_ajax');
        Route::get('/{id}', [UserController::class, 'show'])->name('user.show');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('user.update');
        Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax'])->name('user.edit_ajax');
        Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax'])->name('user.update_ajax');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('user.destroy');
        Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax'])->name('user.confirm_ajax');
        Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax'])->name('user.delete_ajax');
        Route::get('/import', [UserController::class, 'import'])->name('user.import');
        Route::post('/import_ajax', [UserController::class, 'import_ajax'])->name('user.import_ajax');
        Route::get('/export_excel', [UserController::class, 'export_excel'])->name('user.export');
        Route::get('/export_pdf', [UserController::class, 'export_pdf'])->name('user.exportpdf');
    });

    Route::middleware(['authorize:ADM,MNG,STF'])->prefix('stok')->group(function () {
        Route::get('/', [StokController::class, 'index'])->name('stok.index');
        Route::get('/create_ajax', [StokController::class, 'create_ajax'])->name('stok.create');
        Route::post('/', [StokController::class, 'store'])->name('stok.store');
        Route::get('/edit/{id}', [StokController::class, 'edit'])->name('stok.edit');
        Route::post('/update/{id}', [StokController::class, 'update'])->name('stok.update');
        Route::get('/delete/{id}', [StokController::class, 'destroy'])->name('stok.destroy');
        Route::post('/store_ajax', [StokController::class, 'store_ajax'])->name('stok.store_ajax');
        Route::get('/list', [StokController::class, 'list'])->name('stok.list');
        Route::delete('/stok/{id}', [StokController::class, 'delete_ajax'])->name('stok.delete');

    });

    Route::middleware(['auth', 'authorize:ADM,MNG,STF'])->prefix('penjualan')->group(function () {
        Route::get('/', [TransaksiPenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/create', [TransaksiPenjualanController::class, 'create'])->name('penjualan.create');
        Route::get('/create_ajax', [TransaksiPenjualanController::class, 'create_ajax'])->name('penjualan.create_ajax');
        Route::post('/store_ajax', [TransaksiPenjualanController::class, 'store_ajax'])->name('penjualan.store_ajax');
        Route::post('/', [TransaksiPenjualanController::class, 'store'])->name('penjualan.store');
        Route::post('/cek-stok', [TransaksiPenjualanController::class, 'cekStok'])->name('penjualan.cek-stok');
        Route::get('/penjualan/list', [TransaksiPenjualanController::class, 'list'])->name('penjualan.list');
    });
});
