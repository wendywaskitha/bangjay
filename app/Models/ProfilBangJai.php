<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilBangJai extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'konten_profil',
        'foto_profil',
        'foto_banner',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}