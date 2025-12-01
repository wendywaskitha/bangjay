@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<style>
    :root {
        --primary-green: #18872e;
        --primary-green-dark: #146624;
        --primary-green-light: #2ba245;
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
        --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
        --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.15);
        --transition-base: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Hero Carousel Improvements */
    .hero-carousel {
        position: relative;
        max-height: 70vh;
        overflow: hidden;
        margin-bottom: 0;
    }

    .hero-carousel .carousel-item {
        height: 70vh;
        position: relative;
    }

    .hero-carousel .carousel-item img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        filter: brightness(0.7);
    }

    .hero-gradient {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 50%, #34c759 100%);
    }

    .carousel-caption {
        bottom: 15% !important;
        left: 50% !important;
        right: auto !important;
        transform: translateX(-50%);
        max-width: 900px;
        text-shadow: 2px 4px 12px rgba(0, 0, 0, 0.7);
        z-index: 2;
    }

    .carousel-caption h2 {
        font-size: clamp(2rem, 5vw, 3.5rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .carousel-caption p {
        font-size: clamp(1rem, 2vw, 1.25rem);
        max-width: 700px;
        margin: 0 auto 1.5rem;
        line-height: 1.6;
    }

    .carousel-caption .btn {
        border-radius: 50px;
        padding: 0.875rem 2.5rem;
        font-weight: 600;
        font-size: 1.0625rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        transition: var(--transition-base);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .carousel-caption .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        border-color: rgba(255, 255, 255, 0.5);
    }

    /* Carousel Controls */
    .carousel-control-prev,
    .carousel-control-next {
        width: 5%;
        opacity: 0.8;
        transition: var(--transition-base);
    }

    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        opacity: 1;
    }

    .carousel-indicators {
        bottom: 2rem;
    }

    .carousel-indicators [data-bs-target] {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin: 0 6px;
        background-color: rgba(255, 255, 255, 0.5);
        border: 2px solid transparent;
        transition: var(--transition-base);
    }

    .carousel-indicators [data-bs-target].active {
        background-color: white;
        transform: scale(1.2);
    }

    /* Stats Section - Full Width */
    .stats-section {
        background: linear-gradient(135deg, #f1f8f4 0%, #e8f5e9 100%);
        padding: 4rem 0;
        margin-top: -2rem;
        position: relative;
        z-index: 10;
    }

    /* Sidebar Sticky */
    .sidebar-sticky {
        position: sticky;
        top: 100px;
    }

    /* Section Improvements */
    .section-wrapper {
        padding: 4rem 0;
    }

    .section-title {
        position: relative;
        padding-bottom: 1.25rem;
        margin-bottom: 1rem;
        font-weight: 800;
        font-size: clamp(1.5rem, 3vw, 2rem);
        color: #1a1a1a;
        letter-spacing: -0.03em;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-green), var(--primary-green-light));
        border-radius: 3px;
    }

    .section-subtitle {
        color: #6c757d;
        font-size: 1rem;
        margin-bottom: 2rem;
        font-weight: 400;
    }

    /* Card Improvements */
    .card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: var(--transition-base);
        box-shadow: var(--shadow-sm);
        height: 100%;
        background: white;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-lg);
    }

    .card-img-wrapper {
        overflow: hidden;
        position: relative;
        height: 240px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }

    .card-img-top {
        height: 100%;
        width: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .card:hover .card-img-top {
        transform: scale(1.1);
    }

    /* Announcement Card */
    .announcement-card {
        border-left: 5px solid var(--primary-green);
        background: white;
        position: relative;
        overflow: hidden;
        transition: var(--transition-base);
        margin-bottom: 1.5rem;
    }

    .announcement-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(24, 135, 46, 0.05), transparent);
        transition: var(--transition-base);
    }

    .announcement-card:hover {
        border-left-width: 8px;
        transform: translateX(5px);
    }

    .announcement-card:hover::before {
        left: 100%;
    }

    .announcement-card .card-title {
        color: #1a1a1a;
        font-size: 1.125rem;
        font-weight: 700;
        line-height: 1.4;
        margin-bottom: 0.75rem;
    }

    /* Profile Card */
    .profile-highlight {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        transition: var(--transition-base);
        border: 1px solid rgba(24, 135, 46, 0.1);
        margin-bottom: 2rem;
    }

    .profile-highlight:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-5px);
    }

    /* Icon Improvements */
    .benefit-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, rgba(24, 135, 46, 0.12), rgba(43, 162, 69, 0.18));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.75rem;
        color: var(--primary-green);
        transition: var(--transition-base);
    }

    .card:hover .benefit-icon,
    .profile-highlight:hover .benefit-icon {
        transform: scale(1.1) rotate(5deg);
        background: linear-gradient(135deg, rgba(24, 135, 46, 0.18), rgba(43, 162, 69, 0.25));
    }

    /* Stat Card */
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem 2rem;
        text-align: center;
        box-shadow: var(--shadow-md);
        transition: var(--transition-base);
        border-top: 5px solid var(--primary-green);
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, transparent, rgba(24, 135, 46, 0.04));
        opacity: 0;
        transition: var(--transition-base);
    }

    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 16px 40px rgba(24, 135, 46, 0.25);
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-number {
        font-size: clamp(2.25rem, 5vw, 3rem);
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
        margin-bottom: 1rem;
    }

    .stat-label {
        font-size: 1.25rem;
        font-weight: 700;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .stat-desc {
        font-size: 0.875rem;
        color: var(--primary-green);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Button Improvements */
    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        border: none;
        border-radius: 50px;
        padding: 0.875rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        transition: var(--transition-base);
        box-shadow: 0 4px 16px rgba(24, 135, 46, 0.3);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .btn-primary-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: var(--transition-base);
    }

    .btn-primary-custom:hover {
        background: linear-gradient(135deg, var(--primary-green-dark), var(--primary-green));
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(24, 135, 46, 0.4);
    }

    .btn-primary-custom:hover::before {
        left: 100%;
    }

    /* Badge Improvements */
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8125rem;
        letter-spacing: 0.03em;
    }

    .bg-success {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
    }

    .bg-primary {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light)) !important;
    }

    /* Empty State */
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }

    .empty-state .benefit-icon {
        width: 100px;
        height: 100px;
        font-size: 2.5rem;
        opacity: 0.5;
        margin-bottom: 1.5rem;
    }

    .empty-state p {
        color: #6c757d;
        font-size: 1.125rem;
        margin-bottom: 0;
    }

    /* Animations */
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

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .hero-carousel,
        .hero-carousel .carousel-item {
            height: 60vh;
            max-height: 60vh;
        }

        .sidebar-sticky {
            position: static;
            top: auto;
        }

        .section-wrapper {
            padding: 3rem 0;
        }

        .stats-section {
            padding: 3rem 0;
        }

        .profile-highlight {
            padding: 1.75rem;
        }
    }

    @media (max-width: 768px) {
        .hero-carousel,
        .hero-carousel .carousel-item {
            height: 55vh;
            max-height: 55vh;
        }

        .carousel-caption {
            bottom: 10% !important;
            padding: 0 1rem;
        }

        .section-wrapper {
            padding: 2.5rem 0;
        }

        .stats-section {
            padding: 2.5rem 0;
        }

        .profile-highlight {
            padding: 1.5rem;
        }

        .stat-card {
            margin-bottom: 1.5rem;
            padding: 2rem 1.5rem;
        }

        .card-img-wrapper {
            height: 200px;
        }
    }

    @media (max-width: 576px) {
        .hero-carousel,
        .hero-carousel .carousel-item {
            height: 50vh;
            max-height: 50vh;
        }

        .section-wrapper {
            padding: 2rem 0;
        }

        .stats-section {
            padding: 2rem 0;
        }

        .profile-highlight {
            padding: 1.25rem;
        }

        .card-img-wrapper {
            height: 180px;
        }
    }
