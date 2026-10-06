<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedItem extends Model
{
    use HasFactory;

    protected $table = 'deleted_items';

    protected $fillable = [
        'nama_barang',
        'no_seri',
        'barcode',
        'jumlah',
        'keterangan',
        'tanggal',
        'status',
        'deleted_at',
    ];
}