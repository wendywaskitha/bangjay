<!-- resources/views/partials/navbar.blade.php -->
<style>
    :root {
        --navbar-height: 60px;
        --primary-green: #18872e;
        --primary-green-dark: #146624;
        --primary-green-light: #2ba245;
    }

    .navbar-custom {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
        box-shadow: 0 2px 20px rgba(24, 135, 46, 0.15);
        padding: 0.5rem 0;
        min-height: var(--navbar-height);
        backdrop-filter: blur(10px);
        position: sticky;
        top: 0;
        z-index: 1030;
        transition: all 0.3s ease;
    }

    .navbar-custom.scrolled {
        box-shadow: 0 4px 30px rgba(24, 135, 46, 0.25);
        padding: 0.4rem 0;
    }

    .navbar-brand {
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .navbar-brand img {
        max-height: 45px;
        transition: all 0.3s ease;
        filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.2));
    }

    .navbar-custom.scrolled .navbar-brand img {
        max-height: 38px;
    }

    .navbar-brand:hover {
        transform: translateY(-2px);
    }

    .navbar-nav {
        align-items: center;
        gap: 0.25rem;
    }

    .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 600;
        font-size: 0.8125rem;
        padding: 0.5rem 0.75rem !important;
        border-radius: 50px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.4s ease, height 0.4s ease;
    }

    .nav-link:hover::before {
        width: 100%;
        height: 100%;
        border-radius: 50px;
    }

    .nav-link:hover {
        color: white !important;
        transform: translateY(-2px);
    }

    .nav-link.active {
        background: rgba(255, 255, 255, 0.25);
        color: white !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0.375rem;
        left: 50%;
        transform: translateX(-50%);
        width: 24px;
        height: 3px;
        background: white;
        border-radius: 2px;
    }

    .btn-whatsapp {
        background: white;
        color: var(--primary-green) !important;
        border: 2px solid white;
        border-radius: 50px;
        padding: 0.625rem 1.5rem !important;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-whatsapp:hover {
        background: var(--primary-green-dark);
        color: white !important;
        border-color: var(--primary-green-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    }

    .btn-whatsapp i {
        font-size: 1.125rem;
    }

    .navbar-toggler {
        border: 2px solid rgba(255, 255, 255, 0.5);
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
        border-color: white;
    }

    .navbar-toggler:hover {
        border-color: white;
        background: rgba(255, 255, 255, 0.1);
    }

    .navbar-toggler-icon {
        width: 1.5rem;
        height: 1.5rem;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2.5' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* Mobile Menu Styles */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green) 100%);
            margin: 1rem -1rem -0.75rem;
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .navbar-nav {
            gap: 0.5rem;
        }

        .nav-link {
            padding: 0.65rem 0.9rem !important;
            margin: 0.15rem 0;
            font-size: 0.75rem;
        }

        .nav-link.active::after {
            display: none;
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 4px solid white;
            border-radius: 8px;
        }

        .btn-whatsapp {
            width: 100%;
            justify-content: center;
            margin-top: 1rem;
        }

        .navbar-nav.ms-lg-auto {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
    }

    /* Tablet Responsive */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .nav-link {
            font-size: 1rem;
        }
    }

    /* Desktop Large */
    @media (min-width: 1200px) {
        .navbar-nav {
            gap: 0.5rem;
        }

        .nav-link {
            padding: 0.625rem 1.25rem !important;
        }
    }

    /* Animation for mobile menu */
    .navbar-collapse.collapsing {
        transition: height 0.35s ease;
    }

    .navbar-collapse.show {
        animation: slideDown 0.35s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Badge for notifications (optional) */
    .nav-link .badge {
        position: absolute;
        top: 0.25rem;
        right: 0.25rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.625rem;
        border-radius: 50px;
        background: #dc3545;
        color: white;
    }

    /* Dropdown support (jika ada submenu) */
    .dropdown-menu {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        padding: 0.5rem;
        margin-top: 0.5rem;
    }

    .dropdown-item {
        border-radius: 8px;
        padding: 0.625rem 1rem;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .dropdown-item:hover {
        background: rgba(24, 135, 46, 0.1);
        color: var(--primary-green);
        transform: translateX(4px);
    }

    /* Accessibility improvements */
    .nav-link:focus,
    .btn-whatsapp:focus {
        outline: 2px solid white;
        outline-offset: 2px;
    }

    /* Loading state (optional) */
    .navbar-custom.loading {
        opacity: 0.7;
        pointer-events: none;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}" aria-label="Kembali ke Beranda">
            @if(get_settings('general.app_logo'))
                <img src="{{ asset('storage/' . get_settings('general.app_logo')) }}"
                     alt="{{ get_settings('general.app_name') ?? config('app.name') }}"
                     class="img-fluid"
                     loading="eager">
            @else
                <span>{{ get_settings('general.app_name') ?? config('app.name') }}</span>
            @endif
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}"
                       href="{{ route('home') }}"
                       aria-current="{{ request()->is('/') ? 'page' : 'false' }}">
                        <i class="bi bi-house-door me-1"></i>
                        Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('profil*') ? 'active' : '' }}"
                       href="{{ route('profil.index') }}"
                       aria-current="{{ request()->is('profil*') ? 'page' : 'false' }}">
                        <i class="bi bi-person-circle me-1"></i>
                        Profil Bang Jai
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('artikel*') ? 'active' : '' }}"
                       href="{{ route('artikel.index') }}"
                       aria-current="{{ request()->is('artikel*') ? 'page' : 'false' }}">
                        <i class="bi bi-newspaper me-1"></i>
                        Artikel Berita
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('katalog-bantuan*') ? 'active' : '' }}"
                       href="{{ route('katalog-bantuan.index') }}"
                       aria-current="{{ request()->is('katalog-bantuan*') ? 'page' : 'false' }}">
                        <i class="bi bi-book me-1"></i>
                        Katalog Bantuan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('sebaran-bantuan*') ? 'active' : '' }}"
                       href="{{ route('sebaran-bantuan.index') }}"
                       aria-current="{{ request()->is('sebaran-bantuan*') ? 'page' : 'false' }}">
                        <i class="bi bi-map me-1"></i>
                        Sebaran Bantuan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('kelompok-tani*') ? 'active' : '' }}"
                       href="{{ route('kelompok-tani.index') }}"
                       aria-current="{{ request()->is('kelompok-tani*') ? 'page' : 'false' }}">
                        <i class="bi bi-people me-1"></i>
                        Kelompok Tani
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('kontak*') ? 'active' : '' }}"
                       href="{{ route('kontak.index') }}"
                       aria-current="{{ request()->is('kontak*') ? 'page' : 'false' }}">
                        <i class="bi bi-envelope me-1"></i>
                        Kontak
                    </a>
                </li>
            </ul>

            @if(get_settings('contact.link_whatsapp'))
            <ul class="navbar-nav ms-lg-auto">
                <li class="nav-item">
                    <a class="nav-link btn-whatsapp"
                       href="{{ get_settings('contact.link_whatsapp') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="Hubungi kami via WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                </li>
            </ul>
            @endif
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar-custom');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    const navbarToggler = document.querySelector('.navbar-toggler');

    // Navbar scroll effect
    let lastScroll = 0;
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }

        lastScroll = currentScroll;
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideNav = navbar.contains(event.target);
        const isNavbarExpanded = navbarCollapse.classList.contains('show');

        if (!isClickInsideNav && isNavbarExpanded) {
            navbarToggler.click();
        }
    });

    // Close mobile menu when clicking on nav link
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            }
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#navbarNav') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const navbarHeight = navbar.offsetHeight;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // Add active state animation
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateY(-2px)';
            }
        });

        link.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = '';
            }
        });
    });

    // Keyboard navigation improvements
    navbar.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && navbarCollapse.classList.contains('show')) {
            navbarToggler.click();
        }
    });
});
</script>
