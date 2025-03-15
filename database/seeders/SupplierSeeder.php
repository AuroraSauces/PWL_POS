<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('m_supplier')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            [
                'supplier_kode' => 'SUP001',
                'supplier_nama' => 'PT. Elektronica',
                'supplier_alamat' => 'Jl. Teknologi No. 1, Jakarta',
                'supplier_kontak' => '08123456999',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'supplier_kode' => 'SUP002',
                'supplier_nama' => 'Barang Murah',
                'supplier_alamat' => 'Jl. Ekonomi No. 23, Bandung',
                'supplier_kontak' => '08234568888',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'supplier_kode' => 'SUP003',
                'supplier_nama' => 'Galon Serbaguna',
                'supplier_alamat' => 'Jl. Air Sejahtera No. 5, Surabaya',
                'supplier_kontak' => '08345677777',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('m_supplier')->insert($data);
    }
}
