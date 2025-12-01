<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    public function index()
    {
        return view('frontend.kontak.index');
    }
    
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'pesan' => 'required|string',
        ]);
        
        // Di sini bisa ditambahkan logika untuk menyimpan pesan kontak
        // Misalnya menyimpan ke database atau mengirim email
        
        // Untuk sementara, kita hanya redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Pesan Anda telah terkirim. Terima kasih telah menghubungi kami.');
    }
}