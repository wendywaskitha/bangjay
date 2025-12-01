<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'slug',
        'isi',
        'mulai_tayang',
        'selesai_tayang',
        'is_active',
    ];

    protected $casts = [
        'mulai_tayang' => 'date',
        'selesai_tayang' => 'date',
        'is_active' => 'boolean',
    ];
}