<?php
    // app/Models/BarangModel.php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class BarangModel extends Model
    {
        use HasFactory;

        protected $table = 'm_barang';
        protected $primaryKey = 'barang_id';
        protected $fillable = [
            'kategori_id',
            'barang_kode',
            'barang_nama',
            'harga_beli',
            'harga_jual'
        ];

        /**
         * Get the kategori that owns the barang
         */
        public function kategori()
        {
            return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
        }

        /**
         * Get the penjualan details for the barang
         */
        public function penjualanDetail()
        {
            return $this->hasMany(TransaksiPenjualanDetailModel::class, 'barang_id', 'barang_id');
        }

        /**
         * Get the stok records for the barang
         */
        public function stok()
        {
            return $this->hasMany(StokModel::class, 'barang_id', 'barang_id');
        }

        /**
         * Get the current stock quantity
         */
        public function getCurrentStockAttribute()
        {
            $stokMasuk = $this->stok()->sum('stok_jumlah');
            $stokKeluar = $this->penjualanDetail()->sum('jumlah');

            return $stokMasuk - $stokKeluar;
        }
    }
