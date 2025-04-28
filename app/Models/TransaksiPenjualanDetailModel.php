<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPenjualanDetailModel extends Model
{
    protected $table = 't_penjualan_detail'; // Nama tabel yang benar

    protected $primaryKey = 'detail_id';

    public $timestamps = true;

    protected $fillable = [
        'penjualan_id', // Relasi ke t_penjualan
        'barang_id',    // Relasi ke barang
        'jumlah',       // Jumlah barang yang dijual
    ];

    // Relasi ke model Barang
    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id');
    }

    // Mengurangi stok berdasarkan jumlah yang dijual
    public function reduceStok($jumlah = null)
{
    $jumlahYangDijual = $jumlah ?? $this->jumlah;

    $stok = StokModel::where('barang_id', $this->barang_id)
        ->where('stok_jumlah', '>', 0)
        ->orderBy('stok_tanggal', 'asc')
        ->get();

    foreach ($stok as $stokItem) {
        if ($jumlahYangDijual <= 0) {
            break;
        }

        $stokYangAda = $stokItem->stok_jumlah;

        if ($stokYangAda >= $jumlahYangDijual) {
            $stokItem->stok_jumlah -= $jumlahYangDijual;
            $stokItem->save();
            break;
        } else {
            $jumlahYangDijual -= $stokYangAda;
            $stokItem->stok_jumlah = 0;
            $stokItem->save();
        }
    }
}
}
