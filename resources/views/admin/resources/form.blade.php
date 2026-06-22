@extends('layouts.admin')
@section('title', $config['title'])
@section('content')

@php
$currentPlatform = strtolower(old('platform', $item?->platform ?? ''));
// Platform → Bootstrap icon + brand colour
$platformMeta = [
    'facebook'  => ['icon' => 'bi-facebook',  'color' => '#1877f2'],
    'instagram' => ['icon' => 'bi-instagram', 'color' => '#e1306c'],
    'twitter'   => ['icon' => 'bi-twitter-x', 'color' => '#000000'],
    'x'         => ['icon' => 'bi-twitter-x', 'color' => '#000000'],
    'youtube'   => ['icon' => 'bi-youtube',   'color' => '#ff0000'],
    'linkedin'  => ['icon' => 'bi-linkedin',  'color' => '#0a66c2'],
    'pinterest' => ['icon' => 'bi-pinterest', 'color' => '#e60023'],
    'tiktok'    => ['icon' => 'bi-tiktok',    'color' => '#010101'],
    'whatsapp'  => ['icon' => 'bi-whatsapp',  'color' => '#25d366'],
];
$currentMeta = $platformMeta[$currentPlatform] ?? ['icon' => 'bi-share', 'color' => '#6c757d'];
@endphp

