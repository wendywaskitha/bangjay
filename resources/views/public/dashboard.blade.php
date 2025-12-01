@extends('layouts.app')

@section('title', 'Dashboard Monitoring - Bang Jai')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --primary-green: #18872e;
            --primary-green-dark: #146624;
            --primary-green-light: #2ba245;
        }

        /* Page Header */
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
            color: white;
            padding: 3rem;
            border-radius: 20px;
            margin-bottom: 2.5rem;
            box-shadow: 0 8px 32px rgba(24, 135, 46, 0.3);
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .dashboard-header-content {
            position: relative;
            z-index: 1;
        }

        .dashboard-header h1 {
            font-size: clamp(2rem, 5vw, 2.75rem);
            font-weight: 800;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }

        .dashboard-header p {
            font-size: 1.125rem;
            opacity: 0.95;
            margin: 0;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-green), var(--primary-green-light));
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 32px rgba(24, 135, 46, 0.2);
        }

        .stat-card-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-info {
            flex: 1;
        }

        .stat-label {
            font-size: 0.8125rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.75rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1a1a1a;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 0.875rem;
            color: #28a745;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, rgba(24, 135, 46, 0.1), rgba(43, 162, 69, 0.15));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--primary-green);
        }

        /* Chart Cards */
        .chart-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            height: 100%;
        }

        .chart-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .chart-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chart-card-title i {
            color: var(--primary-green);
            font-size: 1.5rem;
        }

        .chart-container {
            position: relative;
            height: 350px;
        }

        /* Table Styles */
        .table-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
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
            padding: 1rem;
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
        }

        .table-custom tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }

        .rank-badge {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9375rem;
        }

        .badge-custom {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8125rem;
        }

        .badge-primary-custom {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
            color: white;
        }

        /* Map Styles */
        #map {
            height: 500px;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .leaflet-popup-content-wrapper {
            border-radius: 12px;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .filter-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .form-select-custom {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-select-custom:focus {
            border-color: var(--primary-green-light);
            box-shadow: 0 0 0 0.2rem rgba(24, 135, 46, 0.1);
        }

        /* Loading State */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 20px;
        }

        .spinner-border-custom {
            width: 3rem;
            height: 3rem;
            color: var(--primary-green);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 2rem 1.5rem;
                margin-bottom: 2rem;
            }

            .stat-card {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .stat-value {
                font-size: 2rem;
            }

            .stat-icon {
                width: 60px;
                height: 60px;
                font-size: 1.75rem;
            }

            .chart-card {
                padding: 1.5rem;
            }

            .chart-container {
                height: 300px;
            }

            #map {
                height: 400px;
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
<div class="container-fluid py-4">
    <!-- Dashboard Header -->
    <div class="dashboard-header animate-fade-in">
        <div class="dashboard-header-content">
            <h1>
                <i class="bi bi-speedometer2 me-3"></i>Dashboard Monitoring
            </h1>
            <p>Monitor dan analisis distribusi bantuan pertanian secara real-time</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section animate-fade-in" style="animation-delay: 0.1s;">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="filter-label">
                    <i class="bi bi-calendar3 me-1"></i>Periode
                </label>
                <select class="form-select form-select-custom" id="periodeFilter">
                    <option value="all">Semua Periode</option>
                    <option value="month">Bulan Ini</option>
                    <option value="quarter">Kuartal Ini</option>
                    <option value="year" selected>Tahun Ini</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">
                    <i class="bi bi-geo-alt me-1"></i>Kabupaten
                </label>
                <select class="form-select form-select-custom" id="kabupatenFilter">
                    <option value="">Semua Kabupaten</option>
                    @foreach($bantuanByKabupaten as $data)
                        <option value="{{ $data->kabupaten_id ?? '' }}">{{ $data->nama_kabupaten }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">
                    <i class="bi bi-tag me-1"></i>Jenis Bantuan
                </label>
                <select class="form-select form-select-custom" id="jenisFilter">
                    <option value="">Semua Jenis</option>
                    @foreach($bantuanByJenis as $data)
                        <option value="{{ $data->id }}">{{ $data->nama_bantuan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary-custom w-100" onclick="applyFilters()">
                    <i class="bi bi-funnel me-2"></i>Terapkan Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6 animate-fade-in" style="animation-delay: 0.2s;">
            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <div class="stat-label">Total Kelompok Tani</div>
                        <div class="stat-value">{{ number_format($totalKelompokTani) }}</div>
                        <div class="stat-change">
                            <i class="bi bi-arrow-up-circle"></i>
                            <span>Terdaftar</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 animate-fade-in" style="animation-delay: 0.3s;">
            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <div class="stat-label">Total Sebaran Bantuan</div>
                        <div class="stat-value">{{ number_format($totalSebaranBantuan) }}</div>
                        <div class="stat-change">
                            <i class="bi bi-arrow-up-circle"></i>
                            <span>Tersalurkan</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-gift-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 animate-fade-in" style="animation-delay: 0.4s;">
            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <div class="stat-label">Jenis Bantuan</div>
                        <div class="stat-value">{{ number_format($totalJenisBantuan) }}</div>
                        <div class="stat-change">
                            <i class="bi bi-arrow-up-circle"></i>
                            <span>Tipe</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 animate-fade-in" style="animation-delay: 0.5s;">
            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <div class="stat-label">Rata-rata / Kelompok</div>
                        <div class="stat-value">
                            @if($totalKelompokTani > 0)
                                {{ number_format($totalSebaranBantuan / $totalKelompokTani, 1) }}
                            @else
                                0
                            @endif
                        </div>
                        <div class="stat-change">
                            <i class="bi bi-calculator"></i>
                            <span>Bantuan</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Bantuan by Kabupaten -->
        <div class="col-xl-6 animate-fade-in" style="animation-delay: 0.6s;">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i class="bi bi-pie-chart"></i>
                        <span>Distribusi per Kabupaten</span>
                    </h3>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i>Export Data</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-printer me-2"></i>Print</a></li>
                        </ul>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="bantuanByKabupatenChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bantuan by Jenis -->
        <div class="col-xl-6 animate-fade-in" style="animation-delay: 0.7s;">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i class="bi bi-diagram-3"></i>
                        <span>Distribusi per Jenis Bantuan</span>
                    </h3>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i>Export Data</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-printer me-2"></i>Print</a></li>
                        </ul>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="bantuanByJenisChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution by Month and Top Groups -->
    <div class="row g-4 mb-4">
        <!-- Distribution by Month -->
        <div class="col-xl-7 animate-fade-in" style="animation-delay: 0.8s;">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Trend Distribusi (12 Bulan)</span>
                    </h3>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i>Export Data</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-printer me-2"></i>Print</a></li>
                        </ul>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="bantuanByMonthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top 5 Kelompok Tani -->
        <div class="col-xl-5 animate-fade-in" style="animation-delay: 0.9s;">
            <div class="table-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i class="bi bi-trophy"></i>
                        <span>Top 5 Kelompok Tani</span>
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Kelompok</th>
                                <th>Lokasi</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topKelompokTani as $index => $kelompok)
                            <tr>
                                <td>
                                    <div class="rank-badge">{{ $index + 1 }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $kelompok->nama_kelompok }}</div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $kelompok->desa->nama_desa ?? '-' }}, {{ $kelompok->desa->kecamatan->nama_kecamatan ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge badge-primary-custom">
                                        {{ $kelompok->sebaran_bantuans_count }} Bantuan
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Tidak ada data
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Distributions -->
    <div class="row animate-fade-in" style="animation-delay: 1s;">
        <div class="col-12">
            <div class="table-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i class="bi bi-clock-history"></i>
                        <span>Distribusi Bantuan Terbaru</span>
                    </h3>
                    <a href="{{ route('sebaran-bantuan.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kelompok Tani</th>
                                <th>Lokasi</th>
                                <th>Jenis Bantuan</th>
                                <th>Volume</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDistributions as $distribution)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $distribution->tanggal_penetapan->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $distribution->tanggal_penetapan->diffForHumans() }}</small>
                                </td>
                                <td class="fw-bold">{{ $distribution->kelompokTani->nama_kelompok }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ $distribution->kelompokTani->desa->nama_desa ?? '-' }},
                                        {{ $distribution->kelompokTani->desa->kecamatan->nama_kecamatan ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    @forelse($distribution->jenisBantuans as $jenis)
                                        <span class="badge badge-primary-custom mb-1">{{ $jenis->nama_bantuan }}</span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td>
                                    @forelse($distribution->jenisBantuans as $jenis)
                                        <div>{{ $jenis->pivot->volume ?? '-' }} {{ $jenis->pivot->satuan ?? '' }}</div>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Tersalurkan
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Tidak ada data distribusi terbaru
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="row animate-fade-in" style="animation-delay: 1.1s;">
        <div class="col-12">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">
                        <i class="bi bi-map"></i>
                        <span>Peta Sebaran Kelompok Tani Penerima Bantuan</span>
                    </h3>
                    <a href="{{ route('sebaran-bantuan.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-arrow-right me-1"></i>Lihat Peta Lengkap
                    </a>
                </div>
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Color palette
    const colors = [
        '#18872e', '#2ba245', '#28a745', '#20c997', '#17a2b8',
        '#007bff', '#6610f2', '#6f42c1', '#e83e8c', '#fd7e14'
    ];

    // Chart for Bantuan by Kabupaten
    const kabupatenCtx = document.getElementById('bantuanByKabupatenChart');
    new Chart(kabupatenCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($bantuanByKabupaten as $data)
                    '{{ $data->nama_kabupaten }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($bantuanByKabupaten as $data)
                        {{ $data->total }},
                    @endforeach
                ],
                backgroundColor: colors,
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Chart for Bantuan by Jenis
    const jenisCtx = document.getElementById('bantuanByJenisChart');
    new Chart(jenisCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($bantuanByJenis as $data)
                    '{{ $data->nama_bantuan }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($bantuanByJenis as $data)
                        {{ $data->total }},
                    @endforeach
                ],
                backgroundColor: colors,
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            }
        }
    });

    // Chart for Bantuan by Month
    const monthCtx = document.getElementById('bantuanByMonthChart');
    new Chart(monthCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($bantuanByMonth as $data)
                    '{{ \Carbon\Carbon::create(null, $data->month, 1)->format('M Y') }}',
                @endforeach
            ],
            datasets: [{
                label: 'Jumlah Bantuan',
                data: [
                    @foreach($bantuanByMonth as $data)
                        {{ $data->total }},
                    @endforeach
                ],
                borderColor: '#18872e',
                backgroundColor: 'rgba(24, 135, 46, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#18872e',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            }
        }
    });

    // Initialize map
    const map = L.map('map').setView([-4.0, 122.5], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Add markers
    @foreach($kelompokTaniWithBantuan as $kelompok)
        @if($kelompok->latitude && $kelompok->longitude)
            const customIcon{{ $kelompok->id }} = L.divIcon({
                className: 'custom-div-icon',
                html: '<div style="background-color: #18872e; width: 30px; height: 30px; border-radius: 50%; border: 4px solid white; box-shadow: 0 2px 12px rgba(0,0,0,0.3);"></div>',
                iconSize: [38, 38],
                iconAnchor: [19, 19]
            });

            const marker{{ $kelompok->id }} = L.marker([{{ $kelompok->latitude }}, {{ $kelompok->longitude }}], {
                icon: customIcon{{ $kelompok->id }}
            }).addTo(map);

            marker{{ $kelompok->id }}.bindPopup(`
                <div style="min-width: 200px;">
                    <h6 style="margin-bottom: 0.5rem; font-weight: 700; color: #18872e;">
                        <i class="bi bi-people-fill me-2"></i>{{ $kelompok->nama_kelompok }}
                    </h6>
                    <p style="margin: 0.5rem 0; color: #6c757d; font-size: 0.875rem;">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $kelompok->desa->nama_desa ?? '' }}, {{ $kelompok->desa->kecamatan->nama_kecamatan ?? '' }}
                    </p>
                    <p style="margin: 0; font-weight: 600; color: #18872e;">
                        <i class="bi bi-gift me-1"></i>
                        {{ $kelompok->sebaran_bantuans_count }} Bantuan
                    </p>
                </div>
            `);
        @endif
    @endforeach

    // Auto-fit map to markers
    @if($kelompokTaniWithBantuan->count() > 0)
        const bounds = L.latLngBounds([
            @foreach($kelompokTaniWithBantuan as $kelompok)
                @if($kelompok->latitude && $kelompok->longitude)
                    [{{ $kelompok->latitude }}, {{ $kelompok->longitude }}],
                @endif
            @endforeach
        ]);
        map.fitBounds(bounds, { padding: [50, 50] });
    @endif

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

// Filter function
function applyFilters() {
    // Implementation for filtering
    console.log('Filters applied');
    // Reload dengan query parameters
}
</script>
@endpush
