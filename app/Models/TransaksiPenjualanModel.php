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
        'user_id',           // Kolom user_id untuk mengetahui siapa yang membuat transaksi
    ];

    // Relasi dengan detail penjualan
    public function detailPenjualan()
    {
        return $this->hasMany(TransaksiPenjualanDetailModel::class, 'penjualan_id');
    }

    // Relasi dengan User (menambahkan informasi user yang terkait dengan transaksi)
    public function user()
    {
        // Menghubungkan user_id di t_penjualan dengan user_id di m_user
        return $this->belongsTo(User::class, 'user_id', 'user_id');
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
