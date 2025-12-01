<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\KatalogBantuan;
use App\Models\JenisBantuan;

class KatalogBantuanController extends Controller
{
    public function index()
    {
        $katalogBantuan = KatalogBantuan::with('jenisBantuan.kategoriBantuan')
            ->where('is_active', true)
            ->get();
            
        // Kelompokkan berdasarkan status (aktif, akan datang, selesai)
        $activeKatalog = $katalogBantuan->filter(function ($katalog) {
            return $katalog->tanggal_mulai <= now() && $katalog->tanggal_selesai >= now();
        });
        
        $upcomingKatalog = $katalogBantuan->filter(function ($katalog) {
            return $katalog->tanggal_mulai > now();
        });
        
        $completedKatalog = $katalogBantuan->filter(function ($katalog) {
            return $katalog->tanggal_selesai < now();
        });

        return view('frontend.katalog-bantuan.index', compact('activeKatalog', 'upcomingKatalog', 'completedKatalog'));
    }
    
    public function show($slug)
    {
        $katalogBantuan = KatalogBantuan::with('jenisBantuan.kategoriBantuan')->where('slug', $slug)->firstOrFail();
        
        return view('frontend.katalog-bantuan.show', compact('katalogBantuan'));
    }
}