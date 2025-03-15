<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('m_kategori')->delete();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            ['kategori_kode' => 'ELEC', 'kategori_nama' => 'Elektronik', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_kode' => 'FURN', 'kategori_nama' => 'Furniture', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_kode' => 'FOOD', 'kategori_nama' => 'Makanan & Minuman', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_kode' => 'CLTH', 'kategori_nama' => 'Pakaian', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['kategori_kode' => 'TOYS', 'kategori_nama' => 'Mainan', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('m_kategori')->insert($data);
    }
}
