@extends('layouts.app')

@section('title', 'Sebaran Bantuan')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
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

        /* Map Container */
        .map-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .map-header {
            background: linear-gradient(135deg, #f1f8f4, #e8f5e9);
            padding: 1.5rem 2rem;
            border-bottom: 2px solid #e9ecef;
        }

        .map-header h5 {
            margin: 0;
            font-weight: 700;
            color: var(--primary-green-dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .map-stats {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .map-stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 600;
        }

        .map-stat-value {
            color: var(--primary-green);
            font-weight: 700;
            font-size: 1.125rem;
        }

        #map {
            height: 600px;
            width: 100%;
            position: relative;
        }

        /* Custom Popup */
        .leaflet-popup-content-wrapper {
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .leaflet-popup-content {
            margin: 0;
            padding: 0;
            min-width: 280px;
        }

        .popup-header {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
            color: white;
            padding: 1.25rem;
            border-radius: 16px 16px 0 0;
        }

        .popup-header h6 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 700;
        }

        .popup-body {
            padding: 1.25rem;
        }

        .popup-info-item {
            display: flex;
            align-items: start;
            gap: 0.75rem;
            margin-bottom: 0.875rem;
            padding-bottom: 0.875rem;
            border-bottom: 1px solid #e9ecef;
        }

        .popup-info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .popup-info-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, rgba(24, 135, 46, 0.1), rgba(43, 162, 69, 0.15));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-green);
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .popup-info-content strong {
            display: block;
            font-size: 0.8125rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .popup-info-content p {
            margin: 0;
            color: #1a1a1a;
            font-weight: 600;
        }

        .popup-info-content ul {
            margin: 0.5rem 0 0;
            padding-left: 1.25rem;
            list-style: none;
        }

        .popup-info-content ul li {
            position: relative;
            padding-left: 1.25rem;
            margin-bottom: 0.375rem;
            color: #495057;
        }

        .popup-info-content ul li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: var(--primary-green);
            font-weight: 700;
        }

        /* Legend */
        .legend-container {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .legend-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .legend-title i {
            color: var(--primary-green);
        }

        .legend-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.25rem;
            background: #f8f9fa;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .legend-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .legend-marker {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }

        .legend-label {
            font-weight: 600;
            color: #495057;
        }

        /* Custom Marker Cluster */
        .marker-cluster-small,
        .marker-cluster-medium,
        .marker-cluster-large {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
            border: 3px solid white;
            box-shadow: 0 2px 12px rgba(24, 135, 46, 0.3);
        }

        .marker-cluster-small div,
        .marker-cluster-medium div,
        .marker-cluster-large div {
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary-green-dark);
            font-weight: 700;
        }

        /* Loading State */
        .map-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            background: white;
            padding: 2rem 3rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            display: none;
        }

        .map-loading.active {
            display: block;
        }

        .spinner-border {
            color: var(--primary-green);
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

            #map {
                height: 450px;
            }

            .map-header {
                padding: 1.25rem 1.5rem;
            }

            .map-stats {
                gap: 1rem;
            }

            .legend-container {
                padding: 1.5rem;
            }

            .legend-grid {
                grid-template-columns: 1fr;
            }

            .leaflet-popup-content {
                min-width: 240px;
            }
        }

        @media (max-width: 576px) {
            #map {
                height: 400px;
            }

            .filter-section {
                padding: 1.25rem;
            }

            .btn-reset-filter {
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
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="text-center">
                <i class="bi bi-geo-alt me-2"></i>Peta Sebaran Bantuan
            </h1>
            <p class="text-center">Visualisasi distribusi bantuan pertanian di seluruh wilayah</p>
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
                <li class="breadcrumb-item active" aria-current="page">Sebaran Bantuan</li>
            </ol>
        </nav>
    </div>

    <!-- Filter Section -->
    <div class="filter-section animate-fade-in" style="animation-delay: 0.1s;">
        <h5 class="filter-section-title">
            <i class="bi bi-funnel"></i>
            <span>Filter Sebaran Bantuan</span>
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

    <!-- Map Container -->
    <div class="map-container animate-fade-in" style="animation-delay: 0.2s;">
        <div class="map-header">
            <h5>
                <i class="bi bi-map-fill"></i>
                <span>Peta Interaktif Sebaran Bantuan</span>
            </h5>
            <div class="map-stats">
                <div class="map-stat-item">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Total Lokasi: <span class="map-stat-value" id="totalLokasi">{{ $sebaranBantuan->count() }}</span></span>
                </div>
                <div class="map-stat-item">
                    <i class="bi bi-people-fill"></i>
                    <span>Kelompok Tani: <span class="map-stat-value" id="totalKelompok">{{ $sebaranBantuan->pluck('kelompok_tani_id')->unique()->count() }}</span></span>
                </div>
                <div class="map-stat-item">
                    <i class="bi bi-gift-fill"></i>
                    <span>Jenis Bantuan: <span class="map-stat-value" id="totalJenis">{{ \App\Models\JenisBantuan::count() }}</span></span>
                </div>
            </div>
        </div>
        <div id="map">
            <div class="map-loading" id="mapLoading">
                <div class="text-center">
                    <div class="spinner-border mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-0 fw-bold">Memuat peta...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="legend-container animate-fade-in" style="animation-delay: 0.3s;">
        <h5 class="legend-title">
            <i class="bi bi-info-circle"></i>
            <span>Keterangan Peta</span>
        </h5>
        <div class="legend-grid">
            <div class="legend-item">
                <div class="legend-marker" style="background-color: #0d6efd;"></div>
                <span class="legend-label">Alsintan</span>
            </div>
            <div class="legend-item">
                <div class="legend-marker" style="background-color: #198754;"></div>
                <span class="legend-label">Bibit</span>
            </div>
            <div class="legend-item">
                <div class="legend-marker" style="background-color: #ffc107;"></div>
                <span class="legend-label">Pupuk & Lainnya</span>
            </div>
            <div class="legend-item">
                <div class="legend-marker" style="background: linear-gradient(135deg, #18872e, #2ba245);"></div>
                <span class="legend-label">Cluster (Beberapa Lokasi)</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script>
        // Show loading
        document.getElementById('mapLoading').classList.add('active');

        // Inisialisasi peta
        const map = L.map('map').setView([-4.0, 122.5], 10); // Default: Sulawesi Tenggara

        // Tambahkan tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Initialize marker cluster group
        const markers = L.markerClusterGroup({
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });

        // Ambil data sebaran bantuan
        const sebaranBantuan = @json($sebaranBantuan);
        let allMarkers = [];

        // Fungsi untuk menentukan warna marker
        function getColorByKategori(kategoriList) {
            const kategoriStr = kategoriList.toLowerCase();
            if (kategoriStr.includes('alsintan')) return '#0d6efd';
            if (kategoriStr.includes('bibit')) return '#198754';
            return '#ffc107';
        }

        // Fungsi untuk membuat marker
        function createMarker(item) {
            if (!item.kelompok_tani.latitude || !item.kelompok_tani.longitude) {
                return null;
            }

            // Gabungkan semua jenis bantuan
            let jenisBantuanList = '';
            let kategoriList = '';

            if (item.jenis_bantuans && item.jenis_bantuans.length > 0) {
                item.jenis_bantuans.forEach(function(jenis) {
                    jenisBantuanList += `<li>${jenis.nama_bantuan}</li>`;
                    kategoriList += jenis.kategori_bantuan.nama_kategori + ' ';
                });
            }

            const popupContent = `
                <div class="popup-header">
                    <h6><i class="bi bi-people-fill me-2"></i>${item.kelompok_tani.nama_kelompok}</h6>
                </div>
                <div class="popup-body">
                    <div class="popup-info-item">
                        <div class="popup-info-icon">
                            <i class="bi bi-map"></i>
                        </div>
                        <div class="popup-info-content">
                            <strong>Lokasi</strong>
                            <p>${item.kelompok_tani.desa.nama_desa}, ${item.kelompok_tani.desa.kecamatan.nama_kecamatan}</p>
                        </div>
                    </div>
                    <div class="popup-info-item">
                        <div class="popup-info-icon">
                            <i class="bi bi-gift"></i>
                        </div>
                        <div class="popup-info-content">
                            <strong>Jenis Bantuan</strong>
                            <ul>${jenisBantuanList || '<li>Tidak ada data</li>'}</ul>
                        </div>
                    </div>
                    <div class="popup-info-item">
                        <div class="popup-info-icon">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <div class="popup-info-content">
                            <strong>Tahun</strong>
                            <p>${item.jenis_bantuans.length > 0 ? item.jenis_bantuans[0].periode_tahun : 'N/A'}</p>
                        </div>
                    </div>
                </div>
            `;

            const color = getColorByKategori(kategoriList);
            const icon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });

            const marker = L.marker(
                [parseFloat(item.kelompok_tani.latitude), parseFloat(item.kelompok_tani.longitude)],
                { icon: icon }
            );

            marker.bindPopup(popupContent, {
                maxWidth: 320,
                className: 'custom-popup'
            });

            // Store item data with marker for filtering
            marker.itemData = item;

            return marker;
        }

        // Add all markers
        sebaranBantuan.forEach(function(item) {
            const marker = createMarker(item);
            if (marker) {
                allMarkers.push(marker);
                markers.addLayer(marker);
            }
        });

        map.addLayer(markers);

        // Fit map to markers
        if (allMarkers.length > 0) {
            const group = new L.featureGroup(allMarkers);
            map.fitBounds(group.getBounds().pad(0.1));
        }

        // Hide loading
        setTimeout(() => {
            document.getElementById('mapLoading').classList.remove('active');
        }, 1000);

        // Filter function
        function filterMap() {
            const tahun = document.getElementById('tahunFilter').value;
            const kategori = document.getElementById('kategoriFilter').value;
            const kecamatan = document.getElementById('kecamatanFilter').value;
            const desa = document.getElementById('desaFilter').value;

            markers.clearLayers();
            let filteredMarkers = [];
            let filteredCount = 0;

            allMarkers.forEach(function(marker) {
                const item = marker.itemData;
                let showMarker = true;

                // Filter tahun
                if (tahun && item.jenis_bantuans.length > 0) {
                    const hasTahun = item.jenis_bantuans.some(jenis => jenis.periode_tahun == tahun);
                    if (!hasTahun) showMarker = false;
                }

                // Filter kategori
                if (kategori && item.jenis_bantuans.length > 0) {
                    const hasKategori = item.jenis_bantuans.some(jenis => jenis.kategori_bantuan.id == kategori);
                    if (!hasKategori) showMarker = false;
                }

                // Filter kecamatan
                if (kecamatan && item.kelompok_tani.desa.kecamatan_id != kecamatan) {
                    showMarker = false;
                }

                // Filter desa
                if (desa && item.kelompok_tani.desa_id != desa) {
                    showMarker = false;
                }

                if (showMarker) {
                    markers.addLayer(marker);
                    filteredMarkers.push(marker);
                    filteredCount++;
                }
            });

            // Update stats
            document.getElementById('totalLokasi').textContent = filteredCount;

            // Fit bounds if filtered
            if (filteredMarkers.length > 0) {
                const group = new L.featureGroup(filteredMarkers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        // Event listeners
        document.getElementById('tahunFilter').addEventListener('change', filterMap);
        document.getElementById('kategoriFilter').addEventListener('change', filterMap);
        document.getElementById('kecamatanFilter').addEventListener('change', filterMap);
        document.getElementById('desaFilter').addEventListener('change', filterMap);

        // Reset filter
        document.getElementById('resetFilter').addEventListener('click', function() {
            document.getElementById('tahunFilter').value = '';
            document.getElementById('kategoriFilter').value = '';
            document.getElementById('kecamatanFilter').value = '';
            document.getElementById('desaFilter').value = '';
            filterMap();
        });

        // Responsive map
        window.addEventListener('resize', function() {
            map.invalidateSize();
        });
    </script>
@endpush