</style>

<div class="container-fluid px-0">
    <!-- Hero Carousel -->
    @if($heroBanners->count() > 0)
        <div id="heroCarousel" class="carousel slide carousel-fade hero-carousel" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-indicators">
                @foreach($heroBanners as $index => $banner)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                            class="{{ $index == 0 ? 'active' : '' }}" aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach($heroBanners as $index => $banner)
                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                        @if($banner->gambar)
                            <img src="{{ asset('storage/' . $banner->gambar) }}" class="d-block w-100" alt="{{ $banner->judul }}" loading="{{ $index == 0 ? 'eager' : 'lazy' }}">
                        @else
                            <div class="d-block w-100 hero-gradient" style="height: 70vh;"></div>
                        @endif
                        <div class="carousel-caption d-md-block">
                            <h2 class="display-4 fw-bold mb-3">{{ $banner->judul }}</h2>
                            @if($banner->subjudul)
                                <p class="lead mb-4">{{ $banner->subjudul }}</p>
                            @elseif($banner->deskripsi_singkat)
                                <p class="lead mb-4">{{ $banner->deskripsi_singkat }}</p>
                            @endif
                            @if($banner->cta_link && $banner->cta_text)
                                <a href="{{ $banner->cta_link }}" class="btn btn-light btn-lg">
                                    {{ $banner->cta_text }} <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if($heroBanners->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            @endif
        </div>
    @else
        <!-- Fallback Hero -->
        <div class="hero-carousel">
            <div class="carousel-item active" style="height: 70vh;">
                <div class="d-block w-100 hero-gradient" style="height: 100%;"></div>
                <div class="carousel-caption d-md-block">
                    <h2 class="display-4 fw-bold mb-3">Selamat Datang di Rumah Aspirasi Bang Jai</h2>
                    <p class="lead mb-4">Portal Informasi Pertanian dan Pengembangan Kelompok Tani</p>
                    <a href="{{ route('profil.index') }}" class="btn btn-light btn-lg">
                        Pelajari Lebih Lanjut <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistik Program - Full Width di bawah Hero -->
    <section class="stats-section">
        <div class="container">
            <div class="text-center mb-5 animate-fade-in">
                <h2 class="section-title" style="text-align: center;">
                    Statistik Program
                    <span style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 70px; height: 5px; background: linear-gradient(90deg, var(--primary-green), var(--primary-green-light)); border-radius: 3px;"></span>
                </h2>
                <p class="section-subtitle" style="text-align: center;">Capaian dan dampak program Bang Jai untuk pertanian</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="stat-card">
                        <div class="benefit-icon mx-auto" style="width: 70px; height: 70px; margin-bottom: 1.25rem;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-number">{{ number_format(\App\Models\KelompokTani::count()) }}</div>
                        <h5 class="stat-label">Kelompok Tani</h5>
                        <p class="stat-desc mb-0">Terdaftar</p>
                    </div>
                </div>
                <div class="col-md-4 animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="stat-card">
                        <div class="benefit-icon mx-auto" style="width: 70px; height: 70px; margin-bottom: 1.25rem;">
                            <i class="bi bi-gift-fill"></i>
                        </div>
                        <div class="stat-number">{{ number_format(\App\Models\SebaranBantuan::count()) }}</div>
                        <h5 class="stat-label">Bantuan</h5>
                        <p class="stat-desc mb-0">Tersalurkan</p>
                    </div>
                </div>
                <div class="col-md-4 animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="stat-card">
                        <div class="benefit-icon mx-auto" style="width: 70px; height: 70px; margin-bottom: 1.25rem;">
                            <i class="bi bi-newspaper"></i>
                        </div>
                        <div class="stat-number">{{ number_format(\App\Models\Artikel::where('status', 'published')->count()) }}</div>
                        <h5 class="stat-label">Artikel</h5>
                        <p class="stat-desc mb-0">Terpublikasi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content with Sidebar Kanan -->
    <div class="container">
        <div class="row section-wrapper">
            <!-- Konten Utama Kiri -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <!-- Artikel Berita -->
                <section class="mb-5 animate-fade-in" style="animation-delay: 0.4s;">
                    <h3 class="section-title">Artikel Berita</h3>
                    <p class="section-subtitle">Berita terkini seputar pertanian</p>

                    @if($articles->count() > 0)
                        <div class="row g-4">
                            @foreach($articles as $article)
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-img-wrapper">
                                        @if($article->thumbnail)
                                            <img src="{{ asset('storage/' . $article->thumbnail) }}" class="card-img-top" alt="{{ $article->judul }}" loading="lazy">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                                <i class="bi bi-newspaper" style="font-size: 3rem; color: #dee2e6;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body p-3 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                                            @if($article->kategoriArtikel)
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-tag-fill me-1"></i>
                                                    {{ $article->kategoriArtikel->nama_kategori }}
                                                </span>
                                            @endif
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $article->published_at->format('d M Y') }}
                                            </small>
                                        </div>
                                        <h6 class="card-title fw-bold mb-2">{{ Str::limit($article->judul, 60) }}</h6>
                                        <p class="card-text text-muted mb-3 flex-grow-1" style="font-size: 0.875rem; line-height: 1.6;">
                                            {!! Str::limit(strip_tags($article->isi), 100) !!}
                                        </p>
                                        <a href="{{ route('artikel.show', $article->slug) }}" class="btn btn-primary-custom btn-sm w-100 mt-auto">
                                            Baca <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="benefit-icon">
                                <i class="bi bi-newspaper"></i>
                            </div>
                            <p>Belum ada artikel berita.</p>
                        </div>
                    @endif
                </section>

                <!-- Katalog Bantuan -->
                <section class="animate-fade-in" style="animation-delay: 0.5s;">
                    <h3 class="section-title">Katalog Bantuan</h3>
                    <p class="section-subtitle">Program bantuan yang tersedia</p>

                    @if($activeKatalog->count() > 0)
                        <div class="row g-4">
                            @foreach($activeKatalog as $katalog)
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-img-wrapper">
                                        @if($katalog->foto)
                                            <img src="{{ asset('storage/' . $katalog->foto) }}" class="card-img-top" alt="{{ $katalog->judul }}" loading="lazy">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                                <i class="bi bi-image" style="font-size: 3rem; color: #dee2e6;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body p-3 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                                            @if($katalog->jenisBantuan)
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-tag-fill me-1"></i>
                                                    {{ $katalog->jenisBantuan->kategoriBantuan->nama_kategori ?? 'Umum' }}
                                                </span>
                                            @endif
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-range me-1"></i>
                                                {{ $katalog->tanggal_mulai->format('d M') }} - {{ $katalog->tanggal_selesai->format('d M') }}
                                            </small>
                                        </div>
                                        <h6 class="card-title fw-bold mb-2">{{ Str::limit($katalog->judul, 60) }}</h6>
                                        <p class="card-text text-muted mb-3 flex-grow-1" style="font-size: 0.875rem; line-height: 1.6;">
                                            {!! Str::limit(strip_tags($katalog->deskripsi), 100) !!}
                                        </p>
                                        <a href="{{ route('katalog-bantuan.show', $katalog->slug) }}" class="btn btn-primary-custom btn-sm w-100 mt-auto">
                                            Detail <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="benefit-icon">
                                <i class="bi bi-folder2-open"></i>
                            </div>
                            <p>Belum ada katalog bantuan aktif.</p>
                        </div>
                    @endif
                </section>
            </div>

            <!-- Sidebar Kanan -->
            <div class="col-lg-4">
                <div class="sidebar-sticky">
                    <!-- Profil Bang Jai -->
                    <div class="animate-fade-in" style="animation-delay: 0.4s;">
                        <h3 class="section-title">Profil Bang Jai</h3>
                        <p class="section-subtitle">Mengenal Bang Jai</p>

                        @if($profil = \App\Models\ProfilBangJai::where('is_active', true)->first())
                            <div class="profile-highlight">
                                @if($profil->foto_profil)
                                    <div class="text-center mb-3">
                                        <img src="{{ asset('storage/' . $profil->foto_profil) }}"
                                             alt="{{ $profil->judul }}"
                                             class="rounded-circle img-fluid"
                                             style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--primary-green-light);">
                                    </div>
                                @else
                                    <div class="benefit-icon">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                @endif
                                <h5 class="fw-bold mb-3 text-center">{{ $profil->judul }}</h5>
                                <p class="text-muted mb-3" style="font-size: 0.9375rem; line-height: 1.7;">
                                    {!! Str::limit(strip_tags($profil->konten_profil), 200) !!}
                                </p>
                                <div class="text-center">
                                    <a href="{{ route('profil.index') }}" class="btn btn-primary-custom w-100">
                                        Selengkapnya <i class="bi bi-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="benefit-icon">
                                    <i class="bi bi-info-circle"></i>
                                </div>
                                <p>Profil akan segera tersedia.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Pengumuman Terbaru -->
                    <div class="animate-fade-in" style="animation-delay: 0.5s;">
                        <h3 class="section-title mt-4">Pengumuman</h3>
                        <p class="section-subtitle">Info Terbaru</p>

                        @if($announcements->count() > 0)
                            @foreach($announcements->take(3) as $announcement)
                            <div class="announcement-card">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start gap-2 mb-2">
                                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, rgba(24, 135, 46, 0.12), rgba(43, 162, 69, 0.18)); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="bi bi-megaphone-fill" style="color: var(--primary-green); font-size: 1.125rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-2">{{ $announcement->judul }}</h6>
                                            <p class="card-text text-muted mb-2" style="font-size: 0.875rem; line-height: 1.6;">
                                                {!! Str::limit(strip_tags($announcement->isi), 80) !!}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $announcement->mulai_tayang->format('d M Y') }}
                                                </small>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <div class="benefit-icon">
                                    <i class="bi bi-megaphone"></i>
                                </div>
                                <p>Belum ada pengumuman.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Pause carousel on hover
    const carousel = document.getElementById('heroCarousel');
    if (carousel) {
        carousel.addEventListener('mouseenter', function() {
            bootstrap.Carousel.getInstance(carousel)?.pause();
        });
        carousel.addEventListener('mouseleave', function() {
            bootstrap.Carousel.getInstance(carousel)?.cycle();
        });
    }
});
</script>
@endsection
