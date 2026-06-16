@extends('layouts.admin')

@section('title', 'Theme & Logo Customization')

@section('content')
<div class="mb-4">
    <h1 class="fw-bold text-ink mb-1">Theme & Logo Customization</h1>
    <p class="text-muted small mb-0">Upload and change logos and section images for both the website and the admin panel.</p>
</div>

<form method="post" action="{{ route('admin.theme.customization.store') }}" class="table-card p-4" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- Logo Customization Card -->
        <div class="col-lg-6">
            <div class="border rounded-3 p-4 bg-white h-100">
                <h5 class="fw-bold text-ink mb-3 font-serif"><i class="bi bi-slack text-teal me-2"></i>Branding Logos</h5>
                
                <!-- Website Logo -->
                <div class="mb-4 pb-3 border-bottom border-light">
                    <label class="form-label fw-bold small text-ink">Website Logo</label>
                    <input class="form-control" type="file" name="site_logo">
                    <div class="mt-2 text-center p-2 bg-light rounded-3" style="max-width: 200px;">
                        <small class="text-muted d-block mb-1">Current website logo:</small>
                        <img src="{{ asset($settings['site_logo'] ?? 'images/logo.svg') }}" alt="Website Logo" style="height: 48px; max-width: 100%;">
                    </div>
                </div>

                <!-- Navbar Brand Image -->
                <div class="mb-4 pb-3 border-bottom border-light">
                    <label class="form-label fw-bold small text-ink">Navbar Brand Image</label>
                    <input class="form-control" type="file" name="site_brand_image">
                    <div class="mt-2 text-center p-2 bg-light rounded-3" style="max-width: 200px;">
                        <small class="text-muted d-block mb-1">Current navbar brand image:</small>
                        <img src="{{ asset($settings['site_brand_image'] ?? 'images/logo.svg') }}" alt="Navbar Brand Image" style="height: 48px; max-width: 100%;">
                    </div>
                </div>

                <!-- Admin Panel Logo -->
                <div>
                    <label class="form-label fw-bold small text-ink">Admin Panel Logo</label>
                    <input class="form-control" type="file" name="admin_logo">
                    <div class="mt-2 text-center p-2 bg-dark rounded-3" style="max-width: 200px;">
                        <small class="text-muted-light d-block mb-1 text-white opacity-50">Current admin logo:</small>
                        <img src="{{ asset($settings['admin_logo'] ?? 'images/logo.svg') }}" alt="Admin Logo" style="height: 48px; max-width: 100%;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Images Card -->
        <div class="col-lg-6">
            <div class="border rounded-3 p-4 bg-white h-100">
                <h5 class="fw-bold text-ink mb-3 font-serif"><i class="bi bi-images text-teal me-2"></i>Section Background Images</h5>
                
                <!-- About Section Image -->
                <div class="mb-4 pb-3 border-bottom border-light">
                    <label class="form-label fw-bold small text-ink">About Us Section Image</label>
                    <input class="form-control" type="file" name="about_image">
                    <div class="mt-2" style="max-width: 250px;">
                        <small class="text-muted d-block mb-1">Current about image:</small>
                        <img src="{{ $settings['about_image'] ?? 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80' }}" class="img-thumbnail rounded-3" alt="About image">
                    </div>
                </div>

                <!-- Dining Section Image -->
                <div>
                    <label class="form-label fw-bold small text-ink">Dining Section Preview Image</label>
                    <input class="form-control" type="file" name="dining_image">
                    <div class="mt-2" style="max-width: 250px;">
                        <small class="text-muted d-block mb-1">Current dining image:</small>
                        <img src="{{ $settings['dining_image'] ?? 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1200&q=80' }}" class="img-thumbnail rounded-3" alt="Dining image">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <button class="btn btn-teal mt-4 px-4 py-2.5 shadow-sm fw-bold"><i class="bi bi-cloud-arrow-up-fill me-2"></i>Update Logos & Images</button>
</form>
@endsection
