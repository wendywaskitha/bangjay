<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProfilBangJai;

class ProfilController extends Controller
{
    public function index()
    {
        // Ambil profil Bang Jai yang aktif
        $profil = ProfilBangJai::where('is_active', true)->first();
        
        if (!$profil) {
            $profil = new ProfilBangJai();
            $profil->judul = 'Profil Bang Jai';
            $profil->konten_profil = 'Profil Bang Jai akan segera diupdate.';
        }

        return view('frontend.profil.index', compact('profil'));
    }
}