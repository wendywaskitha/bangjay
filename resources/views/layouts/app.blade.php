<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' . config('app.name', 'Rumah Aspirasi Bang Jai') : config('app.name', 'Rumah Aspirasi Bang Jai') }}</title>

    <!-- Favicon -->
    @if(get_settings('general.app_favicon'))
        <link rel="shortcut icon" href="{{ asset('storage/' . get_settings('general.app_favicon')) }}" type="image/x-icon">
        <link rel="icon" href="{{ asset('storage/' . get_settings('general.app_favicon')) }}" type="image/x-icon">
    @else
        <link rel="shortcut icon" href="{{ asset('storage/assets/site_favicon.ico') }}" type="image/x-icon">
        <link rel="icon" href="{{ asset('storage/assets/site_favicon.ico') }}" type="image/x-icon">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-green: #18872e;
            --primary-green-dark: #126522;
            --primary-green-light: #2ba245;
        }

        body {
            font-family: 'Nunito', sans-serif;
        }

        .navbar {
            background-color: var(--primary-green) !important;
        }

        .bg-primary, .btn-primary {
            background-color: var(--primary-green) !important;
            border-color: var(--primary-green) !important;
        }

        .btn-primary:hover {
            background-color: var(--primary-green-dark) !important;
            border-color: var(--primary-green-dark) !important;
        }

        .bg-primary-custom {
            background-color: var(--primary-green) !important;
        }

        .bg-primary-custom.text-white {
            color: white !important;
        }

        .text-primary-custom {
            color: var(--primary-green) !important;
        }

        .btn-outline-primary-custom {
            color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .btn-outline-primary-custom:hover {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 15px rgba(24, 135, 46, 0.2) !important;
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Header -->
    {{-- @include('partials.header') --}}

    <!-- Navigation -->
    @include('partials.navbar')

    <!-- Hero Section -->
    @hasSection('hero')
        @yield('hero')
    @endif

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
