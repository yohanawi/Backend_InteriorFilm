<x-default-layout>
    @section('title')
        {{ $wrappingArea->title }} - Wrapping Area Details
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('wrapping-areas.show', $wrappingArea) }}
    @endsection

    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h2>{{ $wrappingArea->title }}</h2>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('wrapping-areas.edit', $wrappingArea) }}" class="btn btn-sm btn-primary me-2">
                    <i class="ki-outline ki-notepad-edit fs-3"></i>
                    Edit
                </a>
                <a href="{{ route('wrapping-areas.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-outline ki-left-square fs-3"></i>
                    Back to List
                </a>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body">
            <!--begin::Basic Information-->
            <div class="mb-10">
                <!-- Section Header with Icon -->
                <div class="mb-6 d-flex align-items-center">
                    <div class="symbol symbol-40px me-3">
                        <div class="symbol-label bg-light-primary">
                            <i class="ki-outline ki-information-5 fs-2 text-primary"></i>
                        </div>
                    </div>
                    <h3 class="mb-0 fw-bolder">Basic Information</h3>
                </div>

                <!-- Information Cards Grid -->
                <div class="row g-4">
                    <!-- Slug Card -->
                    <div class="col-md-6">
                        <div class="border border-gray-300 border-dashed card card-flush h-100">
                            <div class="p-6 card-body">
                                <div class="mb-3 d-flex align-items-center">
                                    <i class="ki-outline ki-tag fs-3 text-primary me-2"></i>
                                    <span class="text-muted fs-7 fw-semibold">SLUG</span>
                                </div>
                                <div class="text-gray-800 fw-bold fs-5">{{ $wrappingArea->slug }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Card -->
                    <div class="col-md-3">
                        <div class="border border-gray-300 border-dashed card card-flush h-100">
                            <div class="p-6 card-body">
                                <div class="mb-3 d-flex align-items-center">
                                    <i class="ki-outline ki-status fs-3 text-primary me-2"></i>
                                    <span class="text-muted fs-7 fw-semibold">STATUS</span>
                                </div>
                                @if ($wrappingArea->is_active)
                                    <div class="d-flex align-items-center">
                                        <span class="px-4 py-2 badge badge-success fs-7 fw-bold">
                                            <i class="text-white ki-outline ki-check-circle fs-6 me-1"></i>Active
                                        </span>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center">
                                        <span class="px-4 py-2 badge badge-danger fs-7 fw-bold">
                                            <i class="text-white ki-outline ki-cross-circle fs-6 me-1"></i>Inactive
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sort Order Card -->
                    <div class="col-md-3">
                        <div class="border border-gray-300 border-dashed card card-flush h-100">
                            <div class="p-6 card-body">
                                <div class="mb-3 d-flex align-items-center">
                                    <i class="ki-outline ki-sort fs-3 text-primary me-2"></i>
                                    <span class="text-muted fs-7 fw-semibold">SORT ORDER</span>
                                </div>
                                <div class="text-gray-800 fw-bold fs-5">{{ $wrappingArea->sort_order }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Title Card -->
                    <div class="col-md-12">
                        <div class="border border-gray-300 border-dashed card card-flush h-100">
                            <div class="p-6 card-body">
                                <div class="mb-3 d-flex align-items-center">
                                    <i class="ki-outline ki-text fs-3 text-primary me-2"></i>
                                    <span class="text-muted fs-7 fw-semibold">META TITLE</span>
                                </div>
                                <div class="text-gray-800 fw-bold fs-6">{{ $wrappingArea->meta_title }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meta Description - Full Width -->
                <div class="mt-4 border border-gray-300 border-dashed card card-flush">
                    <div class="p-6 card-body">
                        <div class="mb-3 d-flex align-items-center">
                            <i class="ki-outline ki-document fs-3 text-primary me-2"></i>
                            <span class="text-muted fs-7 fw-semibold">META DESCRIPTION</span>
                        </div>
                        <div class="text-gray-700 fw-semibold fs-6 lh-lg">{{ $wrappingArea->meta_description }}</div>
                    </div>
                </div>

                <!-- Keywords Section -->
                @if ($wrappingArea->keywords)
                    <div class="mt-4 border border-gray-300 border-dashed card card-flush">
                        <div class="p-6 card-body">
                            <div class="mb-4 d-flex align-items-center">
                                <i class="ki-outline ki-price-tag fs-3 text-primary me-2"></i>
                                <span class="text-muted fs-7 fw-semibold">KEYWORDS</span>
                            </div>
                            <div class="flex-wrap gap-2 d-flex">
                                @foreach ($wrappingArea->keywords as $keyword)
                                    <span class="px-4 py-3 badge badge-lg badge-light-primary fw-bold fs-7">
                                        <i class="ki-outline ki-tag fs-7 me-1"></i>{{ $keyword }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!--begin::Main Section-->
            <div class="mb-10">
                <div class="border-0 shadow-sm card">
                    <div class="card-header bg-gradient-primary"
                        style="background: linear-gradient(135deg, #3E97FF 0%, #1B84FF 100%);">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-3">
                                <div class="bg-white symbol-label bg-opacity-20">
                                    <i class="text-white ki-outline ki-home-2 fs-2"></i>
                                </div>
                            </div>
                            <h3 class="mb-0 text-white fw-bold">Main Section</h3>
                        </div>
                    </div>
                    <div class="p-8 card-body">
                        <div class="row g-6">
                            <div class="col-lg-{{ $wrappingArea->main_image ? '7' : '12' }}">
                                <div class="mb-6">
                                    <label
                                        class="mb-3 d-flex align-items-center text-muted fs-7 fw-bold text-uppercase">
                                        <i class="ki-outline ki-text-bold fs-5 me-2 text-primary"></i>Heading
                                    </label>
                                    <h4 class="mb-0 text-gray-900 fw-bolder">{{ $wrappingArea->main_heading }}</h4>
                                </div>
                                <div>
                                    <label
                                        class="mb-3 d-flex align-items-center text-muted fs-7 fw-bold text-uppercase">
                                        <i class="ki-outline ki-note-2 fs-5 me-2 text-primary"></i>Description
                                    </label>
                                    <p class="mb-0 text-gray-700 fs-6 lh-lg">{{ $wrappingArea->main_description }}</p>
                                </div>
                            </div>
                            @if ($wrappingArea->main_image)
                                <div class="col-lg-5">
                                    <div
                                        class="overflow-hidden border border-gray-300 rounded shadow-sm position-relative">
                                        <div class="ribbon ribbon-triangle ribbon-top-start border-primary">
                                            <div class="ribbon-icon mt-n5 ms-n6">
                                                <i class="text-white ki-outline ki-picture fs-2"></i>
                                            </div>
                                        </div>
                                        <img src="{{ $wrappingArea->main_image }}" alt="Main image" class="w-100"
                                            style="max-height: 300px; object-fit: cover;" />
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!--begin::Why Partner Section-->
            <div class="mb-10">
                <div class="border-0 shadow-sm card">
                    <div class="card-header bg-gradient-success"
                        style="background: linear-gradient(135deg, #50CD89 0%, #3BA668 100%);">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-3">
                                <div class="bg-white symbol-label bg-opacity-20">
                                    <i class="text-white ki-outline ki-people fs-2"></i>
                                </div>
                            </div>
                            <h3 class="mb-0 text-white fw-bold">Why Partner Section</h3>
                        </div>
                    </div>
                    <div class="p-8 card-body">
                        <div class="row g-6">
                            <div class="col-lg-{{ $wrappingArea->why_partner_image ? '7' : '12' }}">
                                <div class="mb-6">
                                    <label
                                        class="mb-3 d-flex align-items-center text-muted fs-7 fw-bold text-uppercase">
                                        <i class="ki-outline ki-text-bold fs-5 me-2 text-success"></i>Heading
                                    </label>
                                    <h4 class="mb-0 text-gray-900 fw-bolder">{{ $wrappingArea->why_partner_heading }}
                                    </h4>
                                </div>
                                <div>
                                    <label
                                        class="mb-3 d-flex align-items-center text-muted fs-7 fw-bold text-uppercase">
                                        <i class="ki-outline ki-note-2 fs-5 me-2 text-success"></i>Description
                                    </label>
                                    <p class="mb-0 text-gray-700 fs-6 lh-lg">
                                        {{ $wrappingArea->why_partner_description }}</p>
                                </div>
                            </div>
                            @if ($wrappingArea->why_partner_image)
                                <div class="col-lg-5">
                                    <div
                                        class="overflow-hidden border border-gray-300 rounded shadow-sm position-relative">
                                        <div class="ribbon ribbon-triangle ribbon-top-start border-success">
                                            <div class="ribbon-icon mt-n5 ms-n6">
                                                <i class="text-white ki-outline ki-picture fs-2"></i>
                                            </div>
                                        </div>
                                        <img src="{{ $wrappingArea->why_partner_image }}" alt="Partner image"
                                            class="w-100" style="max-height: 300px; object-fit: cover;" />
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!--begin::Features-->
            @if ($wrappingArea->features && count($wrappingArea->features) > 0)
                <div class="mb-10">
                    <div class="mb-6 d-flex align-items-center">
                        <div class="symbol symbol-50px me-4">
                            <div class="symbol-label bg-light-warning">
                                <i class="ki-outline ki-star fs-2x text-warning"></i>
                            </div>
                        </div>
                        <h3 class="mb-0 fw-bolder">Features</h3>
                    </div>
                    <div class="row g-4">
                        @foreach ($wrappingArea->features as $index => $feature)
                            <div class="col-md-6">
                                <div class="border border-dashed card border-warning h-100 hover-elevate-up">
                                    <div class="p-6 card-body">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 symbol symbol-45px me-4">
                                                <div class="symbol-label bg-light-warning">
                                                    <span class="fs-3 fw-bold text-warning">{{ $index + 1 }}</span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-3 text-gray-900 fw-bold">{{ $feature['title'] }}</h5>
                                                <p class="mb-0 text-gray-600 fs-6">{{ $feature['description'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!--begin::Guide Section-->
            <div class="mb-10">
                <div class="border-0 shadow-sm card">
                    <div class="card-header bg-gradient-info"
                        style="background: linear-gradient(135deg, #7239EA 0%, #5014D0 100%);">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-3">
                                <div class="bg-white symbol-label bg-opacity-20">
                                    <i class="text-white ki-outline ki-book-open fs-2"></i>
                                </div>
                            </div>
                            <h3 class="mb-0 text-white fw-bold">Installation Guide</h3>
                        </div>
                    </div>
                    <div class="p-8 card-body">
                        <div class="mb-8">
                            <div class="mb-6">
                                <label class="mb-3 d-flex align-items-center text-muted fs-7 fw-bold text-uppercase">
                                    <i class="ki-outline ki-text-bold fs-5 me-2 text-info"></i>Guide Heading
                                </label>
                                <h4 class="mb-0 text-gray-900 fw-bolder">{{ $wrappingArea->guide_heading }}</h4>
                            </div>
                            <div>
                                <label class="mb-3 d-flex align-items-center text-muted fs-7 fw-bold text-uppercase">
                                    <i class="ki-outline ki-note-2 fs-5 me-2 text-info"></i>Guide Description
                                </label>
                                <p class="mb-0 text-gray-700 fs-6 lh-lg">{{ $wrappingArea->guide_description }}</p>
                            </div>
                        </div>

                        @if ($wrappingArea->guide && count($wrappingArea->guide) > 0)
                            <div class="stepper stepper-links" id="kt_stepper">
                                @foreach ($wrappingArea->guide as $index => $item)
                                    <div
                                        class="card mb-5 border border-gray-300 {{ $index === 0 ? 'border-2 border-primary' : '' }}">
                                        <div class="card-body p-7">
                                            <div class="row g-6">
                                                @if (isset($item['image']))
                                                    <div class="col-md-5">
                                                        <div class="overflow-hidden rounded position-relative h-100">
                                                            <img src="{{ $item['image'] }}"
                                                                alt="Guide step {{ $index + 1 }}"
                                                                class="rounded w-100 h-100"
                                                                style="object-fit: cover; min-height: 250px;" />
                                                            <div class="top-0 m-4 position-absolute start-0">
                                                                <span class="badge badge-circle badge-primary"
                                                                    style="width: 45px; height: 45px; line-height: 45px; font-size: 1.25rem;">{{ $index + 1 }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="{{ isset($item['image']) ? 'col-md-7' : 'col-12' }}">
                                                    @if (isset($item['subheading']))
                                                        <div class="mb-3">
                                                            <span
                                                                class="px-4 py-2 badge badge-light-primary fs-7 fw-bold">{{ $item['subheading'] }}</span>
                                                        </div>
                                                    @endif
                                                    @if (!isset($item['image']))
                                                        <div class="mb-4 d-flex align-items-center">
                                                            <span class="badge badge-circle badge-primary me-3"
                                                                style="width: 45px; height: 45px; line-height: 45px; font-size: 1.25rem;">{{ $index + 1 }}</span>
                                                            <h4 class="mb-0 fw-bolder">
                                                                {{ $item['heading'] ?? 'Step ' . ($index + 1) }}</h4>
                                                        </div>
                                                    @else
                                                        <h4 class="mb-4 fw-bolder">
                                                            {{ $item['heading'] ?? 'Step ' . ($index + 1) }}</h4>
                                                    @endif
                                                    <p class="mb-5 text-gray-700 fs-6 lh-lg">
                                                        {{ $item['description'] ?? '' }}</p>
                                                    @if (isset($item['features']) && count($item['features']) > 0)
                                                        <div class="p-5 rounded bg-light-success">
                                                            <ul class="mb-0 list-unstyled">
                                                                @foreach ($item['features'] as $feature)
                                                                    <li class="mb-3 d-flex align-items-start">
                                                                        <i
                                                                            class="flex-shrink-0 ki-outline ki-check-circle fs-2 text-success me-3"></i>
                                                                        <span
                                                                            class="text-gray-800 fw-semibold">{{ $feature['title'] }}</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!--begin::Why Use Section-->
            <div class="mb-10">
                <div class="border-0 shadow-sm card">
                    <div class="card-header bg-gradient-info"
                        style="background: linear-gradient(135deg, #0d6efd 100%);">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-3">
                                <div class="bg-white symbol-label bg-opacity-20">
                                    <i class="text-white ki-outline ki-shield-tick fs-2x"></i>
                                </div>
                            </div>
                            <h3 class="mb-0 text-white fw-bold">Why Use Section</h3>
                        </div>
                    </div>
                    <div class="p-8 card-body">
                        <div class="mb-8">
                            <div class="mb-6">
                                <label class="mb-3 d-flex align-items-center text-muted fs-7 fw-bold text-uppercase">
                                    <i class="ki-outline ki-text-bold fs-5 text-info me-2"></i>Why Use Heading
                                </label>
                                <h4 class="mb-0 text-gray-900 fw-bolder">{{ $wrappingArea->why_use_heading }}</h4>
                            </div>
                            <div>
                                <label class="mb-3 d-flex align-items-center text-muted fs-7 fw-bold text-uppercase">
                                    <i class="ki-outline ki-note-2 fs-5 text-info me-2"></i>Why Use Description
                                </label>
                                <p class="mb-0 text-gray-700 fs-6 lh-lg">{{ $wrappingArea->why_use_description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Why Use Section-->

            <!--begin::Hero Section-->
            <div class="mb-10">
                <div class="border-0 shadow-sm card bg-light-danger">
                    <div class="p-0 card-body">
                        <div class="row g-0">
                            <div class="col-lg-{{ $wrappingArea->hero_image ? '7' : '12' }}">
                                <div class="p-10">
                                    <div class="mb-6 d-flex align-items-center">
                                        <div class="symbol symbol-50px me-4">
                                            <div class="symbol-label bg-danger">
                                                <i class="text-white ki-outline ki-rocket fs-2x"></i>
                                            </div>
                                        </div>
                                        <h3 class="mb-0 fw-bolder">Hero Section</h3>
                                    </div>
                                    <div class="mb-6">
                                        <label
                                            class="mb-3 d-flex align-items-center text-muted fs-7 fw-bold text-uppercase">
                                            <i class="ki-outline ki-message-text fs-5 me-2 text-danger"></i>Hero
                                            Text
                                        </label>
                                        <h2 class="mb-0 text-gray-900 fw-bolder">
                                            {{ $wrappingArea->hero_text }}</h2>
                                    </div>
                                    @if ($wrappingArea->hero_subtext)
                                        <div>
                                            <label
                                                class="mb-3 d-flex align-items-center text-muted fs-7 fw-bold text-uppercase">
                                                <i class="ki-outline ki-message-text-2 fs-5 me-2 text-danger"></i>Hero
                                                Subtext
                                            </label>
                                            <p class="mb-0 text-gray-700 fs-5 lh-lg">
                                                {{ $wrappingArea->hero_subtext }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if ($wrappingArea->hero_image)
                                <div class="col-lg-5">
                                    <img src="{{ $wrappingArea->hero_image }}" alt="Hero image"
                                        class="w-100 h-100 rounded-end"
                                        style="object-fit: cover; min-height: 350px;" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!--begin::Gallery Section-->
            <div class="mb-10">
                <div class="border-0 shadow-sm card">
                    <div class="card-header" style="background: linear-gradient(135deg, #F1416C 0%, #D1355B 100%);">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-3">
                                <div class="bg-white symbol-label bg-opacity-20">
                                    <i class="text-white ki-outline ki-element-1 fs-2x"></i>
                                </div>
                            </div>
                            <h3 class="mb-0 text-white fw-bold">Gallery</h3>
                        </div>
                    </div>
                    <div class="p-8 card-body">
                        <div class="mb-8">
                            <div class="mb-6">
                                <label class="mb-3 d-flex align-items-center text-muted fs-7 fw-bold text-uppercase">
                                    <i class="ki-outline ki-text-bold fs-5 me-2 text-danger"></i>Gallery
                                    Heading
                                </label>
                                <h4 class="mb-0 text-gray-900 fw-bolder">{{ $wrappingArea->gallery_heading }}
                                </h4>
                            </div>
                            <div>
                                <label class="mb-3 d-flex align-items-center text-muted fs-7 fw-bold text-uppercase">
                                    <i class="ki-outline ki-note-2 fs-5 me-2 text-danger"></i>Gallery
                                    Description
                                </label>
                                <p class="mb-0 text-gray-700 fs-6 lh-lg">
                                    {{ $wrappingArea->gallery_description }}</p>
                            </div>
                        </div>

                        @if ($wrappingArea->photos && count($wrappingArea->photos) > 0)
                            <div class="row g-4">
                                @foreach ($wrappingArea->photos as $photo)
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div
                                            class="overflow-hidden border-0 shadow-sm cursor-pointer card hover-elevate-up">
                                            <div class="position-relative">
                                                <img src="{{ $photo['src'] }}"
                                                    alt="{{ $photo['alt'] ?? 'Gallery image' }}" class="w-100"
                                                    style="height: 200px; object-fit: cover;" />
                                                <div
                                                    class="top-0 transition bg-opacity-50 opacity-0 overlay position-absolute start-0 w-100 h-100 bg-dark hover-opacity-100 d-flex align-items-center justify-content-center">
                                                    <i class="text-white ki-outline ki-eye fs-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!--begin::Products-->
            @if ($wrappingArea->products && $wrappingArea->products->count() > 0)
                <div class="mb-10">
                    <div class="border-0 shadow-sm card">
                        <div class="card-header bg-light">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px me-3">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="ki-outline ki-tag fs-2 text-primary"></i>
                                        </div>
                                    </div>
                                    <h3 class="mb-0 fw-bolder">Associated Products</h3>
                                </div>
                                <span class="badge badge-primary badge-lg">{{ $wrappingArea->products->count() }}
                                    Products</span>
                            </div>
                        </div>
                        <div class="p-8 card-body">
                            <div class="row g-5">
                                @foreach ($wrappingArea->products as $product)
                                    <div class="col-xl-3 col-lg-4 col-md-6">
                                        <div class="border border-gray-300 border-dashed card h-100 hover-elevate-up">
                                            @if ($product->thumbnail)
                                                <div class="overflow-hidden card-img-top position-relative"
                                                    style="height: 180px;">
                                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}"
                                                        class="w-100 h-100" style="object-fit: cover;" />
                                                    <div class="top-0 m-3 position-absolute end-0">
                                                        <span class="badge badge-light-success">Product</span>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="p-5 card-body">
                                                <h6 class="mb-2 text-gray-900 card-title fw-bold">
                                                    {{ $product->name }}
                                                </h6>
                                                @if ($product->sku)
                                                    <div class="d-flex align-items-center">
                                                        <i class="ki-outline ki-barcode fs-6 text-muted me-2"></i>
                                                        <span class="text-muted fs-7">{{ $product->sku }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!--begin::Metadata-->
            <div class="mb-10">
                <div class="border-0 shadow-sm card bg-light">
                    <div class="p-8 card-body">
                        <div class="row g-5">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-success">
                                            <i class="ki-outline ki-calendar-add fs-4 text-success"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-2 text-muted fs-7 fw-bold text-uppercase d-block">Created
                                            At</label>
                                        <span
                                            class="text-gray-900 fw-bold fs-5">{{ $wrappingArea->created_at->format('M d, Y') }}</span>
                                        <span
                                            class="text-muted fs-6 ms-2">{{ $wrappingArea->created_at->format('H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-warning">
                                            <i class="ki-outline ki-calendar-edit fs-4 text-warning"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-2 text-muted fs-7 fw-bold text-uppercase d-block">Updated
                                            At</label>
                                        <span
                                            class="text-gray-900 fw-bold fs-5">{{ $wrappingArea->updated_at->format('M d, Y') }}</span>
                                        <span
                                            class="text-muted fs-6 ms-2">{{ $wrappingArea->updated_at->format('H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .hover-elevate-up {
                    transition: all 0.3s ease;
                }

                .hover-elevate-up:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
                }

                .hover-opacity-100:hover .overlay {
                    opacity: 1 !important;
                }

                .transition {
                    transition: all 0.3s ease;
                }
            </style>
        </div>
</x-default-layout>
