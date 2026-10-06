<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang terhubung
    protected $table = 'items';

    // Kolom yang diizinkan untuk diisi data (Mass Assignment)
    protected $fillable = [
        'nama_barang',
        'no_seri',
        'barcode',
        'jumlah',
        'keterangan',
        'tanggal',
        'status',
    ];
}