<div class="mb-3">
    <a href="{{ route('admin.resources.index', $resource) }}" class="btn btn-sm btn-link text-teal text-decoration-none fw-semibold p-0">
        <i class="bi bi-arrow-left me-1"></i> Back to {{ $config['title'] }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="form-card-header d-flex align-items-start align-items-sm-center justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="fw-bold font-serif mb-1 text-white">
                <i class="bi {{ $item ? 'bi-pencil-square' : 'bi-plus-circle-fill' }} me-2 opacity-75"></i>
                {{ $item ? 'Modify Existing Entry' : 'Create New Entry' }}
            </h5>
            <small class="text-white opacity-70">All required fields will be validated on save.</small>
        </div>
        <span class="badge px-3 py-2 text-uppercase tracking-wider extra-small fw-bold">
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

                {{-- ── social-links: platform ───────────────────────────── --}}
                @if($resource === 'social-links' && $name === 'platform')
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold small text-ink mb-2">
                            <i class="bi bi-share text-teal me-1"></i> Platform
                        </label>
                        <div class="d-flex align-items-center gap-3">
                            {{-- live icon preview circle --}}
                            <div id="platform-icon-box"
                                 class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-3"
                                 style="width:52px;height:52px;background:{{ $currentMeta['color'] }};transition:background 0.2s;">
                                <i id="platform-icon-el"
                                   class="bi {{ $currentMeta['icon'] }} text-white"
                                   style="font-size:1.4rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <select class="form-select" name="platform" id="platform-select" required>
                                    <option value="">Select Platform…</option>
                                    @foreach(['Facebook','Instagram','Twitter','X','YouTube','LinkedIn','Pinterest','TikTok','WhatsApp'] as $p)
                                        <option value="{{ strtolower($p) }}"
                                            @selected($currentPlatform === strtolower($p))>
                                            {{ $p }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                {{-- ── home-sections: section_key readonly ──────────────── --}}
                @elseif($resource === 'home-sections' && $name === 'section_key')
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold small text-ink mb-2">
                            <i class="bi bi-key text-teal me-1"></i> Section Key
                        </label>
                        <input class="form-control bg-light text-muted"
                               type="text" name="section_key"
                               value="{{ old('section_key', $item?->section_key ?? 'about_preview') }}"
                               readonly style="cursor:not-allowed;">
                        <small class="text-muted extra-small d-block mt-1">
                            <i class="bi bi-lock-fill me-1"></i>Section key is fixed and cannot be changed.
                        </small>
                    </div>

                {{-- ── settings file override ───────────────────────────── --}}
                @elseif($resource === 'settings' && $item && $item->type === 'file' && $name === 'value')
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold small text-ink mb-2">
                            <i class="bi bi-image text-teal me-1"></i> Value (File)
                        </label>
                        <input class="form-control" type="file" name="value">
                        @if($item->value)
                            <div class="mt-2 p-2 bg-light rounded-3 d-inline-block">
                                <small class="text-muted d-block mb-1"><i class="bi bi-eye-fill me-1"></i>Current:</small>
                                <img src="{{ asset($item->value) }}" alt="Preview" class="img-thumbnail" style="max-height:70px;">
                            </div>
                        @endif
                    </div>

                {{-- ── generic textarea ─────────────────────────────────── --}}
                @elseif($type === 'textarea')
                    <div class="col-12">
                        <label class="form-label fw-bold small text-ink mb-2">
                            <i class="bi bi-text-paragraph text-teal me-1"></i>
                            {{ str($name)->headline() }}
                        </label>
                        <textarea class="form-control" rows="5" name="{{ $name }}"
                                  placeholder="Provide {{ str($name)->headline() }} content…">{{ old($name, $item?->{$name}) }}</textarea>
                    </div>

                {{-- ── generic checkbox ─────────────────────────────────── --}}
                @elseif($type === 'checkbox')
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold small text-ink mb-2">
                            <i class="bi bi-toggle-on text-teal me-1"></i>
                            {{ str($name)->headline() }}
                        </label>
                        <div class="form-check form-switch p-3 border rounded-3 bg-light bg-opacity-50">
                            <input class="form-check-input ms-0 me-2" type="checkbox"
                                   name="{{ $name }}" value="1"
                                   id="switch-{{ $name }}" @checked(old($name, $item?->{$name}))>
                            <label class="form-check-label text-ink fw-semibold small"
                                   for="switch-{{ $name }}">Enabled</label>
                            <small class="text-muted d-block extra-small mt-1">Check to make this entry live on the website.</small>
                        </div>
                    </div>

                {{-- ── generic file upload ──────────────────────────────── --}}
                @elseif($type === 'file')
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold small text-ink mb-2">
                            <i class="bi bi-image text-teal me-1"></i>
                            {{ str($name)->headline() }}
                        </label>
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
                    </div>

                {{-- ── generic select / text input ──────────────────────── --}}
                @else
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold small text-ink mb-2">
                            <i class="bi {{ $type === 'number' ? 'bi-hash' : 'bi-input-cursor-text' }} text-teal me-1"></i>
                            {{ str($name)->headline() }}
                        </label>
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
                                   step="0.01" name="{{ $name }}"
                                   value="{{ old($name,$item?->{$name}) }}"
                                   placeholder="Enter {{ str($name)->headline() }}…">
                        @endif
                    </div>
                @endif

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
    const meta = {
        facebook:  { icon: 'bi-facebook',  color: '#1877f2' },
        instagram: { icon: 'bi-instagram', color: '#e1306c' },
        twitter:   { icon: 'bi-twitter-x', color: '#000000' },
        x:         { icon: 'bi-twitter-x', color: '#000000' },
        youtube:   { icon: 'bi-youtube',   color: '#ff0000' },
        linkedin:  { icon: 'bi-linkedin',  color: '#0a66c2' },
        pinterest: { icon: 'bi-pinterest', color: '#e60023' },
        tiktok:    { icon: 'bi-tiktok',    color: '#010101' },
        whatsapp:  { icon: 'bi-whatsapp',  color: '#25d366' },
    };

    const select  = document.getElementById('platform-select');
    const iconBox = document.getElementById('platform-icon-box');
    const iconEl  = document.getElementById('platform-icon-el');

    function syncPlatform(val) {
        const m = meta[val] || { icon: 'bi-share', color: '#6c757d' };
        if (iconBox) iconBox.style.background = m.color;
        if (iconEl)  iconEl.className = 'bi ' + m.icon + ' text-white';
    }

    if (select) {
        select.addEventListener('change', () => syncPlatform(select.value));
        syncPlatform(select.value);
    }
})();
</script>
@endif

@endsection
