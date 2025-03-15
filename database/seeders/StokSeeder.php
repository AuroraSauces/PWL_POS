<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StokSeeder extends Seeder
{
    public function run()
    {
        $barang = DB::table('m_barang')->pluck('barang_id')->toArray();
        $users = DB::table('m_user')->pluck('user_id')->toArray();

        if (empty($barang) || empty($users)) {
            return;
        }

        for ($i = 0; $i < 15; $i++) {
            DB::table('t_stok')->insert([
                'barang_id' => $barang[array_rand($barang)],
                'user_id' => $users[array_rand($users)],
                'stok_tanggal' => Carbon::now()->subDays(rand(1, 30)),
                'stok_jumlah' => rand(5, 50),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
