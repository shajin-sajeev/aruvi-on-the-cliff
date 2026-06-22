@extends('layouts.admin')
@section('title', $config['title'])
@section('content')

@php
// Platform logo URLs for social-links
$platformLogos = [
    'facebook'  => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Facebook_Logo_%282019%29.png/600px-Facebook_Logo_%282019%29.png',
    'instagram' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Instagram_icon.png/600px-Instagram_icon.png',
    'twitter'   => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Logo_of_Twitter.svg/600px-Logo_of_Twitter.svg.png',
    'x'         => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/X_logo_2023_%28white_background%29.png/600px-X_logo_2023_%28white_background%29.png',
    'youtube'   => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/09/YouTube_full-color_icon_%282017%29.svg/600px-YouTube_full-color_icon_%282017%29.svg.png',
    'linkedin'  => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/LinkedIn_logo_initials.png/600px-LinkedIn_logo_initials.png',
    'pinterest' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/Pinterest-logo.png/600px-Pinterest-logo.png',
    'tiktok'    => 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a9/TikTok_logo.svg/600px-TikTok_logo.svg.png',
    'whatsapp'  => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/600px-WhatsApp.svg.png',
];
$currentPlatform = strtolower(old('platform', $item?->platform ?? ''));
$platformLogoUrl = $platformLogos[$currentPlatform] ?? null;
@endphp

