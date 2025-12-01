@extends('layouts.app')

@section('title', 'Kontak Kami')

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

    /* Form Card */
    .contact-form-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .form-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-title i {
        color: var(--primary-green);
        font-size: 2rem;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.9375rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label i {
        color: var(--primary-green);
    }

    .form-control,
    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.875rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-green-light);
        box-shadow: 0 0 0 0.2rem rgba(24, 135, 46, 0.1);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 150px;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        border: none;
        color: white;
        padding: 1rem 2.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.0625rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(24, 135, 46, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, var(--primary-green-dark), var(--primary-green));
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(24, 135, 46, 0.4);
    }

    /* Alert */
    .alert-custom {
        border: none;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .alert-custom.alert-success {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
    }

    .alert-custom i {
        font-size: 1.5rem;
    }

    /* Info Card */
    .info-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        height: 100%;
    }

    .info-card-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .info-card-title i {
        color: var(--primary-green);
        font-size: 1.75rem;
    }

    .contact-info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .contact-info-item {
        display: flex;
        align-items: start;
        gap: 1rem;
        margin-bottom: 1.75rem;
        padding: 1.25rem;
        background: #f8f9fa;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .contact-info-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }

    .contact-info-item:last-child {
        margin-bottom: 0;
    }

    .contact-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, rgba(24, 135, 46, 0.1), rgba(43, 162, 69, 0.15));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-green);
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .contact-info-content {
        flex: 1;
    }

    .contact-info-label {
        font-size: 0.8125rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.375rem;
    }

    .contact-info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
        line-height: 1.6;
    }

    /* Social Media */
    .social-section {
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 2px solid #e9ecef;
    }

    .social-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #495057;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .social-links {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .social-link {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        text-decoration: none;
    }

    .social-link:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
    }

    .social-link.facebook {
        background: #1877F2;
    }

    .social-link.youtube {
        background: #FF0000;
    }

    .social-link.whatsapp {
        background: #25D366;
    }

    .social-link.instagram {
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
    }

    .social-link.twitter {
        background: #1DA1F2;
    }

    /* Map Section */
    .map-section {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-top: 2rem;
    }

    .map-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .map-title i {
        color: var(--primary-green);
        font-size: 1.75rem;
    }

    .map-container {
        height: 400px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .contact-form-card,
        .info-card {
            padding: 2rem 1.5rem;
        }

        .map-container {
            height: 300px;
        }

        .social-links {
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .contact-form-card,
        .info-card {
            padding: 1.75rem 1.25rem;
        }

        .btn-submit {
            width: 100%;
            justify-content: center;
        }

        .form-title {
            font-size: 1.5rem;
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
    .btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .btn-submit .spinner-border {
        width: 1.25rem;
        height: 1.25rem;
        border-width: 2px;
    }
</style>

<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="text-center">
                <i class="bi bi-envelope-paper me-2"></i>Hubungi Kami
            </h1>
            <p class="text-center">Silakan hubungi kami untuk informasi lebih lanjut atau kirim pesan</p>
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
                <li class="breadcrumb-item active" aria-current="page">Kontak Kami</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <!-- Form Section -->
        <div class="col-lg-8 mb-4">
            <div class="contact-form-card animate-fade-in" style="animation-delay: 0.1s;">
                <h2 class="form-title">
                    <i class="bi bi-send"></i>
                    <span>Kirim Pesan</span>
                </h2>

                @if(session('success'))
                <div class="alert-custom alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                </div>
                @endif

                <form method="POST" action="{{ route('kontak.store') }}" id="contactForm">
                    @csrf
                    <div class="mb-4">
                        <label for="nama" class="form-label">
                            <i class="bi bi-person"></i>
                            Nama Lengkap
                        </label>
                        <input type="text"
                               class="form-control @error('nama') is-invalid @enderror"
                               id="nama"
                               name="nama"
                               value="{{ old('nama') }}"
                               placeholder="Masukkan nama lengkap Anda"
                               required>
                        @error('nama')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i>
                            Alamat Email
                        </label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="nama@contoh.com"
                               required>
                        @error('email')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="pesan" class="form-label">
                            <i class="bi bi-chat-text"></i>
                            Pesan Anda
                        </label>
                        <textarea class="form-control @error('pesan') is-invalid @enderror"
                                  id="pesan"
                                  name="pesan"
                                  rows="6"
                                  placeholder="Tulis pesan Anda di sini..."
                                  required>{{ old('pesan') }}</textarea>
                        @error('pesan')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Minimal 10 karakter</small>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="bi bi-send-fill"></i>
                        <span>Kirim Pesan</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Section -->
        <div class="col-lg-4 mb-4">
            <div class="info-card animate-fade-in" style="animation-delay: 0.2s;">
                <h2 class="info-card-title">
                    <i class="bi bi-info-circle"></i>
                    <span>Informasi Kontak</span>
                </h2>

                <ul class="contact-info-list">
                    @if(get_settings('contact.alamat_kantor'))
                    <li class="contact-info-item">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="contact-info-content">
                            <div class="contact-info-label">Alamat Kantor</div>
                            <p class="contact-info-value">{{ get_settings('contact.alamat_kantor') }}</p>
                        </div>
                    </li>
                    @endif

                    @if(get_settings('contact.no_telepon'))
                    <li class="contact-info-item">
                        <div class="contact-icon">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div class="contact-info-content">
                            <div class="contact-info-label">Nomor Telepon</div>
                            <p class="contact-info-value">
                                <a href="tel:{{ get_settings('contact.no_telepon') }}" style="color: inherit; text-decoration: none;">
                                    {{ get_settings('contact.no_telepon') }}
                                </a>
                            </p>
                        </div>
                    </li>
                    @endif

                    @if(get_settings('contact.email'))
                    <li class="contact-info-item">
                        <div class="contact-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="contact-info-content">
                            <div class="contact-info-label">Email</div>
                            <p class="contact-info-value">
                                <a href="mailto:{{ get_settings('contact.email') }}" style="color: inherit; text-decoration: none;">
                                    {{ get_settings('contact.email') }}
                                </a>
                            </p>
                        </div>
                    </li>
                    @endif
                </ul>

                <!-- Social Media -->
                <div class="social-section">
                    <h3 class="social-title">
                        <i class="bi bi-share"></i>
                        <span>Ikuti Kami</span>
                    </h3>
                    <div class="social-links">
                        @if(get_settings('contact.link_facebook'))
                        <a href="{{ get_settings('contact.link_facebook') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="social-link facebook"
                           aria-label="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        @endif

                        @if(get_settings('contact.link_youtube'))
                        <a href="{{ get_settings('contact.link_youtube') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="social-link youtube"
                           aria-label="YouTube">
                            <i class="bi bi-youtube"></i>
                        </a>
                        @endif

                        @if(get_settings('contact.link_whatsapp'))
                        <a href="{{ get_settings('contact.link_whatsapp') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="social-link whatsapp"
                           aria-label="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        @endif

                        @if(get_settings('contact.link_instagram'))
                        <a href="{{ get_settings('contact.link_instagram') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="social-link instagram"
                           aria-label="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        @endif

                        @if(get_settings('contact.link_twitter'))
                        <a href="{{ get_settings('contact.link_twitter') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="social-link twitter"
                           aria-label="Twitter">
                            <i class="bi bi-twitter"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section (Optional) -->
    @if(get_settings('contact.map_embed'))
    <div class="map-section animate-fade-in" style="animation-delay: 0.3s;">
        <h2 class="map-title">
            <i class="bi bi-map"></i>
            <span>Lokasi Kami</span>
        </h2>
        <div class="map-container">
            {!! get_settings('contact.map_embed') !!}
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function(e) {
        // Disable button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            <span>Mengirim...</span>
        `;
    });

    // Character counter for pesan
    const pesanTextarea = document.getElementById('pesan');
    if (pesanTextarea) {
        const counter = document.createElement('small');
        counter.className = 'text-muted float-end';
        pesanTextarea.parentElement.appendChild(counter);

        function updateCounter() {
            const length = pesanTextarea.value.length;
            counter.textContent = `${length} karakter`;
            if (length < 10) {
                counter.classList.add('text-danger');
                counter.classList.remove('text-muted');
            } else {
                counter.classList.add('text-muted');
                counter.classList.remove('text-danger');
            }
        }

        pesanTextarea.addEventListener('input', updateCounter);
        updateCounter();
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

    // Auto-dismiss success alert
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.5s ease';
            successAlert.style.opacity = '0';
            setTimeout(() => successAlert.remove(), 500);
        }, 5000);
    }
});
</script>
@endsection
