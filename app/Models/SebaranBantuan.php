<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SebaranBantuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelompok_tani_id',
        'catatan',
        'tanggal_penetapan',
    ];

    protected $casts = [
        'tanggal_penetapan' => 'date',
    ];

    public function kelompokTani(): BelongsTo
    {
        return $this->belongsTo(KelompokTani::class);
    }

    public function jenisBantuans(): BelongsToMany
    {
        return $this->belongsToMany(JenisBantuan::class, 'sebaran_bantuan_jenis')
                    ->withPivot('volume', 'satuan')
                    ->withTimestamps();
    }
}