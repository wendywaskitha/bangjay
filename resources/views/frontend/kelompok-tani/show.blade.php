@extends('layouts.app')

@section('title', $kelompokTani->nama_kelompok)

@push('styles')
    @if($kelompokTani->latitude && $kelompokTani->longitude)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @endif
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

        /* Hero Header */
        .kelompok-hero {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
            color: white;
            padding: 3rem 2.5rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(24, 135, 46, 0.2);
            position: relative;
            overflow: hidden;
        }

        .kelompok-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .kelompok-hero-content {
            position: relative;
            z-index: 1;
        }

        .kelompok-hero h1 {
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }

        .hero-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .hero-meta-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .hero-meta-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .hero-meta-content strong {
            display: block;
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 0.25rem;
        }

        .hero-meta-content span {
            display: block;
            font-size: 1.125rem;
            font-weight: 700;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 16px;
            padding: 1.75rem;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            border-color: var(--primary-green-light);
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(24, 135, 46, 0.15);
        }

        .info-card-icon {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, rgba(24, 135, 46, 0.1), rgba(43, 162, 69, 0.15));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary-green);
            margin-bottom: 1.25rem;
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
            font-size: 1.375rem;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }

        /* Section */
        .detail-section {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .section-title {
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

        .section-title i {
            color: var(--primary-green);
            font-size: 1.75rem;
        }

        /* Profile Text */
        .profile-text {
            background: linear-gradient(135deg, #f1f8f4, #e8f5e9);
            border-left: 5px solid var(--primary-green);
            padding: 1.75rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            line-height: 1.8;
            color: #495057;
        }

        /* Map */
        #locationMap {
            height: 400px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        /* Table */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .table-custom {
            margin: 0;
        }

        .table-custom thead {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
            color: white;
        }

        .table-custom thead th {
            border: none;
            padding: 1.125rem 1rem;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8125rem;
            letter-spacing: 0.5px;
        }

        .table-custom tbody tr {
            transition: all 0.3s ease;
        }

        .table-custom tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
        }

        .table-custom tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }

        .badge-jabatan {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8125rem;
        }

        /* Bantuan Card */
        .bantuan-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .bantuan-card:hover {
            border-color: var(--primary-green-light);
            box-shadow: 0 8px 24px rgba(24, 135, 46, 0.15);
        }

        .bantuan-card-header {
            background: linear-gradient(135deg, #f1f8f4, #e8f5e9);
            padding: 1.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .bantuan-card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--primary-green-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .bantuan-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .bantuan-list-item {
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .bantuan-list-item:last-child {
            border-bottom: none;
        }

        .bantuan-list-item:hover {
            background: #f8f9fa;
        }

        .bantuan-item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .bantuan-item-title {
            flex: 1;
        }

        .bantuan-item-title strong {
            font-size: 1.0625rem;
            color: #1a1a1a;
        }

        .bantuan-category-badge {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
            color: white;
            padding: 0.375rem 0.875rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8125rem;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }

        .bantuan-item-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            color: #6c757d;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .bantuan-item-meta span {
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .bantuan-description {
            color: #495057;
            line-height: 1.6;
            margin-top: 0.75rem;
        }

        .bantuan-catatan {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .bantuan-catatan strong {
            color: #997404;
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
        }

        .btn-back:hover {
            background: linear-gradient(135deg, var(--primary-green-dark), var(--primary-green));
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(24, 135, 46, 0.4);
            color: white;
        }

        /* Empty State */
        .empty-state-small {
            text-align: center;
            padding: 3rem 2rem;
            color: #6c757d;
        }

        .empty-state-small i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .kelompok-hero {
                padding: 2rem 1.5rem;
            }

            .hero-meta {
                gap: 1.5rem;
            }

            .detail-section {
                padding: 2rem 1.5rem;
            }

            #locationMap {
                height: 300px;
            }

            .table-wrapper {
                border-radius: 8px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .kelompok-hero {
                padding: 1.75rem 1.25rem;
            }

            .detail-section {
                padding: 1.75rem 1.25rem;
            }

            .bantuan-item-header {
                flex-direction: column;
            }

            .bantuan-item-meta {
                flex-direction: column;
                gap: 0.5rem;
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

        /* Leaflet Popup Custom */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .leaflet-popup-content {
            margin: 1rem 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
        }
    </style>
@endpush

@section('content')
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
                    <a href="{{ route('kelompok-tani.index') }}">
                        <i class="bi bi-people me-1"></i>Kelompok Tani
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($kelompokTani->nama_kelompok, 50) }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-11 mx-auto">
            <!-- Hero Section -->
            <div class="kelompok-hero animate-fade-in" style="animation-delay: 0.1s;">
                <div class="kelompok-hero-content">
                    <h1>{{ $kelompokTani->nama_kelompok }}</h1>
                    <div class="hero-meta">
                        <div class="hero-meta-item">
                            <div class="hero-meta-icon">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div class="hero-meta-content">
                                <strong>Lokasi</strong>
                                <span>{{ $kelompokTani->desa->nama_desa }}, {{ $kelompokTani->desa->kecamatan->nama_kecamatan }}</span>
                            </div>
                        </div>
                        <div class="hero-meta-item">
                            <div class="hero-meta-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="hero-meta-content">
                                <strong>Jumlah Anggota</strong>
                                <span>{{ $kelompokTani->jumlah_anggota }} Orang</span>
                            </div>
                        </div>
                        <div class="hero-meta-item">
                            <div class="hero-meta-icon">
                                <i class="bi bi-gift-fill"></i>
                            </div>
                            <div class="hero-meta-content">
                                <strong>Total Bantuan</strong>
                                <span>{{ $kelompokTani->sebaranBantuans->count() }} Bantuan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profil -->
            @if($kelompokTani->profil)
            <div class="profile-text animate-fade-in" style="animation-delay: 0.2s;">
                <h6 class="fw-bold mb-3" style="color: var(--primary-green-dark);">
                    <i class="bi bi-info-circle me-2"></i>Tentang Kelompok
                </h6>
                {!! nl2br(e($kelompokTani->profil)) !!}
            </div>
            @endif

            <!-- Map Section -->
            @if($kelompokTani->latitude && $kelompokTani->longitude)
            <div class="detail-section animate-fade-in" style="animation-delay: 0.3s;">
                <h2 class="section-title">
                    <i class="bi bi-map"></i>
                    <span>Lokasi Kelompok Tani</span>
                </h2>
                <div id="locationMap"></div>
            </div>
            @endif

            <!-- Anggota Section -->
            <div class="detail-section animate-fade-in" style="animation-delay: 0.4s;">
                <h2 class="section-title">
                    <i class="bi bi-people-fill"></i>
                    <span>Daftar Anggota ({{ $kelompokTani->kelompokTaniAnggotas->count() }})</span>
                </h2>

                @if($kelompokTani->kelompokTaniAnggotas->count() > 0)
                <div class="table-wrapper">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Anggota</th>
                                <th>Jabatan</th>
                                <th>No. HP</th>
                                <th>Luas Lahan</th>
                                <th>Komoditas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelompokTani->kelompokTaniAnggotas as $index => $anggota)
                            <tr>
                                <td class="fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold">{{ $anggota->nama_anggota }}</div>
                                </td>
                                <td>
                                    <span class="badge-jabatan {{ $anggota->jabatan == 'Ketua' ? 'bg-primary' : ($anggota->jabatan == 'Sekretaris' ? 'bg-success' : 'bg-info') }}">
                                        {{ $anggota->jabatan }}
                                    </span>
                                </td>
                                <td>
                                    @if($anggota->no_hp)
                                        <i class="bi bi-telephone me-1"></i>{{ $anggota->no_hp }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($anggota->luas_lahan)
                                        <strong>{{ $anggota->luas_lahan }}</strong> Ha
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $anggota->jenisKomoditas->nama_komoditas ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state-small">
                    <i class="bi bi-people"></i>
                    <p class="mb-0">Belum ada data anggota kelompok tani</p>
                </div>
                @endif
            </div>

            <!-- Riwayat Bantuan -->
            <div class="detail-section animate-fade-in" style="animation-delay: 0.5s;">
                <h2 class="section-title">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Bantuan ({{ $kelompokTani->sebaranBantuans->count() }})</span>
                </h2>

                @if($kelompokTani->sebaranBantuans->count() > 0)
                    @foreach($kelompokTani->sebaranBantuans as $sebaran)
                    <div class="bantuan-card">
                        <div class="bantuan-card-header">
                            <h3 class="bantuan-card-title">
                                <i class="bi bi-calendar-event"></i>
                                <span>
                                    Bantuan {{ $sebaran->tanggal_penetapan ? $sebaran->tanggal_penetapan->translatedFormat('d F Y') : 'Tanggal Tidak Tersedia' }}
                                </span>
                            </h3>
                        </div>
                        <div class="bantuan-card-body">
                            @if($sebaran->jenisBantuans->count() > 0)
                                <ul class="bantuan-list">
                                    @foreach($sebaran->jenisBantuans as $jenisBantuan)
                                    <li class="bantuan-list-item">
                                        <div class="bantuan-item-header">
                                            <div class="bantuan-item-title">
                                                <strong>{{ $jenisBantuan->nama_bantuan }}</strong>
                                                <span class="bantuan-category-badge">
                                                    <i class="bi bi-tag-fill"></i>
                                                    {{ $jenisBantuan->kategoriBantuan->nama_kategori }}
                                                </span>
                                            </div>
                                            <div class="bantuan-item-meta">
                                                <span>
                                                    <i class="bi bi-calendar3"></i>
                                                    Tahun {{ $jenisBantuan->periode_tahun }}
                                                </span>
                                                @if($jenisBantuan->pivot->volume)
                                                <span>
                                                    <i class="bi bi-box-seam"></i>
                                                    {{ $jenisBantuan->pivot->volume }} {{ $jenisBantuan->pivot->satuan ?: 'Unit' }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($jenisBantuan->deskripsi)
                                        <p class="bantuan-description">{{ Str::limit($jenisBantuan->deskripsi, 150) }}</p>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                            @endif

                            @if($sebaran->catatan)
                            <div class="bantuan-catatan">
                                <strong><i class="bi bi-sticky me-2"></i>Catatan:</strong> {{ $sebaran->catatan }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state-small">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0">Kelompok tani ini belum pernah menerima bantuan</p>
                    </div>
                @endif
            </div>

            <!-- Back Button -->
            <div class="text-center animate-fade-in" style="animation-delay: 0.6s;">
                <a href="{{ route('kelompok-tani.index') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Daftar Kelompok Tani</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @if($kelompokTani->latitude && $kelompokTani->longitude)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi peta
            const locationMap = L.map('locationMap').setView([{{ $kelompokTani->latitude }}, {{ $kelompokTani->longitude }}], 15);

            // Tambahkan tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(locationMap);

            // Custom icon
            const customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: '<div style="background-color: #18872e; width: 30px; height: 30px; border-radius: 50%; border: 4px solid white; box-shadow: 0 2px 12px rgba(0,0,0,0.3);"></div>',
                iconSize: [38, 38],
                iconAnchor: [19, 19]
            });

            // Tambahkan marker
            const marker = L.marker([{{ $kelompokTani->latitude }}, {{ $kelompokTani->longitude }}], {
                icon: customIcon
            }).addTo(locationMap);

            const popupContent = `
                <div style="min-width: 200px;">
                    <h6 style="margin-bottom: 0.5rem; font-weight: 700; color: #18872e;">
                        <i class="bi bi-people-fill me-2"></i>{{ json_encode($kelompokTani->nama_kelompok) }}
                    </h6>
                    <p style="margin: 0; color: #6c757d; font-size: 0.875rem;">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $kelompokTani->desa->nama_desa }}, {{ $kelompokTani->desa->kecamatan->nama_kecamatan }}
                    </p>
                </div>
            `;

            marker.bindPopup(popupContent).openPopup();

            // Add circle to show area
            L.circle([{{ $kelompokTani->latitude }}, {{ $kelompokTani->longitude }}], {
                color: '#18872e',
                fillColor: '#2ba245',
                fillOpacity: 0.15,
                radius: 500
            }).addTo(locationMap);
        });
    </script>
    @endif

    <script>
        // Intersection Observer untuk animasi
        document.addEventListener('DOMContentLoaded', function() {
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
@endpush
