@extends('layouts.app')

@section('title', 'Kelompok Tani')

@section('content')
<style>
    :root {
        --primary-green: #18872e;
        --primary-green-dark: #146624;
        --primary-green-light: #2ba245;
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
        color: white;
        padding: 3rem 0;
        margin-bottom: 3rem;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 4px 20px rgba(24, 135, 46, 0.2);
    }

    .page-header h1 {
        font-size: clamp(2rem, 5vw, 2.75rem);
        font-weight: 800;
        margin-bottom: 0.75rem;
        letter-spacing: -0.02em;
    }

    .page-header p {
        font-size: 1.125rem;
        margin-bottom: 0;
        opacity: 0.95;
    }

    /* Breadcrumb */
    .breadcrumb-custom {
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .breadcrumb-custom .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item a {
        color: var(--primary-green);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .breadcrumb-item a:hover {
        color: var(--primary-green-dark);
    }

    /* Filter Section */
    .filter-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .filter-section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .filter-section-title i {
        color: var(--primary-green);
        font-size: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.9375rem;
    }

    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        border-color: var(--primary-green-light);
        box-shadow: 0 0 0 0.2rem rgba(24, 135, 46, 0.1);
    }

    .btn-reset-filter {
        background: white;
        border: 2px solid var(--primary-green);
        color: var(--primary-green);
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-reset-filter:hover {
        background: var(--primary-green);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(24, 135, 46, 0.3);
    }

    /* Stats Bar */
    .stats-bar {
        background: linear-gradient(135deg, #f1f8f4, #e8f5e9);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-around;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary-green);
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Kelompok Card */
    .kelompok-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        height: 100%;
        background: white;
    }

    .kelompok-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
    }

    .kelompok-card-header {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        color: white;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .kelompok-card-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .kelompok-name {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .kelompok-card-body {
        padding: 1.75rem;
    }

    .info-item {
        display: flex;
        align-items: start;
        gap: 0.875rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid #e9ecef;
    }

    .info-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .info-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, rgba(24, 135, 46, 0.1), rgba(43, 162, 69, 0.15));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-green);
        font-size: 1.125rem;
        flex-shrink: 0;
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.8125rem;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }

    /* Bantuan List */
    .bantuan-section {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e9ecef;
    }

    .bantuan-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #495057;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .bantuan-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .bantuan-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .bantuan-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }

    .bantuan-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
        white-space: nowrap;
    }

    .bantuan-name {
        font-size: 0.875rem;
        color: #495057;
        font-weight: 600;
        margin: 0;
    }

    /* Card Footer */
    .kelompok-card-footer {
        padding: 1.25rem 1.75rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .btn-detail {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        width: 100%;
        justify-content: center;
    }

    .btn-detail:hover {
        background: linear-gradient(135deg, var(--primary-green-dark), var(--primary-green));
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(24, 135, 46, 0.4);
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state-icon {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(24, 135, 46, 0.1), rgba(43, 162, 69, 0.15));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 3rem;
        color: var(--primary-green);
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.75rem;
    }

    .empty-state-text {
        color: #6c757d;
        font-size: 1.0625rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .filter-section {
            padding: 1.5rem;
        }

        .stats-bar {
            gap: 1rem;
        }

        .stat-value {
            font-size: 1.75rem;
        }

        .kelompok-card-body {
            padding: 1.5rem;
        }

        .btn-reset-filter {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .filter-section {
            padding: 1.25rem;
        }

        .kelompok-card-header {
            padding: 1.25rem;
        }

        .kelompok-name {
            font-size: 1.125rem;
        }

        .info-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    /* Loading State */
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    .loading-overlay.active {
        display: flex;
    }
</style>

<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="text-center">
                <i class="bi bi-people-fill me-2"></i>Kelompok Tani
            </h1>
            <p class="text-center">Daftar kelompok tani yang terdaftar dan bantuan yang diterima</p>
        </div>
    </div>
</div>

<div class="container py-4">
    <!-- Breadcrumb -->
    <div class="breadcrumb-custom animate-fade-in">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">
                        <i class="bi bi-house-door me-1"></i>Beranda
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Kelompok Tani</li>
            </ol>
        </nav>
    </div>

    <!-- Stats Bar -->
    <div class="stats-bar animate-fade-in" style="animation-delay: 0.1s;">
        <div class="stat-item">
            <div class="stat-value" id="totalKelompok">{{ $kelompokTanis->count() }}</div>
            <div class="stat-label">Total Kelompok</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" id="totalAnggota">{{ $kelompokTanis->sum('jumlah_anggota') }}</div>
            <div class="stat-label">Total Anggota</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" id="kelompokTerbantu">{{ $kelompokTanis->filter(function($k) { return $k->sebaranBantuans->count() > 0; })->count() }}</div>
            <div class="stat-label">Terima Bantuan</div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section animate-fade-in" style="animation-delay: 0.2s;">
        <h5 class="filter-section-title">
            <i class="bi bi-funnel"></i>
            <span>Filter Kelompok Tani</span>
        </h5>
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <label for="tahunFilter" class="form-label">
                    <i class="bi bi-calendar3 me-1"></i>Tahun Bantuan
                </label>
                <select class="form-select" id="tahunFilter">
                    <option value="">Semua Tahun</option>
                    @php
                        $tahunList = \App\Models\JenisBantuan::select('periode_tahun')->distinct()->orderBy('periode_tahun', 'desc')->pluck('periode_tahun');
                    @endphp
                    @foreach($tahunList as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label for="kategoriFilter" class="form-label">
                    <i class="bi bi-tag me-1"></i>Kategori Bantuan
                </label>
                <select class="form-select" id="kategoriFilter">
                    <option value="">Semua Kategori</option>
                    @php
                        $kategoriList = \App\Models\KategoriBantuan::pluck('nama_kategori', 'id');
                    @endphp
                    @foreach($kategoriList as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label for="kecamatanFilter" class="form-label">
                    <i class="bi bi-building me-1"></i>Kecamatan
                </label>
                <select class="form-select" id="kecamatanFilter">
                    <option value="">Semua Kecamatan</option>
                    @php
                        $kecamatanList = \App\Models\Kecamatan::pluck('nama_kecamatan', 'id');
                    @endphp
                    @foreach($kecamatanList as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label for="desaFilter" class="form-label">
                    <i class="bi bi-map me-1"></i>Desa
                </label>
                <select class="form-select" id="desaFilter">
                    <option value="">Semua Desa</option>
                    @php
                        $desaList = \App\Models\Desa::pluck('nama_desa', 'id');
                    @endphp
                    @foreach($desaList as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="text-end mt-3">
            <button class="btn-reset-filter" id="resetFilter">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Reset Filter</span>
            </button>
        </div>
    </div>

    <!-- Kelompok Tani List -->
    <div class="row g-4" id="kelompokTaniList">
        @foreach($kelompokTanis as $index => $kelompok)
        <div class="col-md-6 col-lg-4 kelompok-item animate-fade-in"
             style="animation-delay: {{ ($index * 0.1) + 0.3 }}s;"
             data-tahun="{{ $kelompok->sebaranBantuans->pluck('jenisBantuans.*.periode_tahun')->flatten()->unique()->join(',') }}"
             data-kategori="{{ $kelompok->sebaranBantuans->pluck('jenisBantuans.*.kategori_bantuan_id')->flatten()->unique()->join(',') }}"
             data-kecamatan="{{ $kelompok->desa->kecamatan_id }}"
             data-desa="{{ $kelompok->desa_id }}">
            <div class="kelompok-card">
                <div class="kelompok-card-header">
                    <h5 class="kelompok-name">{{ $kelompok->nama_kelompok }}</h5>
                </div>
                <div class="kelompok-card-body">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Lokasi</div>
                            <p class="info-value">{{ $kelompok->desa->nama_desa }}, {{ $kelompok->desa->kecamatan->nama_kecamatan }}</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Jumlah Anggota</div>
                            <p class="info-value">{{ $kelompok->jumlah_anggota }} Orang</p>
                        </div>
                    </div>

                    @if($kelompok->sebaranBantuans->count() > 0)
                        <div class="bantuan-section">
                            <div class="bantuan-title">
                                <i class="bi bi-gift-fill"></i>
                                <span>Bantuan Diterima</span>
                            </div>
                            <div class="bantuan-list">
                                @php
                                    $allJenisBantuan = collect();
                                    foreach($kelompok->sebaranBantuans as $sebaran) {
                                        $allJenisBantuan = $allJenisBantuan->merge($sebaran->jenisBantuans);
                                    }
                                    $uniqueJenisBantuan = $allJenisBantuan->unique('id')->take(3);
                                @endphp
                                @foreach($uniqueJenisBantuan as $jenisBantuan)
                                    <div class="bantuan-item">
                                        <span class="bantuan-badge">
                                            <i class="bi bi-tag-fill"></i>
                                            {{ $jenisBantuan->kategoriBantuan->nama_kategori }}
                                        </span>
                                        <p class="bantuan-name">{{ $jenisBantuan->nama_bantuan }}</p>
                                    </div>
                                @endforeach
                                @if($allJenisBantuan->unique('id')->count() > 3)
                                    <div class="text-center mt-2">
                                        <small class="text-muted">+{{ $allJenisBantuan->unique('id')->count() - 3 }} bantuan lainnya</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                <div class="kelompok-card-footer">
                    <a href="{{ route('kelompok-tani.show', $kelompok->id) }}" class="btn-detail">
                        <span>Lihat Detail</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if($kelompokTanis->count() === 0)
    <div class="empty-state animate-fade-in">
        <div class="empty-state-icon">
            <i class="bi bi-people"></i>
        </div>
        <h3 class="empty-state-title">Belum Ada Data Kelompok Tani</h3>
        <p class="empty-state-text">Data kelompok tani belum tersedia saat ini.</p>
    </div>
    @endif

    <!-- No Results State -->
    <div class="empty-state" id="noResultsState" style="display: none;">
        <div class="empty-state-icon">
            <i class="bi bi-search"></i>
        </div>
        <h3 class="empty-state-title">Tidak Ada Hasil</h3>
        <p class="empty-state-text">Tidak ada kelompok tani yang sesuai dengan filter yang dipilih.</p>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="text-center">
        <div class="spinner-border text-success mb-3" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="fw-bold" style="color: var(--primary-green);">Memfilter data...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kelompokItems = document.querySelectorAll('.kelompok-item');
    const noResultsState = document.getElementById('noResultsState');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const totalKelompokEl = document.getElementById('totalKelompok');

    // Fungsi filter
    function filterKelompokTani() {
        // Show loading
        loadingOverlay.classList.add('active');

        setTimeout(() => {
            const tahun = document.getElementById('tahunFilter').value;
            const kategori = document.getElementById('kategoriFilter').value;
            const kecamatan = document.getElementById('kecamatanFilter').value;
            const desa = document.getElementById('desaFilter').value;

            let visibleCount = 0;

            kelompokItems.forEach(item => {
                let showItem = true;

                // Filter berdasarkan tahun
                if (tahun) {
                    const itemTahun = item.dataset.tahun;
                    if (!itemTahun || !itemTahun.includes(tahun)) {
                        showItem = false;
                    }
                }

                // Filter berdasarkan kategori
                if (kategori) {
                    const itemKategori = item.dataset.kategori;
                    if (!itemKategori || !itemKategori.includes(kategori)) {
                        showItem = false;
                    }
                }

                // Filter berdasarkan kecamatan
                if (kecamatan) {
                    const itemKecamatan = item.dataset.kecamatan;
                    if (itemKecamatan !== kecamatan) {
                        showItem = false;
                    }
                }

                // Filter berdasarkan desa
                if (desa) {
                    const itemDesa = item.dataset.desa;
                    if (itemDesa !== desa) {
                        showItem = false;
                    }
                }

                if (showItem) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Update total
            totalKelompokEl.textContent = visibleCount;

            // Show/hide no results state
            if (visibleCount === 0) {
                noResultsState.style.display = 'block';
            } else {
                noResultsState.style.display = 'none';
            }

            // Hide loading
            loadingOverlay.classList.remove('active');
        }, 300);
    }

    // Event listeners
    document.getElementById('tahunFilter').addEventListener('change', filterKelompokTani);
    document.getElementById('kategoriFilter').addEventListener('change', filterKelompokTani);
    document.getElementById('kecamatanFilter').addEventListener('change', filterKelompokTani);
    document.getElementById('desaFilter').addEventListener('change', filterKelompokTani);

    // Reset filter
    document.getElementById('resetFilter').addEventListener('click', function() {
        document.getElementById('tahunFilter').value = '';
        document.getElementById('kategoriFilter').value = '';
        document.getElementById('kecamatanFilter').value = '';
        document.getElementById('desaFilter').value = '';
        filterKelompokTani();
    });

    // Intersection Observer untuk animasi
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-fade-in').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
        observer.observe(el);
    });
});
</script>
@endsection
