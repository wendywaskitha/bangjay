@extends('layouts.app')

@section('title', $profil->judul)

@section('content')
<style>
    :root {
        --primary-green: #18872e;
        --primary-green-dark: #146624;
        --primary-green-light: #2ba245;
    }

    /* Breadcrumb Styling */
    .breadcrumb-custom {
        background: transparent;
        padding: 0;
        margin-bottom: 2rem;
    }

    .breadcrumb-custom .breadcrumb {
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 0;
    }

    .breadcrumb-item a {
        color: var(--primary-green);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .breadcrumb-item a:hover {
        color: var(--primary-green-dark);
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        color: #6c757d;
        font-weight: 500;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        color: #6c757d;
    }

    /* Profile Header */
    .profile-header {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .profile-banner {
        width: 100%;
        height: 400px;
        object-fit: cover;
        display: block;
    }

    .profile-banner-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
        padding: 2rem;
        color: white;
    }

    .profile-banner-overlay h1 {
        font-size: clamp(1.75rem, 4vw, 2.5rem);
        font-weight: 800;
        margin: 0;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
    }

    /* Profile Avatar */
    .profile-avatar-wrapper {
        position: relative;
        margin-top: -5rem;
        margin-bottom: 2rem;
        text-align: center;
        z-index: 2;
    }

    .profile-avatar {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        object-fit: cover;
        border: 6px solid white;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        background: white;
    }

    /* Profile Card */
    .profile-card {
        background: white;
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .profile-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        transform: translateY(-5px);
    }

    .profile-card-body {
        padding: 2.5rem;
    }

    .profile-title {
        font-size: clamp(1.75rem, 4vw, 2.25rem);
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        text-align: center;
        letter-spacing: -0.02em;
    }

    .profile-subtitle {
        font-size: 1.125rem;
        color: var(--primary-green);
        text-align: center;
        font-weight: 600;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #e9ecef;
    }

    /* Content Styling */
    .profile-content {
        font-size: 1.0625rem;
        line-height: 1.8;
        color: #495057;
        text-align: justify;
    }

    .profile-content p {
        margin-bottom: 1.5rem;
    }

    .profile-content h2,
    .profile-content h3 {
        color: var(--primary-green-dark);
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .profile-content h2 {
        font-size: 1.75rem;
        border-left: 5px solid var(--primary-green);
        padding-left: 1rem;
    }

    .profile-content h3 {
        font-size: 1.5rem;
    }

    .profile-content ul,
    .profile-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .profile-content li {
        margin-bottom: 0.75rem;
    }

    .profile-content blockquote {
        border-left: 4px solid var(--primary-green);
        padding: 1.25rem 1.5rem;
        margin: 2rem 0;
        background: #f8f9fa;
        border-radius: 8px;
        font-style: italic;
        color: #495057;
    }

    .profile-content a {
        color: var(--primary-green);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .profile-content a:hover {
        color: var(--primary-green-dark);
        text-decoration: underline;
    }

    .profile-content img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        margin: 1.5rem 0;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    /* Info Box */
    .info-box {
        background: linear-gradient(135deg, #f1f8f4 0%, #e8f5e9 100%);
        border-left: 5px solid var(--primary-green);
        padding: 1.5rem;
        border-radius: 12px;
        margin: 2rem 0;
    }

    .info-box-icon {
        font-size: 2rem;
        color: var(--primary-green);
        margin-bottom: 1rem;
    }

    .info-box-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-green-dark);
        margin-bottom: 0.75rem;
    }

    .info-box-text {
        margin-bottom: 0;
        color: #495057;
    }

    /* Back Button */
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
        margin-top: 2rem;
    }

    .btn-back:hover {
        background: linear-gradient(135deg, var(--primary-green-dark), var(--primary-green));
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(24, 135, 46, 0.4);
        color: white;
    }

    /* Social Share Buttons */
    .social-share {
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 2px solid #e9ecef;
        text-align: center;
    }

    .social-share-title {
        font-weight: 700;
        color: #495057;
        margin-bottom: 1rem;
    }

    .social-share-buttons {
        display: flex;
        justify-content: center;
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

    .btn-whatsapp {
        background: #25D366;
    }

    .btn-facebook {
        background: #1877F2;
    }

    .btn-twitter {
        background: #1DA1F2;
    }

    .btn-telegram {
        background: #0088cc;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-banner {
            height: 300px;
        }

        .profile-avatar-wrapper {
            margin-top: -4rem;
        }

        .profile-avatar {
            width: 140px;
            height: 140px;
            border-width: 4px;
        }

        .profile-card-body {
            padding: 2rem 1.5rem;
        }

        .profile-content {
            font-size: 1rem;
            text-align: left;
        }

        .breadcrumb-custom .breadcrumb {
            padding: 0.75rem 1rem;
        }

        .info-box {
            padding: 1.25rem;
        }
    }

    @media (max-width: 576px) {
        .profile-banner {
            height: 250px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
        }

        .profile-card-body {
            padding: 1.75rem 1.25rem;
        }

        .btn-back {
            width: 100%;
            justify-content: center;
        }

        .social-share-buttons {
            gap: 0.75rem;
        }

        .btn-social {
            width: 45px;
            height: 45px;
            font-size: 1.125rem;
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
                <li class="breadcrumb-item active" aria-current="page">Profil Bang Jai</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Profile Header with Banner -->
            @if($profil->foto_banner || $profil->foto_profil)
                <div class="profile-header animate-fade-in" style="animation-delay: 0.1s;">
                    @if($profil->foto_banner)
                        <img src="{{ asset('storage/' . $profil->foto_banner) }}"
                             class="profile-banner"
                             alt="{{ $profil->judul }}"
                             loading="eager">
                    @elseif($profil->foto_profil)
                        <img src="{{ asset('storage/' . $profil->foto_profil) }}"
                             class="profile-banner"
                             alt="{{ $profil->judul }}"
                             loading="eager">
                    @endif
                    <div class="profile-banner-overlay">
                        <h1>{{ $profil->judul }}</h1>
                    </div>
                </div>

                <!-- Profile Avatar -->
                @if($profil->foto_profil && $profil->foto_banner)
                    <div class="profile-avatar-wrapper animate-fade-in" style="animation-delay: 0.2s;">
                        <img src="{{ asset('storage/' . $profil->foto_profil) }}"
                             class="profile-avatar"
                             alt="{{ $profil->judul }}"
                             loading="eager">
                    </div>
                @endif
            @endif

            <!-- Profile Card -->
            <div class="profile-card animate-fade-in" style="animation-delay: 0.3s;">
                <div class="profile-card-body">
                    @if(!$profil->foto_banner && !$profil->foto_profil)
                        <h1 class="profile-title">{{ $profil->judul }}</h1>
                        <div class="profile-subtitle">
                            <i class="bi bi-person-badge me-2"></i>Profil Resmi
                        </div>
                    @endif

                    <!-- Info Box (Optional) -->
                    <div class="info-box">
                        <div class="info-box-icon">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        <div class="info-box-title">Tentang Profil Ini</div>
                        <p class="info-box-text">
                            Informasi lengkap mengenai profil, visi, misi, dan dedikasi dalam pengembangan sektor pertanian.
                        </p>
                    </div>

                    <!-- Profile Content -->
                    <div class="profile-content">
                        {!! $profil->konten_profil !!}
                    </div>

                    <!-- Social Share -->
                    <div class="social-share">
                        <div class="social-share-title">
                            <i class="bi bi-share me-2"></i>Bagikan Profil Ini
                        </div>
                        <div class="social-share-buttons">
                            <a href="https://wa.me/?text={{ urlencode($profil->judul . ' - ' . url()->current()) }}"
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
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($profil->judul) }}&url={{ urlencode(url()->current()) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="btn-social btn-twitter"
                               aria-label="Bagikan via Twitter">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($profil->judul) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="btn-social btn-telegram"
                               aria-label="Bagikan via Telegram">
                                <i class="bi bi-telegram"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="text-center">
                        <a href="{{ route('home') }}" class="btn-back">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali ke Beranda</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for anchor links
    document.querySelectorAll('.profile-content a[href^="#"]').forEach(anchor => {
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

    // Copy to clipboard untuk share links
    const shareButtons = document.querySelectorAll('.btn-social');
    shareButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Optional: Track social share analytics
            console.log('Shared to:', this.getAttribute('aria-label'));
        });
    });
});
</script>
@endsection
