<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierModel extends Model
{
    use HasFactory;

    protected $table = 'm_supplier';
    protected $primaryKey = 'supplier_id';

    // Set timestamps to true since we have created_at and updated_at columns
    public $timestamps = true;

    protected $fillable = [
        'supplier_kode',
        'supplier_nama',
        'supplier_alamat',
        'supplier_kontak'
    ];
}
