<!-- resources/views/partials/footer.blade.php -->
<style>
    :root {
        --primary-green: #18872e;
        --primary-green-dark: #146624;
        --primary-green-light: #2ba245;
    }

    .footer-custom {
        background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green) 100%);
        color: white;
        padding: 4rem 0 0;
        margin-top: auto;
        position: relative;
        overflow: hidden;
    }

    .footer-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-green-light), white, var(--primary-green-light));
    }

    .footer-custom::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50%;
        pointer-events: none;
    }

    .footer-content {
        position: relative;
        z-index: 1;
    }

    .footer-section {
        margin-bottom: 2rem;
    }

    .footer-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 0.75rem;
    }

    .footer-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, white, transparent);
        border-radius: 2px;
    }

    .footer-logo {
        max-height: 50px;
        margin-bottom: 1rem;
        filter: brightness(0) invert(1);
    }

    .footer-description {
        font-size: 0.9375rem;
        line-height: 1.7;
        opacity: 0.9;
        margin-bottom: 1.5rem;
    }

    .footer-contact-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-contact-item {
        display: flex;
        align-items: start;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding: 0.5rem 0;
        transition: all 0.3s ease;
    }

    .footer-contact-item:hover {
        transform: translateX(5px);
    }

    .footer-contact-item i {
        font-size: 1.125rem;
        margin-top: 0.125rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .footer-contact-item a {
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .footer-contact-item a:hover {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: underline;
    }

    .footer-links-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    .footer-links-list li a {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0;
        transition: all 0.3s ease;
    }

    .footer-links-list li a:hover {
        color: white;
        transform: translateX(5px);
    }

    .footer-links-list li a i {
        font-size: 0.75rem;
    }

    .social-links {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .social-link {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        transition: all 0.3s ease;
        text-decoration: none;
        border: 2px solid transparent;
    }

    .social-link:hover {
        background: white;
        color: var(--primary-green);
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .btn-contact {
        background: white;
        color: var(--primary-green);
        border: 2px solid white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .btn-contact:hover {
        background: transparent;
        color: white;
        border-color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
    }

    .footer-divider {
        border: none;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        margin: 2.5rem 0;
    }

    .footer-bottom {
        padding: 2rem 0;
        background: rgba(0, 0, 0, 0.2);
    }

    .footer-bottom-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .footer-copyright {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    .footer-credits {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.875rem;
        opacity: 0.9;
    }

    .footer-credits a {
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .footer-credits a:hover {
        color: rgba(255, 255, 255, 0.8);
    }

    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: 0 4px 16px rgba(24, 135, 46, 0.4);
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        border: none;
    }

    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
    }

    .back-to-top:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(24, 135, 46, 0.5);
    }

    /* Newsletter Section (Optional) */
    .newsletter-section {
        background: rgba(255, 255, 255, 0.1);
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
    }

    .newsletter-title {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .newsletter-form {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .newsletter-input {
        flex: 1;
        padding: 0.75rem 1.25rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 0.9375rem;
    }

    .newsletter-input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .newsletter-input:focus {
        outline: none;
        border-color: white;
        background: rgba(255, 255, 255, 0.15);
    }

    .newsletter-btn {
        background: white;
        color: var(--primary-green);
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .newsletter-btn:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .footer-custom {
            padding: 3rem 0 0;
        }

        .footer-section {
            margin-bottom: 2.5rem;
        }

        .footer-bottom-content {
            flex-direction: column;
            text-align: center;
        }

        .social-links {
            justify-content: center;
        }

        .newsletter-form {
            flex-direction: column;
        }

        .newsletter-btn {
            width: 100%;
        }

        .back-to-top {
            bottom: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
        }
    }

    @media (max-width: 576px) {
        .footer-custom {
            padding: 2.5rem 0 0;
        }

        .footer-title {
            font-size: 1.125rem;
        }

        .btn-contact {
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

    .animate-footer {
        animation: fadeInUp 0.6s ease-out forwards;
    }
</style>

<footer class="footer-custom">
    <div class="container footer-content">
        <div class="row">
            <!-- About Section -->
            <div class="col-lg-4 col-md-6 footer-section">
                <h5 class="footer-title">
                    @if(get_settings('general.app_logo'))
                        <img src="{{ asset('storage/' . get_settings('general.app_logo')) }}"
                             alt="{{ get_settings('general.app_name') ?? config('app.name') }}"
                             class="footer-logo">
                    @else
                        {{ get_settings('general.app_name') ?? config('app.name') }}
                    @endif
                </h5>
                <p class="footer-description">
                    {{ get_settings('appearance.footer_text') ?? 'Platform informasi bantuan pertanian dan rumah aspirasi untuk mendukung pengembangan kelompok tani dan sektor pertanian.' }}
                </p>

                <!-- Social Links -->
                <div class="social-links">
                    @if(get_settings('contact.link_facebook'))
                    <a href="{{ get_settings('contact.link_facebook') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="social-link"
                       aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    @endif

                    @if(get_settings('contact.link_youtube'))
                    <a href="{{ get_settings('contact.link_youtube') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="social-link"
                       aria-label="YouTube">
                        <i class="bi bi-youtube"></i>
                    </a>
                    @endif

                    @if(get_settings('contact.link_whatsapp'))
                    <a href="{{ get_settings('contact.link_whatsapp') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="social-link"
                       aria-label="WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    @endif

                    @if(get_settings('contact.link_instagram'))
                    <a href="{{ get_settings('contact.link_instagram') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="social-link"
                       aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    @endif

                    @if(get_settings('contact.link_twitter'))
                    <a href="{{ get_settings('contact.link_twitter') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="social-link"
                       aria-label="Twitter">
                        <i class="bi bi-twitter"></i>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 footer-section">
                <h5 class="footer-title">Tautan Cepat</h5>
                <ul class="footer-links-list">
                    <li>
                        <a href="{{ route('home') }}">
                            <i class="bi bi-chevron-right"></i>
                            <span>Beranda</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profil.index') }}">
                            <i class="bi bi-chevron-right"></i>
                            <span>Profil Bang Jai</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('katalog-bantuan.index') }}">
                            <i class="bi bi-chevron-right"></i>
                            <span>Katalog Bantuan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('sebaran-bantuan.index') }}">
                            <i class="bi bi-chevron-right"></i>
                            <span>Sebaran Bantuan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('kelompok-tani.index') }}">
                            <i class="bi bi-chevron-right"></i>
                            <span>Kelompok Tani</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('kontak.index') }}">
                            <i class="bi bi-chevron-right"></i>
                            <span>Kontak Kami</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6 footer-section">
                <h5 class="footer-title">Hubungi Kami</h5>
                <ul class="footer-contact-list">
                    @if(get_settings('contact.alamat_kantor'))
                    <li class="footer-contact-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <div>{{ get_settings('contact.alamat_kantor') }}</div>
                    </li>
                    @endif

                    @if(get_settings('contact.no_telepon'))
                    <li class="footer-contact-item">
                        <i class="bi bi-telephone-fill"></i>
                        <div>
                            <a href="tel:{{ get_settings('contact.no_telepon') }}">
                                {{ get_settings('contact.no_telepon') }}
                            </a>
                        </div>
                    </li>
                    @endif

                    @if(get_settings('contact.email'))
                    <li class="footer-contact-item">
                        <i class="bi bi-envelope-fill"></i>
                        <div>
                            <a href="mailto:{{ get_settings('contact.email') }}">
                                {{ get_settings('contact.email') }}
                            </a>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- CTA Section -->
            <div class="col-lg-3 col-md-6 footer-section">
                <h5 class="footer-title">Layanan</h5>
                <p class="mb-3" style="opacity: 0.9; font-size: 0.9375rem;">
                    Hubungi kami untuk informasi lebih lanjut tentang program bantuan pertanian.
                </p>

                @if(get_settings('contact.no_telepon'))
                <a href="tel:{{ get_settings('contact.no_telepon') }}" class="btn-contact">
                    <i class="bi bi-telephone-fill"></i>
                    <span>Hubungi Sekarang</span>
                </a>
                @endif

                @if(get_settings('contact.link_whatsapp'))
                <a href="{{ get_settings('contact.link_whatsapp') }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn-contact mt-2"
                   style="background: #25D366; color: white; border-color: #25D366;">
                    <i class="bi bi-whatsapp"></i>
                    <span>Chat WhatsApp</span>
                </a>
                @endif
            </div>
        </div>

        <hr class="footer-divider">
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <div class="footer-copyright">
                    <i class="bi bi-c-circle me-1"></i>
                    {{ date('Y') }} {{ get_settings('general.app_name') ?? config('app.name') }}. All Rights Reserved.
                </div>
                <div class="footer-credits">
                    <span>Powered by</span>
                    <a href="https://treeit.my.id" target="_blank" rel="noopener noreferrer">Arsicom</a>
                    {{-- <span>&</span> --}}
                    {{-- <a href="https://filamentphp.com" target="_blank" rel="noopener noreferrer">FilamentPHP</a> --}}
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="bi bi-arrow-up"></i>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Back to Top functionality
    const backToTopBtn = document.getElementById('backToTop');

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });

    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Smooth scroll for footer links
    document.querySelectorAll('.footer-links-list a').forEach(link => {
        link.addEventListener('click', function(e) {
            // Let normal navigation work, but add smooth behavior
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
});
</script>
