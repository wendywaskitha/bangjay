@extends('layouts.app')

@section('title', $katalogBantuan->judul)

@section('content')
<style>
    :root {
        --primary-green: #18872e;
        --primary-green-dark: #146624;
        --primary-green-light: #2ba245;
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

    /* Hero Image */
    .catalog-hero {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        height: 450px;
    }

    .catalog-hero-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .catalog-hero-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
        padding: 2.5rem 2rem;
        color: white;
    }

    .catalog-hero-title {
        font-size: clamp(1.75rem, 4vw, 2.5rem);
        font-weight: 800;
        margin: 0;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        line-height: 1.2;
    }

    /* Info Cards */
    .info-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }

    .info-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        transition: width 0.3s ease;
    }

    .info-card:hover {
        border-color: var(--primary-green-light);
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(24, 135, 46, 0.15);
    }

    .info-card:hover::before {
        width: 100%;
        opacity: 0.05;
    }

    .info-card-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, rgba(24, 135, 46, 0.1), rgba(43, 162, 69, 0.15));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--primary-green);
        margin-bottom: 1rem;
    }

    .info-card-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .info-card-value {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9375rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .status-badge.active {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .status-badge.upcoming {
        background: linear-gradient(135deg, #17a2b8, #20c997);
        color: white;
    }

    .status-badge.completed {
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
    }

    /* Detail Section */
    .detail-section {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .detail-section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .detail-section-title i {
        color: var(--primary-green);
        font-size: 1.75rem;
    }

    /* Jenis Bantuan Card */
    .jenis-bantuan-card {
        background: linear-gradient(135deg, #f1f8f4 0%, #e8f5e9 100%);
        border-left: 5px solid var(--primary-green);
        padding: 1.75rem;
        border-radius: 16px;
        margin-bottom: 2rem;
    }

    .jenis-bantuan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
        margin-top: 1.25rem;
    }

    .jenis-bantuan-item {
        display: flex;
        flex-direction: column;
    }

    .jenis-bantuan-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .jenis-bantuan-value {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--primary-green-dark);
    }

    .kategori-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9375rem;
    }

    /* Content Styling */
    .catalog-content {
        font-size: 1.0625rem;
        line-height: 1.8;
        color: #495057;
    }

    .catalog-content p {
        margin-bottom: 1.5rem;
    }

    .catalog-content h2,
    .catalog-content h3 {
        color: var(--primary-green-dark);
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .catalog-content h2 {
        font-size: 1.75rem;
        border-left: 5px solid var(--primary-green);
        padding-left: 1rem;
    }

    .catalog-content h3 {
        font-size: 1.5rem;
    }

    .catalog-content ul,
    .catalog-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .catalog-content li {
        margin-bottom: 0.75rem;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2.5rem;
        flex-wrap: wrap;
    }

    .btn-back {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        border: none;
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(24, 135, 46, 0.3);
    }

    .btn-back:hover {
        background: linear-gradient(135deg, var(--primary-green-dark), var(--primary-green));
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(24, 135, 46, 0.4);
        color: white;
    }

    .btn-share {
        background: white;
        border: 2px solid var(--primary-green);
        color: var(--primary-green);
        padding: 0.875rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-share:hover {
        background: var(--primary-green);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(24, 135, 46, 0.3);
    }

    /* Social Share */
    .social-share {
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 2px solid #e9ecef;
    }

    .social-share-title {
        font-weight: 700;
        color: #495057;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .social-share-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-social {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-social:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .btn-whatsapp { background: #25D366; }
    .btn-facebook { background: #1877F2; }
    .btn-twitter { background: #1DA1F2; }
    .btn-telegram { background: #0088cc; }

    /* Responsive */
    @media (max-width: 768px) {
        .catalog-hero {
            height: 350px;
        }

        .catalog-hero-overlay {
            padding: 1.75rem 1.5rem;
        }

        .detail-section {
            padding: 2rem 1.5rem;
        }

        .info-card-grid {
            grid-template-columns: 1fr;
        }

        .jenis-bantuan-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-back,
        .btn-share {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .catalog-hero {
            height: 300px;
        }

        .detail-section {
            padding: 1.75rem 1.25rem;
        }

        .jenis-bantuan-card {
            padding: 1.5rem;
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
</style>

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
                <li class="breadcrumb-item">
                    <a href="{{ route('katalog-bantuan.index') }}">
                        <i class="bi bi-book me-1"></i>Katalog Bantuan
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($katalogBantuan->judul, 50) }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Hero Image -->
            @if($katalogBantuan->foto)
            <div class="catalog-hero animate-fade-in" style="animation-delay: 0.1s;">
                <img src="{{ asset('storage/' . $katalogBantuan->foto) }}"
                     class="catalog-hero-img"
                     alt="{{ $katalogBantuan->judul }}"
                     loading="eager">
                <div class="catalog-hero-overlay">
                    <h1 class="catalog-hero-title">{{ $katalogBantuan->judul }}</h1>
                </div>
            </div>
            @else
            <div class="detail-section animate-fade-in" style="animation-delay: 0.1s;">
                <h1 class="catalog-hero-title" style="color: #1a1a1a; text-shadow: none;">
                    {{ $katalogBantuan->judul }}
                </h1>
            </div>
            @endif

            <!-- Status Badge -->
            <div class="text-center mb-4 animate-fade-in" style="animation-delay: 0.2s;">
                @php
                    $now = now();
                    $mulai = $katalogBantuan->tanggal_mulai;
                    $selesai = $katalogBantuan->tanggal_selesai;

                    if ($now->lt($mulai)) {
                        $statusClass = 'upcoming';
                        $statusIcon = 'clock-history';
                        $statusText = 'Akan Datang';
                    } elseif ($now->between($mulai, $selesai)) {
                        $statusClass = 'active';
                        $statusIcon = 'signal';
                        $statusText = 'Sedang Berlangsung';
                    } else {
                        $statusClass = 'completed';
                        $statusIcon = 'check-circle';
                        $statusText = 'Telah Selesai';
                    }
                @endphp
                <span class="status-badge {{ $statusClass }}">
                    <i class="bi bi-{{ $statusIcon }}"></i>
                    <span>{{ $statusText }}</span>
                </span>
            </div>

            <!-- Info Cards -->
            <div class="info-card-grid animate-fade-in" style="animation-delay: 0.3s;">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="info-card-label">Tanggal Mulai</div>
                    <div class="info-card-value">{{ $katalogBantuan->tanggal_mulai->translatedFormat('d F Y') }}</div>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="info-card-label">Tanggal Selesai</div>
                    <div class="info-card-value">{{ $katalogBantuan->tanggal_selesai->translatedFormat('d F Y') }}</div>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="info-card-label">Durasi Program</div>
                    <div class="info-card-value">
                        {{ $katalogBantuan->tanggal_mulai->diffInDays($katalogBantuan->tanggal_selesai) }} Hari
                    </div>
                </div>
            </div>

            <!-- Jenis Bantuan -->
            @if($katalogBantuan->jenisBantuan)
            <div class="jenis-bantuan-card animate-fade-in" style="animation-delay: 0.4s;">
                <h3 style="color: var(--primary-green-dark); font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="bi bi-gift-fill"></i>
                    <span>Informasi Jenis Bantuan</span>
                </h3>

                <div class="jenis-bantuan-grid">
                    <div class="jenis-bantuan-item">
                        <div class="jenis-bantuan-label">
                            <i class="bi bi-box-seam me-1"></i>Nama Bantuan
                        </div>
                        <div class="jenis-bantuan-value">{{ $katalogBantuan->jenisBantuan->nama_bantuan }}</div>
                    </div>

                    <div class="jenis-bantuan-item">
                        <div class="jenis-bantuan-label">
                            <i class="bi bi-tag me-1"></i>Kategori
                        </div>
                        <div class="jenis-bantuan-value">
                            <span class="kategori-badge">
                                <i class="bi bi-folder-fill"></i>
                                {{ $katalogBantuan->jenisBantuan->kategoriBantuan->nama_kategori }}
                            </span>
                        </div>
                    </div>

                    <div class="jenis-bantuan-item">
                        <div class="jenis-bantuan-label">
                            <i class="bi bi-calendar3 me-1"></i>Periode Tahun
                        </div>
                        <div class="jenis-bantuan-value">{{ $katalogBantuan->jenisBantuan->periode_tahun }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Deskripsi -->
            <div class="detail-section animate-fade-in" style="animation-delay: 0.5s;">
                <h2 class="detail-section-title">
                    <i class="bi bi-file-text"></i>
                    <span>Deskripsi Program</span>
                </h2>

                <div class="catalog-content">
                    {!! $katalogBantuan->deskripsi !!}
                </div>

                <!-- Social Share -->
                <div class="social-share">
                    <div class="social-share-title">
                        <i class="bi bi-share"></i>
                        <span>Bagikan Katalog Ini</span>
                    </div>
                    <div class="social-share-buttons">
                        <a href="https://wa.me/?text={{ urlencode($katalogBantuan->judul . ' - ' . url()->current()) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn-social btn-whatsapp"
                           aria-label="Bagikan via WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn-social btn-facebook"
                           aria-label="Bagikan via Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($katalogBantuan->judul) }}&url={{ urlencode(url()->current()) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn-social btn-twitter"
                           aria-label="Bagikan via Twitter">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($katalogBantuan->judul) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn-social btn-telegram"
                           aria-label="Bagikan via Telegram">
                            <i class="bi bi-telegram"></i>
                        </a>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('katalog-bantuan.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali ke Katalog</span>
                    </a>
                    <button onclick="window.print()" class="btn-share">
                        <i class="bi bi-printer"></i>
                        <span>Cetak Katalog</span>
                    </button>
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

    // Smooth scroll untuk anchor links
    document.querySelectorAll('.catalog-content a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<style media="print">
    .breadcrumb-custom,
    .social-share,
    .action-buttons,
    nav,
    footer {
        display: none !important;
    }

    .detail-section {
        box-shadow: none !important;
        page-break-inside: avoid;
    }
</style>
@endsection
