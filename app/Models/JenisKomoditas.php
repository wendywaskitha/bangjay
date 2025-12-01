<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisKomoditas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_komoditas',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function kelompokTaniAnggotas(): HasMany
    {
        return $this->hasMany(KelompokTaniAnggota::class);
    }
}