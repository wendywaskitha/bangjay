<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SebaranBantuan;
use App\Models\KelompokTani;
use App\Models\JenisBantuan;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Support\Facades\DB;

class PublicDashboardController extends Controller
{
    public function index()
    {
        // Overall statistics
        $totalKelompokTani = KelompokTani::count();
        $totalSebaranBantuan = SebaranBantuan::count();
        $totalJenisBantuan = JenisBantuan::count();
        
        // Recent distributions
        $recentDistributions = SebaranBantuan::with(['kelompokTani', 'jenisBantuans'])
            ->orderBy('tanggal_penetapan', 'desc')
            ->limit(10)
            ->get();
            
        // Top 5 groups receiving most assistance
        $topKelompokTani = KelompokTani::withCount('sebaranBantuans')
            ->orderBy('sebaran_bantuans_count', 'desc')
            ->limit(5)
            ->get();
            
        // Bantuan distribution by kabupaten
        $bantuanByKabupaten = DB::table('sebaran_bantuans')
            ->join('kelompok_tanis', 'sebaran_bantuans.kelompok_tani_id', '=', 'kelompok_tanis.id')
            ->join('desas', 'kelompok_tanis.desa_id', '=', 'desas.id')
            ->join('kecamatans', 'desas.kecamatan_id', '=', 'kecamatans.id')
            ->join('kabupatens', 'kecamatans.kabupaten_id', '=', 'kabupatens.id')
            ->select('kabupatens.nama_kabupaten', DB::raw('count(*) as total'))
            ->groupBy('kabupatens.nama_kabupaten')
            ->orderBy('total', 'desc')
            ->get();
            
        // Distribution by jenis bantuan
        $bantuanByJenis = DB::table('jenis_bantuans')
            ->join('sebaran_bantuan_jenis', 'jenis_bantuans.id', '=', 'sebaran_bantuan_jenis.jenis_bantuan_id')
            ->select('jenis_bantuans.id', 'jenis_bantuans.nama_bantuan', DB::raw('count(*) as total'))
            ->groupBy('jenis_bantuans.id', 'jenis_bantuans.nama_bantuan')
            ->orderBy('total', 'desc')
            ->get();
            
        // Bantuan distribution in the last 12 months
        $bantuanByMonth = DB::table('sebaran_bantuans')
            ->select(
                DB::raw('YEAR(tanggal_penetapan) as year'),
                DB::raw('MONTH(tanggal_penetapan) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('tanggal_penetapan', '>=', now()->subYear())
            ->groupBy(DB::raw('YEAR(tanggal_penetapan)'), DB::raw('MONTH(tanggal_penetapan)'))
            ->orderBy(DB::raw('YEAR(tanggal_penetapan)'))
            ->orderBy(DB::raw('MONTH(tanggal_penetapan)'))
            ->get();
            
        // Data for map visualization
        $kelompokTaniWithBantuan = KelompokTani::with(['desa.kecamatan.kabupaten', 'sebaranBantuans'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()->map(function ($kelompok) {
                // Only include the sebaranBantuans count to avoid loading too much data
                $kelompok->sebaran_bantuans_count = $kelompok->sebaranBantuans->count();
                return $kelompok;
            });

        return view('public.dashboard', compact(
            'totalKelompokTani',
            'totalSebaranBantuan',
            'totalJenisBantuan',
            'recentDistributions',
            'topKelompokTani',
            'bantuanByKabupaten',
            'bantuanByJenis',
            'bantuanByMonth',
            'kelompokTaniWithBantuan'
        ));
    }
}