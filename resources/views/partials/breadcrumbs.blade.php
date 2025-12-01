<!-- resources/views/partials/breadcrumbs.blade.php -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        @yield('breadcrumbs')
    </ol>
</nav>