<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenjualanSeeder extends Seeder
{
    public function run()
    {
        $users = DB::table('m_user')->pluck('user_id')->toArray();

        if (empty($users)) {
            return;
        }

        for ($i = 0; $i < 10; $i++) {
            DB::table('t_penjualan')->insert([
                'user_id' => $users[array_rand($users)],
                'penjualan_tanggal' => Carbon::now()->subDays(rand(1, 30)),
                'penjualan_total' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
