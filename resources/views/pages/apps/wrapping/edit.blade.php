<x-default-layout>
    @section('title')
        Edit Wrapping Area - {{ $wrappingArea->title }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('wrapping-areas.edit', $wrappingArea) }}
    @endsection

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="mb-6 border-0 shadow-sm alert alert-danger alert-dismissible fade show d-flex align-items-center"
            role="alert">
            <div class="symbol symbol-45px me-4">
                <div class="symbol-label bg-danger">
                    <i class="text-white ki-outline ki-cross-circle fs-2x"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-1 fw-bold">Validation Errors</h5>
                <ul class="mb-0 ps-4">
                    @foreach ($errors->all() as $error)
                        <li class="text-gray-700">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 border-0 shadow-sm alert alert-danger alert-dismissible fade show d-flex align-items-center"
            role="alert">
            <i class="ki-outline ki-information-2 fs-2x text-danger me-3"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('wrapping-areas.store') }}" method="POST" enctype="multipart/form-data"
        id="kt_wrapping_form">
        @csrf

        <!--begin::Basic Information-->
        <div class="mb-8 border-0 shadow-sm card">
            <div class="border-0 card-header" style="min-height: 70px;">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4 bg-opacity-80" style="background-color: #0d4859">
                        <div class="symbol-label bg-opacity-20">
                            <i class="text-white ki-outline ki-information-5 fs-2x"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="mb-1 fw-bold fs-2 text-dark">Basic Information</h3>
                    </div>
                </div>
            </div>
            <div class="p-10 card-body">
                <div class="mb-8 row g-8">
                    <div class="col-md-6">
                        <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                            <i class="ki-outline ki-text-bold fs-5 text-primary me-2"></i>Title
                            <span class="text-danger ms-1">*</span>
                        </label>
                        <div class="input-group input-group-lg input-group-solid">
                            <span class="border-0 input-group-text">
                                <i class="text-gray-600 ki-outline ki-document fs-3"></i>
                            </span>
                            <input type="text" name="title"
                                class="form-control form-control-lg form-control-solid border-0 @error('title') is-invalid @enderror"
                                placeholder="Enter a descriptive title" value="{{ old('title', $wrappingArea->title) }}"
                                required />
                        </div>
                        @error('title')
                            <div class="mt-2 text-danger d-flex align-items-center">
                                <i class="ki-outline ki-cross-circle fs-5 me-2"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="mt-2 form-text text-muted">
                            <i class="ki-outline ki-information fs-6 me-1"></i>This will be the main heading displayed
                            to users
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                            <i class="ki-outline ki-tag fs-5 text-success me-2"></i>Slug
                        </label>
                        <div class="input-group input-group-lg input-group-solid">
                            <span class="border-0 input-group-text">
                                <i class="text-gray-600 ki-outline ki-link fs-3"></i>
                            </span>
                            <input type="text" name="slug"
                                class="form-control form-control-lg form-control-solid border-0 @error('slug') is-invalid @enderror"
                                placeholder="auto-generated-from-title"
                                value="{{ old('slug', $wrappingArea->slug) }}" />
                        </div>
                        @error('slug')
                            <div class="mt-2 text-danger d-flex align-items-center">
                                <i class="ki-outline ki-cross-circle fs-5 me-2"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="mt-2 form-text text-muted">
                            <i class="ki-outline ki-information fs-6 me-1"></i>Leave blank to auto-generate from title
                        </div>
                    </div>
                </div>

                <div class="mb-8 row g-8">
                    <div class="col-md-6">
                        <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                            <i class="ki-outline ki-seo fs-5 text-warning me-2"></i>Meta Title
                            <span class="text-danger ms-1">*</span>
                        </label>
                        <div class="input-group input-group-lg input-group-solid">
                            <span class="border-0 input-group-text">
                                <i class="text-gray-600 ki-outline ki-search-list fs-3"></i>
                            </span>
                            <input type="text" name="meta_title"
                                class="form-control form-control-lg form-control-solid border-0 @error('meta_title') is-invalid @enderror"
                                placeholder="SEO optimized title"
                                value="{{ old('meta_title', $wrappingArea->meta_title) }}" maxlength="60" required />
                        </div>
                        @error('meta_title')
                            <div class="mt-2 text-danger d-flex align-items-center">
                                <i class="ki-outline ki-cross-circle fs-5 me-2"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="mt-2 form-text text-muted">
                            <i class="ki-outline ki-information fs-6 me-1"></i>Recommended: 50-60 characters
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                            <i class="ki-outline ki-sort fs-5 text-info me-2"></i>Sort Order
                        </label>
                        <div class="input-group input-group-lg input-group-solid">
                            <span class="border-0 input-group-text">
                                <i class="text-gray-600 ki-outline ki-sort-numbers fs-3"></i>
                            </span>
                            <input type="number" name="sort_order"
                                class="border-0 form-control form-control-lg form-control-solid"
                                value="{{ old('sort_order', $wrappingArea->sort_order) }}" min="0"
                                placeholder="0" />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                            <i class="ki-outline ki-toggle-on fs-5 text-success me-2"></i>Status
                        </label>
                        <select name="is_active" class="form-select form-select-lg form-select-solid"
                            data-control="select2">
                            <option value="1"
                                {{ old('is_active', $wrappingArea->is_active) == 1 ? 'selected' : '' }}>Active
                            </option>
                            <option value="0"
                                {{ old('is_active', $wrappingArea->is_active) == 0 ? 'selected' : '' }}>Inactive
                            </option>
                            </option>
                        </select>
                    </div>
                </div>

                <div class="mb-8">
                    <div>
                        <label class="mb-4 text-gray-800 form-label fw-bold fs-6">
                            <i class="ki-outline ki-note-2 fs-4 text-primary me-2"></i>Meta Description
                            <span class="text-danger ms-1">*</span>
                            <span class="badge badge-primary ms-2">SEO Important</span>
                        </label>
                        <textarea name="meta_description"
                            class="form-control form-control-lg form-control-solid @error('meta_description') is-invalid @enderror"
                            rows="4" placeholder="Write a compelling description for search engines (150-160 characters)"
                            maxlength="160" required>{{ old('meta_description', $wrappingArea->meta_description) }}</textarea>
                        @error('meta_description')
                            <div class="mt-2 text-danger d-flex align-items-center">
                                <i class="ki-outline ki-cross-circle fs-5 me-2"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <div class="form-text text-muted">
                                <i class="ki-outline ki-information fs-6 me-1"></i>Appears in search results
                            </div>
                            <span class="badge badge-light-success" id="char-count">0 / 160</span>
                        </div>
                    </div>
                </div>

                <div class="border border-dashed card border-warning bg-light-warning">
                    <div class="card-body p-7">
                        <label class="mb-4 text-gray-800 form-label fw-bold fs-6">
                            <i class="ki-outline ki-price-tag fs-4 text-warning me-2"></i>Keywords
                            <span class="badge badge-warning ms-2">Optional</span>
                        </label>
                        <div class="mb-4 input-group input-group-lg">
                            <span class="bg-white border-0 input-group-text">
                                <i class="text-gray-600 ki-outline ki-tag fs-3"></i>
                            </span>
                            <input type="text" name="keywords_input" id="keywords_input"
                                class="border-0 form-control form-control-lg"
                                placeholder="Type keyword and press Enter" />
                            <button type="button" class="btn btn-warning" onclick="addKeyword()">
                                <i class="ki-outline ki-plus fs-3"></i>Add
                            </button>
                        </div>
                        <div id="keywords_container" class="flex-wrap gap-2 mb-3 d-flex"></div>
                        <div id="keywords_hidden_container"></div>
                        <div class="form-text text-muted">
                            <i class="ki-outline ki-information fs-6 me-1"></i>Press Enter or click Add to create
                            keyword tags
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--begin::Main Section-->
        <div class="mb-8 border-0 shadow-sm card">
            <div class="border-0 card-header" style="min-height: 70px;">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4 bg-opacity-80" style="background-color: #0d4859">
                        <div class="symbol-label bg-opacity-20">
                            <i class="text-white ki-outline ki-home-2 fs-2x"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold fs-2 text-dark">Main Section</h3>
                </div>
            </div>
            <div class="p-10 card-body">
                <div class="row g-8">
                    <div class="col-lg-4">
                        <label class="mb-4 text-gray-700 form-label fw-bold">
                            <i class="ki-outline ki-picture fs-5 text-success me-2"></i>Main Image
                        </label>
                        <div class="text-center border border-gray-300 border-dashed card card-flush">
                            <div class="p-6 card-body">
                                <style>
                                    .image-input-placeholder {
                                        background-image: url('assets/media/svg/files/blank-image.svg');
                                    }

                                    [data-bs-theme="dark"] .image-input-placeholder {
                                        background-image: url('assets/media/svg/files/blank-image-dark.svg');
                                    }
                                </style>
                                <div class="image-input image-input-outline {{ $wrappingArea->main_image ? '' : 'image-input-empty' }} image-input-placeholder"
                                    data-kt-image-input="true">
                                    <div class="image-input-wrapper w-150px h-150px"
                                        @if ($wrappingArea->main_image) style="background-image: url('{{ $wrappingArea->main_image }}')" @endif>
                                    </div>
                                    <label
                                        class="shadow btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-30px h-30px bg-body"
                                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                        title="Change image">
                                        <i class="ki-outline ki-pencil fs-6"></i>
                                        <input type="file" name="main_image" accept="image/*"
                                            onchange="previewImage(event)" />
                                    </label>
                                    <span
                                        class="shadow btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-30px h-30px bg-body"
                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel">
                                        <i class="ki-outline ki-cross fs-3"></i>
                                    </span>
                                    <span
                                        class="shadow btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-30px h-30px bg-body"
                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove">
                                        <i class="ki-outline ki-cross fs-3"></i>
                                    </span>
                                </div>
                                <div class="mt-3 form-text text-muted">
                                    PNG, JPG, JPEG allowed
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="mb-7">
                            <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                                <i class="ki-outline ki-text-bold fs-5 text-success me-2"></i>Main Heading
                                <span class="text-danger ms-1">*</span>
                            </label>
                            <input type="text" name="main_heading"
                                class="form-control form-control-lg form-control-solid @error('main_heading') is-invalid @enderror"
                                placeholder="Enter main section heading"
                                value="{{ old('main_heading', $wrappingArea->main_heading) }}" required />
                            @error('main_heading')
                                <div class="mt-2 text-danger"><i
                                        class="ki-outline ki-cross-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                                <i class="ki-outline ki-note-2 fs-5 text-success me-2"></i>Main Description
                                <span class="text-danger ms-1">*</span>
                            </label>
                            <textarea name="main_description"
                                class="form-control form-control-lg form-control-solid @error('main_description') is-invalid @enderror"
                                rows="6" placeholder="Enter main section description" required>{{ old('main_description', $wrappingArea->main_description) }}</textarea>
                            @error('main_description')
                                <div class="mt-2 text-danger"><i
                                        class="ki-outline ki-cross-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--begin::Why Partner Section-->
        <div class="mb-8 border-0 shadow-sm card">
            <div class="border-0 card-header" style="min-height: 70px;">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4 bg-opacity-80" style="background-color: #0d4859">
                        <div class="symbol-label bg-opacity-20">
                            <i class="text-white ki-outline ki-people fs-2x"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold fs-2 text-dark">Why Partner Section</h3>
                </div>
            </div>
            <div class="p-10 card-body">
                <div class="mb-8 row g-8">
                    <div class="col-lg-4">
                        <label class="mb-4 text-gray-700 form-label fw-bold">
                            <i class="ki-outline ki-picture fs-5 me-2" style="color: #7239EA;"></i>Partner Image
                        </label>
                        <div class="text-center border border-gray-300 border-dashed card card-flush">
                            <div class="p-6 card-body">
                                <style>
                                    .image-input-placeholder {
                                        background-image: url('assets/media/svg/files/blank-image.svg');
                                    }

                                    [data-bs-theme="dark"] .image-input-placeholder {
                                        background-image: url('assets/media/svg/files/blank-image-dark.svg');
                                    }
                                </style>
                                <div class="image-input image-input-outline {{ $wrappingArea->why_partner_image ? '' : 'image-input-empty' }} image-input-placeholder"
                                    data-kt-image-input="true">
                                    <div class="image-input-wrapper w-150px h-150px"
                                        @if ($wrappingArea->why_partner_image) style="background-image: url('{{ $wrappingArea->why_partner_image }}')" @endif>
                                    </div>
                                    <label
                                        class="shadow btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-30px h-30px bg-body"
                                        data-kt-image-input-action="change">
                                        <i class="ki-outline ki-pencil fs-6"></i>
                                        <input type="file" name="why_partner_image" accept="image/*"
                                            onchange="previewImage(event)" />
                                    </label>
                                    <span
                                        class="shadow btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-30px h-30px bg-body"
                                        data-kt-image-input-action="cancel">
                                        <i class="ki-outline ki-cross fs-3"></i>
                                    </span>
                                    <span
                                        class="shadow btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-30px h-30px bg-body"
                                        data-kt-image-input-action="remove">
                                        <i class="ki-outline ki-cross fs-3"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="mb-7">
                            <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                                <i class="ki-outline ki-text-bold fs-5 me-2" style="color: #7239EA;"></i>Why Partner
                                Heading
                                <span class="text-danger ms-1">*</span>
                            </label>
                            <input type="text" name="why_partner_heading"
                                class="form-control form-control-lg form-control-solid"
                                value="{{ old('why_partner_heading', $wrappingArea->why_partner_heading) }}"
                                required />
                        </div>
                        <div>
                            <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                                <i class="ki-outline ki-note-2 fs-5 me-2" style="color: #7239EA;"></i>Why Partner
                                Description
                                <span class="text-danger ms-1">*</span>
                            </label>
                            <textarea name="why_partner_description" class="form-control form-control-lg form-control-solid" rows="6"
                                required>{{ old('why_partner_description', $wrappingArea->why_partner_description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="my-8 separator separator-dashed"></div>

                <div>
                    <label class="mb-5 text-gray-700 form-label fw-bold fs-6">
                        <i class="ki-outline ki-star fs-5 text-warning me-2"></i>Features
                    </label>
                    <div id="features_container" class="mb-5">
                        @php
                            $features = old('features', $wrappingArea->features ?? []);
                        @endphp
                        @foreach ($features as $index => $feature)
                            <div class="mb-4 border border-gray-400 border-dashed card feature-item">
                                <div class="p-6 card-body">
                                    <div class="row g-5">
                                        <div class="col-md-12">
                                            <input type="text" name="features[0][title]"
                                                value="{{ $feature['title'] ?? '' }}"
                                                class="mb-3 form-control form-control-lg"
                                                placeholder="Feature Title" />
                                            <textarea name="features[0][description]" class="form-control form-control-lg" rows="2"
                                                placeholder="Feature Description">{{ $feature['description'] ?? '' }}</textarea>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button"
                                                class="text-black btn btn-sm btn-icon btn-light-danger remove-feature">
                                                <i class="ki-outline ki-trash fs-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-light-primary" id="add_feature">
                        <i class="ki-outline ki-plus fs-3"></i>Add Feature
                    </button>
                </div>
            </div>
        </div>

        <!--begin::Installation Guide-->
        <div class="mb-8 border-0 shadow-sm card">
            <div class="border-0 card-header" style="min-height: 70px;">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4 bg-opacity-80" style="background-color: #0d4859">
                        <div class="symbol-label bg-opacity-20">
                            <i class="text-white ki-outline ki-book-open fs-2x"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold fs-2 text-dark">Installation Guide</h3>
                </div>
            </div>
            <div class="p-10 card-body">
                <div class="mb-8 row g-6">
                    <div class="col-md-6">
                        <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                            <i class="ki-outline ki-text-bold fs-5 text-danger me-2"></i>Guide Heading
                            <span class="text-danger ms-1">*</span>
                        </label>
                        <input type="text" name="guide_heading"
                            class="form-control form-control-lg form-control-solid"
                            value="{{ old('guide_heading', $wrappingArea->guide_heading) }}" required />
                    </div>
                    <div class="col-md-6">
                        <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                            <i class="ki-outline ki-note-2 fs-5 text-danger me-2"></i>Guide Description
                            <span class="text-danger ms-1">*</span>
                        </label>
                        <input type="text" name="guide_description"
                            class="form-control form-control-lg form-control-solid"
                            value="{{ old('guide_description', $wrappingArea->guide_description) }}" required />
                    </div>
                </div>

                <div class="my-8 separator separator-dashed"></div>

                <label class="mb-5 text-gray-700 form-label fw-bold fs-6">
                    <i class="ki-outline ki-element-11 fs-5 text-danger me-2"></i>Guide Steps
                </label>
                <div id="guide_container" class="mb-5"></div>
                <button type="button" class="btn btn-light-danger" id="add_guide">
                    <i class="ki-outline ki-plus fs-3"></i>Add Guide Step
                </button>
            </div>
        </div>

        <!--begin::Why Use Section-->
        <div class="mb-8 border-0 shadow-sm card">
            <div class="border-0 card-header" style="min-height: 70px;">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4 bg-opacity-80" style="background-color: #0d4859">
                        <div class="symbol-label bg-opacity-20">
                            <i class="text-white ki-outline ki-shield-tick fs-2x"></i>
                        </div>
                    </div>
                    <h3 class="mb-0 text-gray-800 fw-bold fs-2">Why Use Section</h3>
                </div>
            </div>
            <div class="p-10 card-body">
                <div class="mb-7">
                    <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                        <i class="ki-outline ki-text-bold fs-5 text-info me-2"></i>Why Use Heading
                        <span class="text-danger ms-1">*</span>
                    </label>
                    <input type="text" name="why_use_heading"
                        class="form-control form-control-lg form-control-solid"
                        value="{{ old('why_use_heading', $wrappingArea->why_use_heading) }}" required />
                </div>
                <div>
                    <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                        <i class="ki-outline ki-note-2 fs-5 text-info me-2"></i>Why Use Description
                        <span class="text-danger ms-1">*</span>
                    </label>
                    <textarea name="why_use_description" class="form-control form-control-lg form-control-solid" rows="4" required>{{ old('why_use_description', $wrappingArea->why_use_description) }}</textarea>
                </div>
            </div>
        </div>

        <!--begin::Hero Section-->
        <div class="mb-8 border-0 shadow-sm card">
            <div class="border-0 card-header" style="min-height: 70px;">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4 bg-opacity-80" style="background-color: #0d4859">
                        <div class="symbol-label bg-opacity-20">
                            <i class="text-white ki-outline ki-rocket fs-2x"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold fs-2 text-dark">Hero Section</h3>
                </div>
            </div>
            <div class="p-10 card-body">
                <div class="row g-8">
                    <div class="col-lg-8">
                        <div class="mb-7">
                            <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                                <i class="ki-outline ki-message-text fs-5 text-warning me-2"></i>Hero Text
                                <span class="text-danger ms-1">*</span>
                            </label>
                            <input type="text" name="hero_text"
                                class="form-control form-control-lg form-control-solid"
                                value="{{ old('hero_text', $wrappingArea->hero_text) }}" required />
                        </div>
                        <div>
                            <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                                <i class="ki-outline ki-message-text-2 fs-5 text-warning me-2"></i>Hero Subtext
                            </label>
                            <textarea name="hero_subtext" class="form-control form-control-lg form-control-solid" rows="4">{{ old('hero_subtext', $wrappingArea->hero_subtext) }}</textarea>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <label class="mb-4 text-gray-700 form-label fw-bold">
                            <i class="ki-outline ki-picture fs-5 text-warning me-2"></i>Hero Image
                        </label>
                        <div class="text-center border border-gray-300 border-dashed card card-flush">
                            <div class="p-6 card-body">
                                <style>
                                    .image-input-placeholder {
                                        background-image: url('assets/media/svg/files/blank-image.svg');
                                    }

                                    [data-bs-theme="dark"] .image-input-placeholder {
                                        background-image: url('assets/media/svg/files/blank-image-dark.svg');
                                    }
                                </style>
                                <div class="image-input image-input-outline {{ $wrappingArea->hero_image ? '' : 'image-input-empty' }} image-input-placeholder"
                                    data-kt-image-input="true">
                                    <div class="image-input-wrapper w-150px h-150px"
                                        @if ($wrappingArea->hero_image) style="background-image: url('{{ $wrappingArea->hero_image }}')" @endif>
                                    </div>
                                    <label
                                        class="shadow btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-30px h-30px bg-body"
                                        data-kt-image-input-action="change">
                                        <i class="ki-outline ki-pencil fs-6"></i>
                                        <input type="file" name="hero_image" accept="image/*"
                                            onchange="previewImage(event)" />
                                    </label>
                                    <span
                                        class="shadow btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-30px h-30px bg-body"
                                        data-kt-image-input-action="cancel">
                                        <i class="ki-outline ki-cross fs-3"></i>
                                    </span>
                                    <span
                                        class="shadow btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-30px h-30px bg-body"
                                        data-kt-image-input-action="remove">
                                        <i class="ki-outline ki-cross fs-3"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--begin::Related Products-->
        <div class="mb-8 border-0 shadow-sm card">
            <div class="border-0 card-header" style="min-height: 70px;">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4 bg-opacity-80" style="background-color: #0d4859">
                        <div class="symbol-label bg-opacity-20">
                            <i class="text-white ki-outline ki-tag fs-2x"></i>
                        </div>
                    </div>
                    <h3 class="mb-0 text-gray-800 fw-bold fs-2">Related Products</h3>
                </div>
            </div>
            <div class="p-10 card-body">
                <label class="mb-4 text-gray-700 form-label fw-bold fs-6">
                    <i class="ki-outline ki-element-11 fs-5 text-primary me-2"></i>Select Products
                </label>
                <select name="products[]" class="form-select form-select-lg form-select-solid" data-control="select2"
                    data-placeholder="Choose products to display" multiple>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}"
                            {{ in_array($product->id, old('products', [])) ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                <div class="mt-3 form-text text-muted">
                    <i class="ki-outline ki-information fs-6 me-1"></i>Products will display in the hero section
                </div>
            </div>
        </div>

        <!--begin::Gallery Section-->
        <div class="mb-8 border-0 shadow-sm card">
            <div class="border-0 card-header" style="min-height: 70px;">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4 bg-opacity-80" style="background-color: #0d4859">
                        <div class="symbol-label bg-opacity-20">
                            <i class="text-white ki-outline ki-element-1 fs-2x"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold fs-2 text-dark">Gallery Section</h3>
                </div>
            </div>
            <div class="p-10 card-body">
                <div class="mb-8 row g-6">
                    <div class="col-md-6">
                        <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                            <i class="ki-outline ki-text-bold fs-5 text-info me-2"></i>Gallery Heading
                            <span class="text-danger ms-1">*</span>
                        </label>
                        <input type="text" name="gallery_heading"
                            class="form-control form-control-lg form-control-solid"
                            value="{{ old('gallery_heading') }}" required />
                    </div>
                    <div class="col-md-6">
                        <label class="mb-3 text-gray-700 form-label fw-bold fs-6">
                            <i class="ki-outline ki-note-2 fs-5 text-info me-2"></i>Gallery Description
                            <span class="text-danger ms-1">*</span>
                        </label>
                        <input type="text" name="gallery_description"
                            class="form-control form-control-lg form-control-solid"
                            value="{{ old('gallery_description') }}" required />
                    </div>
                </div>

                <div class="my-8 separator separator-dashed"></div>

                <label class="mb-5 text-gray-700 form-label fw-bold fs-6">
                    <i class="ki-outline ki-picture fs-5 text-info me-2"></i>Gallery Photos
                </label>
                <div id="photos_container" class="mb-5"></div>
                <button type="button" class="btn btn-light-info" id="add_photo">
                    <i class="ki-outline ki-plus fs-3"></i>Add Photo
                </button>
            </div>
        </div>

        <!--begin::Actions-->
        <div class="mb-8 border-0 shadow-sm card">
            <div class="p-8 card-body d-flex justify-content-between align-items-center">
                <a href="{{ route('wrapping-areas.index') }}" class="btn btn-lg btn-light-danger">
                    <i class="ki-outline ki-cross fs-3 me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-lg btn-primary">
                    <i class="ki-outline ki-check fs-3 me-2"></i>
                    <span class="indicator-label">Create Wrapping Area</span>
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            // Character counter
            document.querySelector('textarea[name="meta_description"]')?.addEventListener('input', function() {
                const charCount = this.value.length;
                const badge = document.getElementById('char-count');
                if (badge) {
                    badge.textContent = `${charCount} / 160`;
                    badge.className = charCount > 160 ? 'badge badge-danger' :
                        charCount > 140 ? 'badge badge-warning' :
                        'badge badge-success';
                }
            });

            // Keywords management
            let keywords = @json(old('keywords', []));

            function addKeyword() {
                const input = document.getElementById('keywords_input');
                const keyword = input.value.trim();
                if (keyword && !keywords.includes(keyword)) {
                    keywords.push(keyword);
                    renderKeywords();
                    input.value = '';
                }
            }

            function removeKeyword(keyword) {
                keywords = keywords.filter(k => k !== keyword);
                renderKeywords();
            }

            function renderKeywords() {
                const container = document.getElementById('keywords_container');
                const hiddenContainer = document.getElementById('keywords_hidden_container');
                container.innerHTML = keywords.map(keyword => `
                <span class="px-4 py-3 badge badge-lg badge-light-warning d-inline-flex align-items-center"> 
                    <span class="text-black fw-bold">${keyword}</span>
                    <i class="cursor-pointer ki-outline ki-cross fs-6 ms-2" onclick="removeKeyword('${keyword}')"></i>
                </span>
            `).join('');
                hiddenContainer.innerHTML = keywords.map(keyword =>
                    `<input type="hidden" name="keywords[]" value="${keyword}" />`
                ).join('');
            }

            document.getElementById('keywords_input')?.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addKeyword();
                }
            });

            renderKeywords();

            // Auto-generate slug
            $('input[name="title"]').on('input', function() {
                const slug = $(this).val()
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
                $('input[name="slug"]').val(slug);
            });

            // Features management
            let featureIndex = 1;
            document.getElementById('add_feature')?.addEventListener('click', function() {
                const container = document.getElementById('features_container');
                container.insertAdjacentHTML('beforeend', `
                <div class="mb-4 border border-gray-400 border-dashed card feature-item">
                    <div class="p-6 card-body">
                        <div class="mb-3 d-flex justify-content-between">
                            <h5 class="mb-0"><i class="ki-outline ki-star text-warning me-2"></i>Feature ${featureIndex + 1}</h5>
                            <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-feature">
                                <i class="ki-outline ki-trash fs-4"></i>
                            </button>
                        </div>
                        <input type="text" name="features[${featureIndex}][title]" class="mb-3 form-control form-control-lg" placeholder="Feature Title" />
                        <textarea name="features[${featureIndex}][description]" class="form-control form-control-lg" rows="2" placeholder="Feature Description"></textarea>
                    </div>
                </div>
            `);
                featureIndex++;
            });

            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-feature')) {
                    e.target.closest('.feature-item').remove();
                }
            });

            // Guide management  
            let guideIndex = 0;
            document.getElementById('add_guide')?.addEventListener('click', function() {
                const container = document.getElementById('guide_container');
                container.insertAdjacentHTML('beforeend', `
                <div class="mb-6 border border-2 card border-danger guide-item">
                    <div class="card-body p-7">
                        <div class="mb-5 d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><span class="badge badge-lg badge-circle badge-danger me-3">${guideIndex + 1}</span>Guide Step ${guideIndex + 1}</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-guide">
                                <i class="ki-outline ki-trash fs-4"></i>
                            </button>
                        </div>
                        <div class="row g-6">
                            <div class="col-lg-8">
                                <input type="text" name="guide[${guideIndex}][heading]" class="mb-4 form-control form-control-lg" placeholder="Step Heading" />
                                <input type="text" name="guide[${guideIndex}][subheading]" class="mb-4 form-control form-control-lg" placeholder="Step Subheading (Optional)" />
                                <textarea name="guide[${guideIndex}][description]" class="mb-4 form-control form-control-lg" rows="3" placeholder="Step Description"></textarea>
                                <div class="guide-features-container-${guideIndex} mb-3"></div>
                                <button type="button" class="btn btn-sm btn-light-info add-guide-feature" data-index="${guideIndex}">
                                    <i class="ki-outline ki-plus fs-5"></i>Add Feature
                                </button>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-center border border-gray-300 border-dashed card card-flush">
                                    <div class="p-5 card-body">
                                               <style>
                                                    .image-input-placeholder {
                                                        background-image: url('assets/media/svg/files/blank-image.svg');
                                                    }

                                                    [data-bs-theme="dark"] .image-input-placeholder {
                                                        background-image: url('assets/media/svg/files/blank-image-dark.svg');
                                                    }
                                                </style>
                                        <div class="image-input image-input-empty image-input-outline image-input-placeholder" data-kt-image-input="true">
                                            <div class="image-input-wrapper w-150px h-150px"></div>
                                            <label class="shadow btn btn-icon btn-circle btn-sm btn-color-muted btn-active-color-primary w-25px h-25px bg-body" data-kt-image-input-action="change">
                                                <i class="ki-outline ki-pencil fs-7"></i>
                                                <input type="file" name="guide[${guideIndex}][image]" accept="image/*" onchange="previewImage(event)" />
                                            </label>
                                            <span class="shadow btn btn-icon btn-circle btn-sm btn-color-muted btn-active-color-primary w-25px h-25px bg-body" data-kt-image-input-action="remove">
                                                <i class="ki-outline ki-cross fs-4"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
                guideIndex++;
            });

            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-guide')) {
                    e.target.closest('.guide-item').remove();
                }

                if (e.target.closest('.add-guide-feature')) {
                    const btn = e.target.closest('.add-guide-feature');
                    const idx = btn.dataset.index;
                    const container = document.querySelector(`.guide-features-container-${idx}`);
                    const featCount = container.children.length;
                    container.insertAdjacentHTML('beforeend', `
                    <div class="mb-2 input-group">
                        <span class="input-group-text bg-light-success"><i class="ki-outline ki-check-circle text-success"></i></span>
                        <input type="text" name="guide[${idx}][features][${featCount}][title]" class="form-control" placeholder="Feature point" />
                        <button type="button" class="btn btn-sm btn-light-danger remove-guide-feature">
                            <i class="ki-outline ki-trash fs-5"></i>
                        </button>
                    </div>
                `);
                }

                if (e.target.closest('.remove-guide-feature')) {
                    e.target.closest('.input-group').remove();
                }
            });

            // Photos management
            let photoIndex = 0;
            document.getElementById('add_photo')?.addEventListener('click', function() {
                const container = document.getElementById('photos_container');
                container.insertAdjacentHTML('beforeend', `
                <div class="mb-3 border border-gray-400 border-dashed card photo-item">
                    <div class="p-5 card-body">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-5">
                                <label class="mb-2 form-label fw-semibold">Image File</label>
                                <input type="file" name="photos[${photoIndex}][file]" class="form-control" accept="image/*" />
                            </div>
                            <div class="col-lg-6">
                                <label class="mb-2 form-label fw-semibold">Alt Text</label>
                                <input type="text" name="photos[${photoIndex}][alt]" class="form-control" placeholder="Description for SEO" />
                            </div>
                            <div class="col-lg-1 text-end">
                                <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-photo mt-7">
                                    <i class="ki-outline ki-trash fs-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
                photoIndex++;
            });

            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-photo')) {
                    e.target.closest('.photo-item').remove();
                }
            });

            // Image preview function
            function previewImage(event) {
                const input = event.target;
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const wrapper = input.closest('.image-input').querySelector('.image-input-wrapper');
                        wrapper.style.backgroundImage = `url(${e.target.result})`;
                        input.closest('.image-input').classList.remove('image-input-empty');
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    @endpush

    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .hover-elevate-up {
            transition: all 0.3s ease;
        }

        .hover-elevate-up:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .form-control-solid {
            background-color: #F5F8FA;
            border: 0;
        }

        .form-control-solid:focus {
            background-color: #EEF3F7;
        }

        .input-group-solid .input-group-text {
            background-color: #F5F8FA;
            border: 0;
        }

        .badge-circle {
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</x-default-layout>
