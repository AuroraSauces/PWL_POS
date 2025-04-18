<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPenjualanModel extends Model
{
    protected $table = 't_penjualan'; // Nama tabel yang benar

    protected $primaryKey = 'penjualan_id';

    public $timestamps = true;

    // Menyesuaikan dengan kolom yang ada pada tabel
    protected $fillable = [
        'penjualan_tanggal', // Gunakan penjualan_tanggal yang ada di tabel
        'pembeli',           // Menambahkan kolom pembeli
    ];

    // Relasi dengan detail penjualan
    public function detailPenjualan()
    {
        return $this->hasMany(TransaksiPenjualanDetailModel::class, 'penjualan_id');
    }

    // Menghitung total transaksi
    public function totalTransaksi()
    {
        return $this->detailPenjualan->sum(function ($detail) {
            // Mengambil harga_jual dari model BarangModel melalui relasi
            return $detail->jumlah * $detail->barang->harga_jual; // Menggunakan harga_jual yang benar
        });
    }
}
