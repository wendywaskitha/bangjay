<!-- resources/views/partials/header.blade.php -->
<header class="bg-light py-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    @if(get_settings('general.app_logo'))
                        <img src="{{ asset('storage/' . get_settings('general.app_logo')) }}" alt="{{ get_settings('general.app_name') ?? config('app.name') }}" class="img-fluid" style="max-height: 50px;">
                    @else
                        <h4 class="mb-0">{{ get_settings('general.app_name') ?? config('app.name') }}</h4>
                    @endif
                </div>
            </div>
            <div class="col-md-8 text-md-end">
                @if(get_settings('contact.no_telepon') || get_settings('contact.email'))
                <div class="d-flex flex-column flex-md-row gap-3 justify-content-md-end">
                    @if(get_settings('contact.no_telepon'))
                    <div class="d-flex align-items-center">
                        <i class="bi bi-telephone me-2"></i>
                        <span>{{ get_settings('contact.no_telepon') }}</span>
                    </div>
                    @endif
                    @if(get_settings('contact.email'))
                    <div class="d-flex align-items-center">
                        <i class="bi bi-envelope me-2"></i>
                        <span>{{ get_settings('contact.email') }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</header>