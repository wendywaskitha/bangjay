<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelompokTaniAnggota extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelompok_tani_id',
        'nama_anggota',
        'jabatan',
        'no_hp',
        'luas_lahan',
        'jenis_komoditas_id',
    ];

    protected $casts = [
        'jabatan' => 'string',
        'luas_lahan' => 'decimal:2',
    ];

    public function kelompokTani(): BelongsTo
    {
        return $this->belongsTo(KelompokTani::class);
    }

    public function jenisKomoditas(): BelongsTo
    {
        return $this->belongsTo(JenisKomoditas::class);
    }
}