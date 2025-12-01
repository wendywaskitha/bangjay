<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisBantuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_bantuan_id',
        'nama_bantuan',
        'periode_tahun',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'periode_tahun' => 'integer',
        'is_active' => 'boolean',
    ];

    public function kategoriBantuan(): BelongsTo
    {
        return $this->belongsTo(KategoriBantuan::class);
    }

    public function sebaranBantuans(): BelongsToMany
    {
        return $this->belongsToMany(SebaranBantuan::class, 'sebaran_bantuan_jenis')
                    ->withPivot('volume', 'satuan')
                    ->withTimestamps();
    }

    public function katalogBantuans(): HasMany
    {
        return $this->hasMany(KatalogBantuan::class);
    }
}