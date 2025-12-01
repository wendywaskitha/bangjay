<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use App\Models\Artikel;
use App\Models\Pengumuman;
use App\Models\KatalogBantuan;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil hero banner aktif
        $heroBanners = HeroBanner::where('is_active', true)
            ->orderBy('urutan')
            ->get();
            
        // Ambil artikel terbaru
        $articles = Artikel::where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();
            
        // Ambil pengumuman terbaru
        $announcements = Pengumuman::where('is_active', true)
            ->where('mulai_tayang', '<=', now())
            ->where(function($query) {
                $query->whereNull('selesai_tayang')
                      ->orWhere('selesai_tayang', '>=', now());
            })
            ->orderBy('mulai_tayang', 'desc')
            ->limit(3)
            ->get();
            
        // Ambil katalog bantuan aktif
        $activeKatalog = KatalogBantuan::where('is_active', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->orderBy('tanggal_mulai', 'desc')
            ->limit(6)
            ->get();

        return view('frontend.home', compact('heroBanners', 'articles', 'announcements', 'activeKatalog'));
    }
}