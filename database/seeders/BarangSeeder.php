<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarangSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['kategori_id' => 1, 'barang_kode' => 'BRG001', 'barang_nama' => 'TV LED 32 Inch', 'harga_beli' => 2000000, 'harga_jual' => 2500000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 1, 'barang_kode' => 'BRG002', 'barang_nama' => 'Kulkas 2 Pintu', 'harga_beli' => 3000000, 'harga_jual' => 3500000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 1, 'barang_kode' => 'BRG003', 'barang_nama' => 'Mesin Cuci 7 Kg', 'harga_beli' => 1800000, 'harga_jual' => 2200000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 1, 'barang_kode' => 'BRG004', 'barang_nama' => 'AC 1 PK', 'harga_beli' => 2500000, 'harga_jual' => 3000000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 1, 'barang_kode' => 'BRG005', 'barang_nama' => 'Blender', 'harga_beli' => 300000, 'harga_jual' => 400000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 2, 'barang_kode' => 'BRG006', 'barang_nama' => 'Meja Kayu', 'harga_beli' => 500000, 'harga_jual' => 700000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 2, 'barang_kode' => 'BRG007', 'barang_nama' => 'Kursi Sofa', 'harga_beli' => 1000000, 'harga_jual' => 1300000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 2, 'barang_kode' => 'BRG008', 'barang_nama' => 'Lemari Pakaian', 'harga_beli' => 1500000, 'harga_jual' => 1800000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 2, 'barang_kode' => 'BRG009', 'barang_nama' => 'Rak Buku', 'harga_beli' => 700000, 'harga_jual' => 900000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 2, 'barang_kode' => 'BRG010', 'barang_nama' => 'Tempat Tidur', 'harga_beli' => 2500000, 'harga_jual' => 3000000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 3, 'barang_kode' => 'BRG011', 'barang_nama' => 'Beras 10Kg', 'harga_beli' => 120000, 'harga_jual' => 140000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 3, 'barang_kode' => 'BRG012', 'barang_nama' => 'Minyak Goreng 2L', 'harga_beli' => 28000, 'harga_jual' => 35000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 3, 'barang_kode' => 'BRG013', 'barang_nama' => 'Gula Pasir 1Kg', 'harga_beli' => 12000, 'harga_jual' => 15000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 3, 'barang_kode' => 'BRG014', 'barang_nama' => 'Mie Instan 1 Dus', 'harga_beli' => 85000, 'harga_jual' => 95000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_id' => 3, 'barang_kode' => 'BRG015', 'barang_nama' => 'Kopi Bubuk 250gr', 'harga_beli' => 25000, 'harga_jual' => 30000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ];

        DB::table('m_barang')->insert($data);
    }
}
