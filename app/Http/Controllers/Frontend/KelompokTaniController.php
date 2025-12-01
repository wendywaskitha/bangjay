<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\KelompokTani;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Kabupaten;

class KelompokTaniController extends Controller
{
    public function index()
    {
        $kelompokTanis = KelompokTani::with([
            'desa.kecamatan.kabupaten',
            'kelompokTaniAnggotas.jenisKomoditas',
            'sebaranBantuans.jenisBantuans'
        ])
        ->get();
        
        // Ambil data wilayah untuk filter
        $kabupatens = Kabupaten::all();
        $kecamatans = Kecamatan::all();
        $desas = Desa::all();
        
        return view('frontend.kelompok-tani.index', compact('kelompokTanis', 'kabupatens', 'kecamatans', 'desas'));
    }
    
    public function show($id)
    {
        $kelompokTani = KelompokTani::with([
            'desa.kecamatan.kabupaten',
            'kelompokTaniAnggotas.jenisKomoditas',
            'sebaranBantuans.jenisBantuans.kategoriBantuan'
        ])->findOrFail($id);
        
        return view('frontend.kelompok-tani.show', compact('kelompokTani'));
    }
}