<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SebaranBantuan;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Kabupaten;

class SebaranBantuanController extends Controller
{
    public function index()
    {
        $sebaranBantuan = SebaranBantuan::with([
            'kelompokTani.desa.kecamatan.kabupaten',
            'jenisBantuans.kategoriBantuan'
        ])
        ->get();
        
        // Ambil data wilayah untuk filter
        $kabupatens = Kabupaten::all();
        $kecamatans = Kecamatan::all();
        $desas = Desa::all();
        
        // Kelompokkan sebaran bantuan berdasarkan kecamatan
        $groupedSebaran = $sebaranBantuan->groupBy('kelompokTani.desa.kecamatan.nama_kecamatan');
        
        return view('frontend.sebaran-bantuan.index', compact('sebaranBantuan', 'kabupatens', 'kecamatans', 'desas', 'groupedSebaran'));
    }
}