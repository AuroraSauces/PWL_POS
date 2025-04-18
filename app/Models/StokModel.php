<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokModel extends Model
{
    // Pastikan nama tabel sesuai dengan yang ada di database
    protected $table = 't_stok'; // Sesuaikan dengan nama tabel yang benar

    // Tentukan primary key jika tidak menggunakan id standar
    protected $primaryKey = 'stok_id';

    // Mengaktifkan timestamps jika ada kolom created_at dan updated_at
    public $timestamps = true;

    // Kolom yang bisa diisi (mass assignable)
    protected $fillable = [
        'barang_id',
        'user_id',
        'stok_tanggal',
        'stok_jumlah',
    ];

    // Relasi dengan model Barang
    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id');
    }
}


