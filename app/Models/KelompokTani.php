<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class KelompokTani extends Model
{
    use HasFactory;

    protected $fillable = [
        'desa_id',
        'nama_kelompok',
        'jumlah_anggota',
        'profil',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'jumlah_anggota' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function kelompokTaniAnggotas(): HasMany
    {
        return $this->hasMany(KelompokTaniAnggota::class);
    }

    public function sebaranBantuans(): HasMany
    {
        return $this->hasMany(SebaranBantuan::class);
    }
}