<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcEvent extends Model
{
    use HasFactory;

    protected $table = 'qc_events';

    protected $fillable = [
        'title',
        'event_date',
        'event_time',
        'created_by',
    ];
}