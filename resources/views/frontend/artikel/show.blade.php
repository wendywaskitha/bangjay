@extends('layouts.app')

@section('title', $article->judul)

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

    /* Breadcrumb */
    .breadcrumb-custom {
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
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
        transition: var(--transition-base);
    }

    .breadcrumb-item a:hover {
        color: var(--primary-green-dark);
    }

    /* Article Header */
    .article-header {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
        padding: 4rem 0;
        color: white;
        margin-bottom: 3rem;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 4px 20px rgba(24, 135, 46, 0.2);
        position: relative;
        overflow: hidden;
    }

    .article-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .article-header-content {
        position: relative;
        z-index: 1;
    }

    .article-header h1 {
        font-size: clamp(1.75rem, 4vw, 2.5rem);
        font-weight: 800;
        margin-bottom: 1.5rem;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }

    .article-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        margin-bottom: 0;
    }

    .article-meta-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9375rem;
        opacity: 0.95;
    }

    .meta-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
    }

    /* Featured Image */
    .article-featured-image {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        margin-bottom: 3rem;
        position: relative;
    }

    .article-featured-image img {
        width: 100%;
        height: 500px;
        object-fit: cover;
    }

    /* Article Content */
    .article-content-wrapper {
        background: white;
        border-radius: 20px;
        padding: 3rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
    }

    .article-content {
        line-height: 1.8;
        color: #495057;
        font-size: 1.0625rem;
    }

    .article-content p {
        margin-bottom: 1.5rem;
    }

    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 16px;
        margin: 2rem 0;
        box-shadow: var(--shadow-sm);
    }

    .article-content h1,
    .article-content h2,
    .article-content h3 {
        color: var(--primary-green-dark);
        margin-top: 2.5rem;
        margin-bottom: 1.25rem;
        font-weight: 700;
    }

    .article-content h1 {
        font-size: 2rem;
        border-left: 5px solid var(--primary-green);
        padding-left: 1rem;
    }

    .article-content h2 {
        font-size: 1.75rem;
    }

    .article-content h3 {
        font-size: 1.5rem;
    }

    .article-content ul,
    .article-content ol {
        padding-left: 2rem;
        margin-bottom: 1.5rem;
    }

    .article-content li {
        margin-bottom: 0.75rem;
    }

    .article-content blockquote {
        border-left: 4px solid var(--primary-green);
        padding-left: 1.5rem;
        margin: 2rem 0;
        font-style: italic;
        color: #6c757d;
    }

    /* Sidebar */
    .sidebar-sticky {
        position: sticky;
        top: 100px;
    }

    /* Profile Card */
    .profile-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
        transition: var(--transition-base);
    }

    .profile-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .profile-card-header {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        color: white;
        padding: 1.5rem;
        text-align: center;
    }

    .profile-card-header h5 {
        margin: 0;
        font-weight: 700;
        font-size: 1.125rem;
    }

    .profile-card-body {
        padding: 2rem;
        text-align: center;
    }

    .profile-image-wrapper {
        width: 130px;
        height: 130px;
        margin: 0 auto 1.25rem;
        position: relative;
    }

    .profile-image {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #f8f9fa;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .profile-icon-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(24, 135, 46, 0.1), rgba(43, 162, 69, 0.15));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: var(--primary-green);
    }

    .profile-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.75rem;
    }

    .profile-description {
        color: #6c757d;
        font-size: 0.9375rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .btn-profile {
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
        transition: var(--transition-base);
    }

    .btn-profile:hover {
        background: linear-gradient(135deg, var(--primary-green-dark), var(--primary-green));
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(24, 135, 46, 0.3);
        color: white;
    }

    /* Related Articles Card */
    .related-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: var(--transition-base);
    }

    .related-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .related-card-header {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        color: white;
        padding: 1.5rem;
    }

    .related-card-header h5 {
        margin: 0;
        font-weight: 700;
        font-size: 1.125rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .related-card-body {
        padding: 1.5rem;
    }

    .related-article-item {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        transition: var(--transition-base);
        margin-bottom: 1rem;
    }

    .related-article-item:hover {
        border-color: var(--primary-green-light);
        box-shadow: var(--shadow-sm);
        transform: translateX(5px);
    }

    .related-article-item:last-child {
        margin-bottom: 0;
    }

    .related-article-image {
        width: 100%;
        height: 100px;
        object-fit: cover;
    }

    .related-article-content {
        padding: 1rem;
    }

    .related-article-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .related-article-title a {
        color: inherit;
        text-decoration: none;
        transition: var(--transition-base);
    }

    .related-article-title a:hover {
        color: var(--primary-green);
    }

    .related-article-meta {
        font-size: 0.8125rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Action Buttons */
    .article-actions {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 2rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
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
        transition: var(--transition-base);
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
        transition: var(--transition-base);
        cursor: pointer;
    }

    .btn-share:hover {
        background: var(--primary-green);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(24, 135, 46, 0.3);
    }

    /* Share Menu */
    .share-menu {
        display: none;
        position: absolute;
        bottom: 100%;
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow-lg);
        padding: 1rem;
        margin-bottom: 0.5rem;
        z-index: 100;
    }

    .share-menu.active {
        display: block;
    }

    .share-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 8px;
        color: #495057;
        text-decoration: none;
        transition: var(--transition-base);
    }

    .share-link:hover {
        background: #f8f9fa;
        color: var(--primary-green);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .sidebar-sticky {
            position: static;
            top: auto;
        }

        .article-content-wrapper {
            padding: 2rem;
        }
    }

    @media (max-width: 768px) {
        .article-header {
            padding: 2.5rem 0;
            margin-bottom: 2rem;
        }

        .article-meta {
            gap: 1.5rem;
        }

        .article-featured-image img {
            height: 300px;
        }

        .article-content-wrapper {
            padding: 1.5rem;
        }

        .article-content {
            font-size: 1rem;
        }

        .profile-card-body {
            padding: 1.5rem;
        }

        .related-card-body {
            padding: 1rem;
        }

        .article-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-back,
        .btn-share {
            width: 100%;
            justify-content: center;
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

<div class="container-fluid px-0">
    <!-- Article Header -->
    <div class="article-header">
        <div class="article-header-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        @if($article->kategori)
                            <span class="badge" style="background: rgba(255, 255, 255, 0.2); color: white; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.875rem; margin-bottom: 1rem; display: inline-block;">
                                <i class="bi bi-tag-fill me-1"></i>
                                {{ $article->kategori->nama_kategori }}
                            </span>
                        @endif
                        <h1>{{ $article->judul }}</h1>
                        <div class="article-meta">
                            <div class="article-meta-item">
                                <div class="meta-icon">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                <span>{{ $article->published_at->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="article-meta-item">
                                <div class="meta-icon">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <span>{{ $article->author->name ?? 'Admin' }}</span>
                            </div>
                            <div class="article-meta-item">
                                <div class="meta-icon">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <span>{{ ceil(str_word_count(strip_tags($article->konten)) / 200) }} menit baca</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                <li class="breadcrumb-item">
                    <a href="{{ route('artikel.index') }}">
                        <i class="bi bi-newspaper me-1"></i>Artikel
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($article->judul, 50) }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-4 order-lg-2 mb-4">
            <div class="sidebar-sticky">
                <!-- Bang Jai Profile Card -->
                <div class="profile-card animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="profile-card-header">
                        <h5>Profil Bang Jai</h5>
                    </div>
                    <div class="profile-card-body">
                        @php
                            $profil = \App\Models\ProfilBangJai::where('is_active', true)->first();
                        @endphp
                        @if($profil)
                            <div class="profile-image-wrapper">
                                @if($profil->foto_profil)
                                    <img src="{{ asset('storage/' . $profil->foto_profil) }}"
                                         alt="{{ $profil->judul }}"
                                         class="profile-image">
                                @else
                                    <div class="profile-icon-placeholder">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                @endif
                            </div>
                            <h6 class="profile-title">{{ $profil->judul }}</h6>
                            <p class="profile-description">
                                {!! Str::limit(strip_tags($profil->konten_profil), 120) !!}
                            </p>
                            <a href="{{ route('profil.index') }}" class="btn-profile">
                                <span>Lihat Profil Lengkap</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        @else
                            <div class="profile-icon-placeholder" style="margin: 0 auto 1.5rem;">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <h6 class="profile-title">Profil Bang Jai</h6>
                            <p class="profile-description">Profil Bang Jai akan segera ditampilkan</p>
                        @endif
                    </div>
                </div>

                <!-- Related Articles -->
                @if($relatedArticles->count() > 0)
                <div class="related-card animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="related-card-header">
                        <h5>
                            <i class="bi bi-collection"></i>
                            <span>Artikel Terkait</span>
                        </h5>
                    </div>
                    <div class="related-card-body">
                        @foreach($relatedArticles as $related)
                        <div class="related-article-item">
                            @if($related->thumbnail)
                                <img src="{{ asset('storage/' . $related->thumbnail) }}"
                                     class="related-article-image"
                                     alt="{{ $related->judul }}">
                            @else
                                <div class="related-article-image" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-newspaper" style="font-size: 2rem; color: #dee2e6;"></i>
                                </div>
                            @endif
                            <div class="related-article-content">
                                <h6 class="related-article-title">
                                    <a href="{{ route('artikel.show', $related->slug) }}">
                                        {{ Str::limit($related->judul, 60) }}
                                    </a>
                                </h6>
                                <div class="related-article-meta">
                                    <i class="bi bi-calendar3"></i>
                                    <span>{{ $related->published_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8 order-lg-1">
            <!-- Featured Image -->
            @if($article->thumbnail)
            <div class="article-featured-image animate-fade-in" style="animation-delay: 0.3s;">
                <img src="{{ asset('storage/' . $article->thumbnail) }}"
                     alt="{{ $article->judul }}">
            </div>
            @endif

            <!-- Article Content -->
            <div class="article-content-wrapper animate-fade-in" style="animation-delay: 0.4s;">
                <div class="article-content">
                    {!! $article->konten !!}
                </div>
            </div>

            <!-- Article Actions -->
            <div class="article-actions animate-fade-in" style="animation-delay: 0.5s;">
                <a href="{{ route('artikel.index') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Artikel</span>
                </a>
                <div style="position: relative;">
                    <button class="btn-share" id="shareBtn">
                        <i class="bi bi-share"></i>
                        <span>Bagikan Artikel</span>
                    </button>
                    <div class="share-menu" id="shareMenu">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="share-link">
                            <i class="bi bi-facebook" style="color: #1877F2; font-size: 1.25rem;"></i>
                            <span>Facebook</span>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->judul) }}&url={{ urlencode(url()->current()) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="share-link">
                            <i class="bi bi-twitter" style="color: #1DA1F2; font-size: 1.25rem;"></i>
                            <span>Twitter</span>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($article->judul . ' - ' . url()->current()) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="share-link">
                            <i class="bi bi-whatsapp" style="color: #25D366; font-size: 1.25rem;"></i>
                            <span>WhatsApp</span>
                        </a>
                        <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->judul) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="share-link">
                            <i class="bi bi-telegram" style="color: #0088cc; font-size: 1.25rem;"></i>
                            <span>Telegram</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Share button functionality
    const shareBtn = document.getElementById('shareBtn');
    const shareMenu = document.getElementById('shareMenu');

    if (shareBtn && shareMenu) {
        shareBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            shareMenu.classList.toggle('active');
        });

        // Close share menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!shareBtn.contains(e.target) && !shareMenu.contains(e.target)) {
                shareMenu.classList.remove('active');
            }
        });
    }

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

    // Smooth scroll for anchor links in content
    document.querySelectorAll('.article-content a[href^="#"]').forEach(anchor => {
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

@endsection
