<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPenjualanDetailModel extends Model
{
    protected $table = 't_penjualan_detail';
    protected $primaryKey = 'detail_id';

    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'jumlah'
    ];

    // Tambahkan properti untuk flag pengembalian stok
    protected $is_returning_stock = false;

    // Relasi ke model Barang
    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id');
    }

    // BUAT METODE UNTUK MELACAK PERUBAHAN STOK
    // Ini akan mencatat perubahan stok ke log untuk debugging
    public function trackStok($action, $jumlah)
    {
        $message = sprintf(
            "[%s] Barang ID: %d, Action: %s, Jumlah: %d",
            date('Y-m-d H:i:s'),
            $this->barang_id,
            $action,
            $jumlah
        );

        // Simpan ke file log khusus
        file_put_contents(
            storage_path('logs/stok_tracking.log'),
            $message . PHP_EOL,
            FILE_APPEND
        );
    }

    // GANTI METODE reduceStok MENJADI SEPERTI INI
    public function reduceStok($jumlah = null)
    {
        // Hanya untuk transaksi baru, BUKAN update
        if ($this->wasRecentlyCreated) {
            $jumlahYangDijual = $jumlah ?? $this->jumlah;

            // Tracking untuk debugging
            $this->trackStok('Mengurangi (baru)', $jumlahYangDijual);

            // Buat entri stok negatif
            return StokModel::create([
                'barang_id' => $this->barang_id,
                'stok_tanggal' => now(),
                'stok_jumlah' => -$jumlahYangDijual,
                'user_id' => $this->penjualan->user_id ?? 1,
            ]);
        }

        // Jika bukan transaksi baru, catat saja untuk debugging
        $this->trackStok('Mencoba mengurangi (update)', $jumlah ?? $this->jumlah);
        return null;
    }

    // Relasi ke transaksi
    public function penjualan()
    {
        return $this->belongsTo(TransaksiPenjualanModel::class, 'penjualan_id', 'penjualan_id');
    }

    // Tambahkan method ini untuk mengatur flag
    public function setIsReturningStock($value = true)
    {
        $this->is_returning_stock = $value;
        return $this;
    }

    // Tambahkan method ini di dalam class
    protected static function booted()
    {
        // Saat detail transaksi dihapus
        static::deleting(function ($model) {
            // Kembalikan stok hanya jika flag is_returning_stock aktif
            if ($model->is_returning_stock) {
                // Buat entri stok positif untuk mengembalikan stok
                StokModel::create([
                    'barang_id' => $model->barang_id,
                    'stok_tanggal' => now(),
                    'stok_jumlah' => $model->jumlah, // Nilai positif untuk menambah stok
                    'user_id' => $model->penjualan->user_id ?? 1,
                ]);

                // Log untuk debugging
                $model->trackStok('Mengembalikan (hapus)', $model->jumlah);
            }
        });

        // Saat detail transaksi baru dibuat
        static::created(function ($model) {
            // Kurangi stok saat transaksi baru dibuat
            $model->reduceStok();
        });
    }
}
