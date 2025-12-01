@extends('layouts.app')

@section('title', 'Artikel Berita')

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

    /* Page Header */
    .articles-header {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
        padding: 4rem 0;
        color: white;
        margin-bottom: 3rem;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 4px 20px rgba(24, 135, 46, 0.2);
        position: relative;
        overflow: hidden;
    }

    .articles-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .articles-header-content {
        position: relative;
        z-index: 1;
    }

    .articles-header h1 {
        font-size: clamp(2rem, 5vw, 2.75rem);
        font-weight: 800;
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
    }

    .articles-header p {
        font-size: 1.125rem;
        opacity: 0.95;
        max-width: 700px;
        margin: 0 auto;
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

    /* Search and Filter Section */
    .articles-filter {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2.5rem;
    }

    .filter-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        justify-content: center;
    }

    .filter-title i {
        color: var(--primary-green);
        font-size: 1.5rem;
    }

    /* Search Box */
    .search-box {
        max-width: 600px;
        margin: 0 auto 1.5rem;
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 1rem 3.5rem 1rem 1.5rem;
        border: 2px solid #e9ecef;
        border-radius: 50px;
        font-size: 1rem;
        transition: var(--transition-base);
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-green-light);
        box-shadow: 0 0 0 0.2rem rgba(24, 135, 46, 0.1);
    }

    .search-btn {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        border: none;
        color: white;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition-base);
        cursor: pointer;
    }

    .search-btn:hover {
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 4px 12px rgba(24, 135, 46, 0.3);
    }

    /* Category Badges */
    .categories-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.75rem;
    }

    .category-badge {
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: var(--transition-base);
        border: 2px solid #e9ecef;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: white;
        color: #495057;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .category-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        transition: var(--transition-base);
        z-index: -1;
    }

    .category-badge:hover::before {
        left: 0;
    }

    .category-badge:hover {
        color: white;
        border-color: var(--primary-green);
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .category-badge.active {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        color: white;
        border-color: var(--primary-green);
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .category-badge .badge {
        background: rgba(255, 255, 255, 0.2);
        color: inherit;
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .category-badge.active .badge,
    .category-badge:hover .badge {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }

    /* Article Cards */
    .article-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: var(--transition-base);
        box-shadow: var(--shadow-sm);
        height: 100%;
        background: white;
    }

    .article-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-lg);
    }

    .card-img-wrapper {
        position: relative;
        overflow: hidden;
        height: 240px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }

    .card-img-top {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .article-card:hover .card-img-top {
        transform: scale(1.1);
    }

    .article-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8125rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .article-date {
        position: absolute;
        bottom: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.95);
        color: var(--primary-green);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .card-body-custom {
        padding: 2rem;
        display: flex;
        flex-direction: column;
    }

    .card-title-custom {
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

    .card-text-custom {
        color: #6c757d;
        line-height: 1.7;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .card-meta {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 600;
    }

    .meta-item i {
        color: var(--primary-green);
    }

    .btn-read-more {
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
        justify-content: center;
        transition: var(--transition-base);
        width: 100%;
    }

    .btn-read-more:hover {
        background: linear-gradient(135deg, var(--primary-green-dark), var(--primary-green));
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(24, 135, 46, 0.4);
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
    }

    .empty-state-icon {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, rgba(24, 135, 46, 0.1), rgba(43, 162, 69, 0.15));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        font-size: 3.5rem;
        color: var(--primary-green);
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1rem;
    }

    .empty-state-text {
        color: #6c757d;
        font-size: 1.0625rem;
        margin-bottom: 2rem;
    }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 4rem;
        display: flex;
        justify-content: center;
    }

    .pagination {
        display: flex;
        gap: 0.5rem;
    }

    .page-item {
        list-style: none;
    }

    .page-link {
        color: var(--primary-green);
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        transition: var(--transition-base);
        text-decoration: none;
    }

    .page-link:hover {
        background: rgba(24, 135, 46, 0.1);
        border-color: var(--primary-green);
        color: var(--primary-green-dark);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        border-color: var(--primary-green);
        color: white;
        box-shadow: 0 4px 12px rgba(24, 135, 46, 0.3);
    }

    .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Stats Bar */
    .stats-bar {
        background: linear-gradient(135deg, #f1f8f4, #e8f5e9);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        justify-content: center;
        gap: 3rem;
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
    }

    /* Responsive */
    @media (max-width: 768px) {
        .articles-header {
            padding: 2.5rem 0;
            margin-bottom: 2rem;
        }

        .articles-filter {
            padding: 1.5rem;
        }

        .category-badge {
            font-size: 0.875rem;
            padding: 0.625rem 1.25rem;
        }

        .card-img-wrapper {
            height: 200px;
        }

        .card-body-custom {
            padding: 1.5rem;
        }

        .stats-bar {
            gap: 2rem;
        }

        .stat-value {
            font-size: 1.75rem;
        }
    }

    @media (max-width: 576px) {
        .card-img-wrapper {
            height: 180px;
        }

        .card-title-custom {
            font-size: 1.125rem;
        }

        .categories-wrapper {
            gap: 0.5rem;
        }

        .category-badge {
            font-size: 0.8125rem;
            padding: 0.5rem 1rem;
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
    <!-- Header -->
    <div class="articles-header">
        <div class="articles-header-content">
            <div class="container text-center">
                <h1>
                    <i class="bi bi-newspaper me-3"></i>Artikel & Berita
                </h1>
                <p>Berita terkini dan informasi seputar program pertanian Bang Jai</p>
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
                <li class="breadcrumb-item active" aria-current="page">Artikel Berita</li>
            </ol>
        </nav>
    </div>

    <!-- Stats Bar -->
    <div class="stats-bar animate-fade-in" style="animation-delay: 0.1s;">
        <div class="stat-item">
            <div class="stat-value">{{ $articles->total() }}</div>
            <div class="stat-label">Total Artikel</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $categories->count() }}</div>
            <div class="stat-label">Kategori</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $articles->count() }}</div>
            <div class="stat-label">Halaman Ini</div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="articles-filter animate-fade-in" style="animation-delay: 0.2s;">
        <h5 class="filter-title">
            <i class="bi bi-funnel"></i>
            <span>Cari & Filter Artikel</span>
        </h5>

        <!-- Search Box -->
        <div class="search-box">
            <form action="{{ route('artikel.index') }}" method="GET" id="searchForm">
                <input type="text"
                       name="search"
                       class="search-input"
                       placeholder="Cari artikel berdasarkan judul atau konten..."
                       value="{{ request('search') }}">
                <button type="submit" class="search-btn">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
            </form>
        </div>

        <!-- Categories -->
        <div class="categories-wrapper">
            <a href="{{ route('artikel.index') }}"
               class="category-badge {{ request()->routeIs('artikel.index') && !request('category') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i>
                <span>Semua Artikel</span>
            </a>
            @foreach($categories as $category)
                <a href="{{ route('artikel.index', ['category' => $category->slug]) }}"
                   class="category-badge {{ request('category') == $category->slug ? 'active' : '' }}">
                    <i class="bi bi-tag-fill"></i>
                    <span>{{ $category->nama_kategori }}</span>
                    <span class="badge">{{ $category->artikels_count }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Articles Grid -->
    <div class="row g-4">
        @forelse($articles as $index => $article)
            <div class="col-lg-4 col-md-6 animate-fade-in" style="animation-delay: {{ ($index * 0.1) + 0.3 }}s;">
                <div class="article-card">
                    <div class="card-img-wrapper">
                        @if($article->thumbnail)
                            <img src="{{ asset('storage/' . $article->thumbnail) }}"
                                 class="card-img-top"
                                 alt="{{ $article->judul }}"
                                 loading="lazy">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <i class="bi bi-newspaper" style="font-size: 3rem; color: #dee2e6;"></i>
                            </div>
                        @endif

                        @if($article->kategoriArtikel)
                            <div class="article-badge">
                                <i class="bi bi-tag-fill"></i>
                                <span>{{ $article->kategoriArtikel->nama_kategori }}</span>
                            </div>
                        @endif

                        <div class="article-date">
                            <i class="bi bi-calendar3"></i>
                            <span>{{ $article->published_at->format('d M Y') }}</span>
                        </div>
                    </div>

                    <div class="card-body-custom">
                        <h5 class="card-title-custom">{{ $article->judul }}</h5>

                        <div class="card-meta">
                            <div class="meta-item">
                                <i class="bi bi-person-circle"></i>
                                <span>{{ $article->author->name ?? 'Admin' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-eye"></i>
                                <span>{{ number_format($article->views ?? 0) }} views</span>
                            </div>
                        </div>

                        <p class="card-text-custom">
                            {!! Str::limit(strip_tags($article->isi), 130) !!}
                        </p>

                        <a href="{{ route('artikel.show', $article->slug) }}" class="btn-read-more">
                            <span>Baca Selengkapnya</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h3 class="empty-state-title">Artikel Tidak Ditemukan</h3>
                    <p class="empty-state-text">
                        @if(request('search'))
                            Tidak ada artikel yang cocok dengan pencarian "{{ request('search') }}".
                        @elseif(request('category'))
                            Belum ada artikel dalam kategori ini.
                        @else
                            Belum ada artikel yang dipublikasikan.
                        @endif
                    </p>
                    <a href="{{ route('artikel.index') }}" class="btn-read-more" style="max-width: 300px; margin: 0 auto;">
                        <i class="bi bi-arrow-left"></i>
                        <span>Lihat Semua Artikel</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($articles->hasPages())
        <div class="pagination-wrapper">
            {{ $articles->links() }}
        </div>
    @endif
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

    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('searchForm').submit();
            }
        });
    }

    // Smooth scroll untuk pagination
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
});
</script>

@endsection
