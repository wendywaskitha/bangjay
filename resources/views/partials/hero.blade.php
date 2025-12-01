<!-- resources/views/partials/hero.blade.php -->
<section class="hero bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                @if(isset($hero))
                    <h1 class="display-4 fw-bold">{{ $hero->judul ?? get_settings('appearance.hero_default_title') ?? 'Selamat Datang di '.(get_settings('general.app_name') ?? config('app.name')) }}</h1>
                    <p class="lead">{{ $hero->deskripsi_singkat ?? '' }}</p>
                    @if($hero->cta_text && $hero->cta_link)
                    <a href="{{ $hero->cta_link }}" class="btn btn-light btn-lg mt-3">{{ $hero->cta_text }}</a>
                    @endif
                @else
                    <h1 class="display-4 fw-bold">{{ get_settings('appearance.hero_default_title') ?? 'Selamat Datang di '.(get_settings('general.app_name') ?? config('app.name')) }}</h1>
                    <p class="lead">Platform aspirasi dan informasi bantuan pertanian berbasis Laravel + FilamentPHP</p>
                @endif
            </div>
            <div class="col-lg-4 text-center">
                <!-- Gambar hero bisa ditambahkan di sini -->
                @if(isset($hero) && $hero->gambar)
                <img src="{{ asset('storage/'.$hero->gambar) }}" alt="Hero Image" class="img-fluid rounded">
                @endif
            </div>
        </div>
    </div>
</section>