<div class="mb-3">
    <a href="{{ route('admin.resources.index', $resource) }}" class="btn btn-sm btn-link text-teal text-decoration-none fw-semibold p-0">
        <i class="bi bi-arrow-left me-1"></i> Back to {{ $config['title'] }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    {{-- Card Header --}}
    <div class="form-card-header d-flex align-items-start align-items-sm-center justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="fw-bold font-serif mb-1 text-white">
                <i class="bi {{ $item ? 'bi-pencil-square' : 'bi-plus-circle-fill' }} me-2 opacity-75"></i>
                {{ $item ? 'Modify Existing Entry' : 'Create New Entry' }}
            </h5>
            <small class="text-white opacity-70">All required fields will be validated on save.</small>
        </div>
        {{-- Fix #3: badge text must be readable — use text-ink on white/light bg --}}
        <span class="badge bg-white text-ink border border-white border-opacity-50 px-3 py-2 text-uppercase tracking-wider extra-small fw-bold">
            {{ $resource }}
        </span>
    </div>

    <form method="post"
          action="{{ $item ? route('admin.resources.update',[$resource,$item->id]) : route('admin.resources.store',$resource) }}"
          class="p-3 p-sm-4 p-md-5 bg-white"
          enctype="multipart/form-data">
        @csrf
        @if($item) @method('patch') @endif

        <div class="row g-3 g-md-4">
            @foreach($config['fields'] as $field)
                @php([$name, $type] = array_pad(explode(':', $field, 2), 2, 'text'))

                {{-- Fix #2: skip 'icon' field for social-links entirely (replaced by platform logo preview) --}}
                @if($resource === 'social-links' && $name === 'icon')
                    @continue
                @endif

                <div class="{{ $type === 'textarea' ? 'col-12' : 'col-12 col-sm-6' }}">
                    <label class="form-label fw-bold small text-ink mb-2">
                        @php($fieldIcon = ['file' => 'bi-image', 'number' => 'bi-hash', 'checkbox' => 'bi-toggle-on', 'textarea' => 'bi-text-paragraph'][$type] ?? 'bi-input-cursor-text')
                        <i class="bi {{ $fieldIcon }} text-teal me-1"></i>
                        {{ str($name)->headline() }}
                    </label>

                    {{-- Settings file override --}}
                    @if($resource === 'settings' && $item && $item->type === 'file' && $name === 'value')
                        <input class="form-control" type="file" name="value">
                        @if($item->value)
                            <div class="mt-2 p-2 bg-light rounded-3 d-inline-block">
                                <small class="text-muted d-block mb-1"><i class="bi bi-eye-fill me-1"></i>Current:</small>
                                <img src="{{ asset($item->value) }}" alt="Preview" class="img-thumbnail" style="max-height:70px;">
                            </div>
                        @endif

                    {{-- Fix #1: section_key readonly for home-sections, pre-filled with about_preview --}}
                    @elseif($resource === 'home-sections' && $name === 'section_key')
                        <input class="form-control bg-light text-muted"
                               type="text"
                               name="section_key"
                               value="{{ old('section_key', $item?->section_key ?? 'about_preview') }}"
                               readonly
                               style="cursor:not-allowed;">
                        <small class="text-muted extra-small d-block mt-1">
                            <i class="bi bi-lock-fill me-1"></i>Section key is fixed and cannot be changed.
                        </small>

                    {{-- Fix #2: platform field for social-links — show logo preview alongside --}}
                    @elseif($resource === 'social-links' && $name === 'platform')
                        <div class="d-flex align-items-center gap-3">
                            <div id="platform-logo-wrap"
                                 class="border rounded-3 bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:52px;height:52px;overflow:hidden;transition:all 0.2s;">
                                @if($platformLogoUrl)
                                    <img id="platform-logo-img" src="{{ $platformLogoUrl }}"
                                         alt="{{ $currentPlatform }}"
                                         style="width:36px;height:36px;object-fit:contain;">
                                @else
                                    <i id="platform-logo-placeholder" class="bi bi-share text-teal fs-4"></i>
                                    <img id="platform-logo-img" src="" alt="" style="width:36px;height:36px;object-fit:contain;display:none;">
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <select class="form-select" name="platform" id="platform-select" required>
                                    <option value="">Select Platform…</option>
                                    @foreach(['Facebook','Instagram','Twitter','X','YouTube','LinkedIn','Pinterest','TikTok','WhatsApp'] as $p)
                                        <option value="{{ strtolower($p) }}"
                                            @selected(strtolower(old('platform', $item?->platform ?? '')) === strtolower($p))>
                                            {{ $p }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    @elseif($type === 'textarea')
                        <textarea class="form-control" rows="5" name="{{ $name }}"
                                  placeholder="Provide {{ str($name)->headline() }} content…">{{ old($name, $item?->{$name}) }}</textarea>

                    @elseif($type === 'checkbox')
                        <div class="form-check form-switch p-3 border rounded-3 bg-light bg-opacity-50">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="{{ $name }}" value="1"
                                   id="switch-{{ $name }}" @checked(old($name, $item?->{$name}))>
                            <label class="form-check-label text-ink fw-semibold small" for="switch-{{ $name }}">Enabled</label>
                            <small class="text-muted d-block extra-small mt-1">Check to make this entry live on the website.</small>
                        </div>

                    @elseif($type === 'file')
                        <div class="border border-dashed p-3 p-md-4 rounded-3 text-center bg-light bg-opacity-50">
                            <i class="bi bi-cloud-arrow-up text-teal fs-4 d-block mb-2"></i>
                            <input class="form-control" type="file" name="{{ $name }}">
                            <small class="text-muted d-block mt-1 extra-small">Recommended: 1200×800px (JPG/PNG/WEBP)</small>
                            @if($item?->{$name})
                                <div class="mt-2">
                                    <img src="{{ asset($item->{$name}) }}" alt="Preview"
                                         class="img-thumbnail shadow-sm"
                                         style="max-height:100px;object-fit:cover;">
                                </div>
                            @endif
                        </div>

                    @else
                        @if(str_ends_with($name, '_id') && isset($relations[$name]))
                            <select class="form-select" name="{{ $name }}">
                                <option value="">Select {{ str(substr($name,0,-3))->headline() }}…</option>
                                @foreach($relations[$name] as $rel)
                                    <option value="{{ $rel->id }}"
                                        @selected(old($name,$item?->{$name}) == $rel->id)>
                                        {{ $rel->name ?? $rel->title ?? $rel->key ?? $rel->email ?? 'Option #'.$rel->id }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input class="form-control"
                                   type="{{ $type === 'number' ? 'number' : 'text' }}"
                                   step="0.01"
                                   name="{{ $name }}"
                                   value="{{ old($name,$item?->{$name}) }}"
                                   placeholder="Enter {{ str($name)->headline() }}…">
                        @endif
                    @endif
                </div>
            @endforeach
        </div>

        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end mt-4 pt-4 border-top">
            <a href="{{ route('admin.resources.index',$resource) }}" class="btn btn-outline-secondary px-4 py-2 fw-semibold">
                Cancel
            </a>
            <button class="btn btn-teal px-4 py-2 shadow-sm fw-bold">
                <i class="bi bi-check-circle-fill me-2"></i>Save &amp; Publish
            </button>
        </div>
    </form>
</div>

@if($resource === 'social-links')
<script>
(function () {
    const logos = {
        facebook:  'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Facebook_Logo_%282019%29.png/600px-Facebook_Logo_%282019%29.png',
        instagram: 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Instagram_icon.png/600px-Instagram_icon.png',
        twitter:   'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Logo_of_Twitter.svg/600px-Logo_of_Twitter.svg.png',
        x:         'https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/X_logo_2023_%28white_background%29.png/600px-X_logo_2023_%28white_background%29.png',
        youtube:   'https://upload.wikimedia.org/wikipedia/commons/thumb/0/09/YouTube_full-color_icon_%282017%29.svg/600px-YouTube_full-color_icon_%282017%29.svg.png',
        linkedin:  'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/LinkedIn_logo_initials.png/600px-LinkedIn_logo_initials.png',
        pinterest: 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/Pinterest-logo.png/600px-Pinterest-logo.png',
        tiktok:    'https://upload.wikimedia.org/wikipedia/en/thumb/a/a9/TikTok_logo.svg/600px-TikTok_logo.svg.png',
        whatsapp:  'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/600px-WhatsApp.svg.png',
    };

    const select      = document.getElementById('platform-select');
    const logoImg     = document.getElementById('platform-logo-img');
    const placeholder = document.getElementById('platform-logo-placeholder');

    function updateLogo(val) {
        const url = logos[val] || null;
        if (url) {
            logoImg.src = url;
            logoImg.alt = val;
            logoImg.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        } else {
            logoImg.style.display = 'none';
            if (placeholder) placeholder.style.display = 'block';
        }
    }

    if (select) {
        select.addEventListener('change', () => updateLogo(select.value));
        // Run on page load to restore existing value
        updateLogo(select.value);
    }
})();
</script>
@endif

@endsection
