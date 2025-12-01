@extends('layouts.app')

@section('title', 'Katalog Bantuan')

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

    .breadcrumb-item.active {
        color: #6c757d;
    }

    /* Custom Tabs */
    .nav-tabs-custom {
        border: none;
        gap: 0.75rem;
        margin-bottom: 2.5rem;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 0.5rem;
    }

    .nav-tabs-custom::-webkit-scrollbar {
        height: 4px;
    }

    .nav-tabs-custom::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .nav-tabs-custom::-webkit-scrollbar-thumb {
        background: var(--primary-green-light);
        border-radius: 10px;
    }

    .nav-tabs-custom .nav-item {
        flex: 0 0 auto;
    }

    .nav-tabs-custom .nav-link {
        border: 2px solid #e9ecef;
        border-radius: 50px;
        padding: 0.875rem 2rem;
        font-weight: 600;
        color: #495057;
        background: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        white-space: nowrap;
    }

    .nav-tabs-custom .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(24, 135, 46, 0.1), transparent);
        transition: all 0.5s ease;
    }

    .nav-tabs-custom .nav-link:hover {
        border-color: var(--primary-green-light);
        color: var(--primary-green);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(24, 135, 46, 0.15);
    }

    .nav-tabs-custom .nav-link:hover::before {
        left: 100%;
    }

    .nav-tabs-custom .nav-link.active {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        border-color: var(--primary-green);
        color: white;
        box-shadow: 0 4px 16px rgba(24, 135, 46, 0.3);
    }

    .nav-tabs-custom .nav-link i {
        margin-right: 0.5rem;
    }

    /* Tab Badge */
    .tab-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.3);
        padding: 0.25rem 0.625rem;
        border-radius: 50px;
        font-size: 0.8125rem;
        margin-left: 0.5rem;
        font-weight: 700;
    }

    .nav-link.active .tab-badge {
        background: rgba(255, 255, 255, 0.25);
    }

    /* Catalog Card */
    .catalog-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        height: 100%;
        background: white;
    }

    .catalog-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
    }

    .catalog-img-wrapper {
        position: relative;
        overflow: hidden;
        height: 240px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }

    .catalog-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .catalog-card:hover .catalog-img {
        transform: scale(1.1);
    }

    .catalog-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8125rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .catalog-status {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        background: rgba(255, 255, 255, 0.95);
        color: var(--primary-green);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .catalog-card-body {
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
    }

    .catalog-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .catalog-description {
        color: #6c757d;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .catalog-footer {
        padding: 1.25rem 1.75rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .catalog-date {
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .catalog-date i {
        color: var(--primary-green);
    }

    .btn-catalog {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        border: none;
        color: white;
        padding: 0.625rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(24, 135, 46, 0.3);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-catalog:hover {
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
        margin-bottom: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .nav-tabs-custom {
            margin-bottom: 2rem;
        }

        .nav-tabs-custom .nav-link {
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
        }

        .catalog-img-wrapper {
            height: 200px;
        }

        .catalog-card-body {
            padding: 1.5rem;
        }

        .catalog-footer {
            padding: 1rem 1.5rem;
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-catalog {
            width: 100%;
            justify-content: center;
        }

        .empty-state {
            padding: 3rem 1.5rem;
        }

        .empty-state-icon {
            width: 100px;
            height: 100px;
            font-size: 2.5rem;
        }
    }

    @media (max-width: 576px) {
        .catalog-img-wrapper {
            height: 180px;
        }

        .catalog-title {
            font-size: 1.125rem;
        }

        .tab-badge {
            display: block;
            margin-left: 0;
            margin-top: 0.25rem;
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

    /* Tab Content Animation */
    .tab-pane {
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
</style>

<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="text-center">
                <i class="bi bi-book me-2"></i>Katalog Bantuan
            </h1>
            <p class="text-center">Temukan berbagai program bantuan pertanian yang tersedia</p>
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
                <li class="breadcrumb-item active" aria-current="page">Katalog Bantuan</li>
            </ol>
        </nav>
    </div>

    <!-- Custom Tabs -->
    <ul class="nav nav-tabs nav-tabs-custom animate-fade-in" id="katalogTab" role="tablist" style="animation-delay: 0.1s;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active"
                    id="aktif-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#aktif"
                    type="button"
                    role="tab"
                    aria-controls="aktif"
                    aria-selected="true">
                <i class="bi bi-check-circle"></i>Sedang Aktif
                <span class="tab-badge">{{ $activeKatalog->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link"
                    id="akan-datang-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#akan-datang"
                    type="button"
                    role="tab"
                    aria-controls="akan-datang"
                    aria-selected="false">
                <i class="bi bi-clock-history"></i>Akan Datang
                <span class="tab-badge">{{ $upcomingKatalog->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link"
                    id="selesai-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#selesai"
                    type="button"
                    role="tab"
                    aria-controls="selesai"
                    aria-selected="false">
                <i class="bi bi-archive"></i>Selesai
                <span class="tab-badge">{{ $completedKatalog->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="katalogTabContent">
        <!-- Katalog Aktif -->
        <div class="tab-pane fade show active" id="aktif" role="tabpanel" aria-labelledby="aktif-tab">
            @if($activeKatalog->count() > 0)
                <div class="row g-4">
                    @foreach($activeKatalog as $index => $katalog)
                    <div class="col-md-6 col-lg-4 animate-fade-in" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="catalog-card">
                            <div class="catalog-img-wrapper">
                                @if($katalog->foto)
                                    <img src="{{ asset('storage/' . $katalog->foto) }}"
                                         class="catalog-img"
                                         alt="{{ $katalog->judul }}"
                                         loading="lazy">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="bi bi-image" style="font-size: 3rem; color: #dee2e6;"></i>
                                    </div>
                                @endif
                                @if($katalog->jenisBantuan)
                                    <div class="catalog-badge">
                                        <i class="bi bi-tag-fill me-1"></i>
                                        {{ $katalog->jenisBantuan->kategoriBantuan->nama_kategori ?? 'Umum' }}
                                    </div>
                                @endif
                                <div class="catalog-status">
                                    <i class="bi bi-signal"></i>
                                    <span>Aktif Sekarang</span>
                                </div>
                            </div>
                            <div class="catalog-card-body">
                                <h5 class="catalog-title">{{ $katalog->judul }}</h5>
                                <p class="catalog-description">
                                    {!! Str::limit(strip_tags($katalog->deskripsi), 120) !!}
                                </p>
                            </div>
                            <div class="catalog-footer">
                                <div class="catalog-date">
                                    <i class="bi bi-calendar-range"></i>
                                    <span>{{ $katalog->tanggal_mulai->format('d M') }} - {{ $katalog->tanggal_selesai->format('d M Y') }}</span>
                                </div>
                                <a href="{{ route('katalog-bantuan.show', $katalog->slug) }}" class="btn-catalog">
                                    <span>Selengkapnya</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h3 class="empty-state-title">Belum Ada Katalog Aktif</h3>
                    <p class="empty-state-text">Tidak ada katalog bantuan yang sedang aktif saat ini.</p>
                </div>
            @endif
        </div>

        <!-- Katalog Akan Datang -->
        <div class="tab-pane fade" id="akan-datang" role="tabpanel" aria-labelledby="akan-datang-tab">
            @if($upcomingKatalog->count() > 0)
                <div class="row g-4">
                    @foreach($upcomingKatalog as $index => $katalog)
                    <div class="col-md-6 col-lg-4 animate-fade-in" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="catalog-card">
                            <div class="catalog-img-wrapper">
                                @if($katalog->foto)
                                    <img src="{{ asset('storage/' . $katalog->foto) }}"
                                         class="catalog-img"
                                         alt="{{ $katalog->judul }}"
                                         loading="lazy">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="bi bi-image" style="font-size: 3rem; color: #dee2e6;"></i>
                                    </div>
                                @endif
                                @if($katalog->jenisBantuan)
                                    <div class="catalog-badge" style="background: linear-gradient(135deg, #17a2b8, #20c997);">
                                        <i class="bi bi-tag-fill me-1"></i>
                                        {{ $katalog->jenisBantuan->kategoriBantuan->nama_kategori ?? 'Umum' }}
                                    </div>
                                @endif
                                <div class="catalog-status" style="color: #17a2b8;">
                                    <i class="bi bi-clock-history"></i>
                                    <span>Segera Hadir</span>
                                </div>
                            </div>
                            <div class="catalog-card-body">
                                <h5 class="catalog-title">{{ $katalog->judul }}</h5>
                                <p class="catalog-description">
                                    {!! Str::limit(strip_tags($katalog->deskripsi), 120) !!}
                                </p>
                            </div>
                            <div class="catalog-footer">
                                <div class="catalog-date">
                                    <i class="bi bi-calendar-event"></i>
                                    <span>Mulai: {{ $katalog->tanggal_mulai->format('d M Y') }}</span>
                                </div>
                                <a href="{{ route('katalog-bantuan.show', $katalog->slug) }}" class="btn-catalog" style="background: linear-gradient(135deg, #17a2b8, #20c997);">
                                    <span>Selengkapnya</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <h3 class="empty-state-title">Tidak Ada Katalog Mendatang</h3>
                    <p class="empty-state-text">Belum ada katalog bantuan yang dijadwalkan akan datang.</p>
                </div>
            @endif
        </div>

        <!-- Katalog Selesai -->
        <div class="tab-pane fade" id="selesai" role="tabpanel" aria-labelledby="selesai-tab">
            @if($completedKatalog->count() > 0)
                <div class="row g-4">
                    @foreach($completedKatalog as $index => $katalog)
                    <div class="col-md-6 col-lg-4 animate-fade-in" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="catalog-card" style="opacity: 0.85;">
                            <div class="catalog-img-wrapper">
                                @if($katalog->foto)
                                    <img src="{{ asset('storage/' . $katalog->foto) }}"
                                         class="catalog-img"
                                         alt="{{ $katalog->judul }}"
                                         loading="lazy"
                                         style="filter: grayscale(20%);">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="bi bi-image" style="font-size: 3rem; color: #dee2e6;"></i>
                                    </div>
                                @endif
                                @if($katalog->jenisBantuan)
                                    <div class="catalog-badge" style="background: linear-gradient(135deg, #6c757d, #495057);">
                                        <i class="bi bi-tag-fill me-1"></i>
                                        {{ $katalog->jenisBantuan->kategoriBantuan->nama_kategori ?? 'Umum' }}
                                    </div>
                                @endif
                                <div class="catalog-status" style="color: #6c757d;">
                                    <i class="bi bi-check-circle"></i>
                                    <span>Selesai</span>
                                </div>
                            </div>
                            <div class="catalog-card-body">
                                <h5 class="catalog-title">{{ $katalog->judul }}</h5>
                                <p class="catalog-description">
                                    {!! Str::limit(strip_tags($katalog->deskripsi), 120) !!}
                                </p>
                            </div>
                            <div class="catalog-footer">
                                <div class="catalog-date">
                                    <i class="bi bi-calendar-check"></i>
                                    <span>{{ $katalog->tanggal_mulai->format('d M') }} - {{ $katalog->tanggal_selesai->format('d M Y') }}</span>
                                </div>
                                <a href="{{ route('katalog-bantuan.show', $katalog->slug) }}" class="btn-catalog" style="background: linear-gradient(135deg, #6c757d, #495057);">
                                    <span>Selengkapnya</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-archive"></i>
                    </div>
                    <h3 class="empty-state-title">Belum Ada Katalog Selesai</h3>
                    <p class="empty-state-text">Belum ada katalog bantuan yang telah selesai.</p>
                </div>
            @endif
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

    // Tab change handler
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(e) {
            // Reset animations when tab changes
            const targetPane = document.querySelector(e.target.getAttribute('data-bs-target'));
            const cards = targetPane.querySelectorAll('.animate-fade-in');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    });

    // Smooth scroll ke top saat ganti tab
    document.querySelectorAll('.nav-link').forEach(tab => {
        tab.addEventListener('click', function() {
            setTimeout(() => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }, 100);
        });
    });
});
</script>
@endsection
