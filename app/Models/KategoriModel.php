<?php
// app/Models/KategoriModel.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriModel extends Model
{
    use HasFactory;

    protected $table = 'm_kategori';
    protected $primaryKey = 'kategori_id';

    // Tambahkan properti $fillable untuk mass assignment
    protected $fillable = ['kategori_nama', 'kategori_kode'];

    // Relasi ke barang
    public function barangs()
    {
        return $this->hasMany(BarangModel::class, 'kategori_id', 'kategori_id');
    }
}
