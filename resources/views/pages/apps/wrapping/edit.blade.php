<x-default-layout>
    @section('title', 'Edit Wrapping Area - ' . $wrappingArea->title)
    @section('breadcrumbs')
        {{ Breadcrumbs::render('wrapping-areas.edit', $wrappingArea) }}
    @endsection

    <div class="container-fluid">
        <!-- Alert Messages -->
        @if ($errors->any())
            <div class="mb-4 alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start">
                    <i class="ki-outline ki-information-5 fs-2 me-3"></i>
                    <div class="flex-grow-1">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mt-2 mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ki-outline ki-cross-circle fs-2 me-3"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('wrapping-areas.update', $wrappingArea) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Basic Information Card -->
            <div class="mb-4 shadow-sm card">
                <div class="card-header bg-light">
                    <h3 class="mb-0 card-title">
                        <i class="ki-outline ki-information fs-2 me-2"></i>Basic Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label required">Title</label>
                            <input type="text" name="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $wrappingArea->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug"
                                class="form-control @error('slug') is-invalid @enderror"
                                value="{{ old('slug', $wrappingArea->slug) }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control"
                                value="{{ old('meta_title', $wrappingArea->meta_title) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control"
                                value="{{ old('sort_order', $wrappingArea->sort_order) }}" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select" data-control="select2">
                                <option value="1"
                                    {{ old('is_active', $wrappingArea->is_active) == 1 ? 'selected' : '' }}>Active
                                </option>
                                <option value="0"
                                    {{ old('is_active', $wrappingArea->is_active) == 0 ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label required">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3" required>{{ old('meta_description', $wrappingArea->meta_description) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keywords</label>
                            <input type="text" id="keywords_input" class="form-control"
                                placeholder="Type keyword and press Enter">
                            <div id="keywords_container" class="mt-2"></div>
                            <div id="keywords_hidden_container"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Section Card -->
            <div class="mb-4 shadow-sm card">
                <div class="card-header bg-light">
                    <h3 class="mb-0 card-title">
                        <i class="ki-outline ki-picture fs-2 me-2"></i>Main Section
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">Main Image</label>
                            <div class="py-1 card card-flush">
                                <div class="pt-1 text-center card-body">
                                    <div class="image-input image-input-outline {{ $wrappingArea->main_image ? '' : 'image-input-empty' }}"
                                        data-kt-image-input="true">
                                        <div class="image-input-wrapper w-150px h-150px"
                                            @if ($wrappingArea->main_image) style="background-image: url('{{ $wrappingArea->main_image }}')" @endif>
                                        </div>
                                        <label
                                            class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                            title="Change image">
                                            <i class="ki-outline ki-pencil fs-7"></i>
                                            <input type="file" name="main_image" accept="image/*">
                                            <input type="hidden" name="main_image_remove">
                                        </label>
                                        <span
                                            class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                            title="Cancel image">
                                            <i class="ki-outline ki-cross fs-2"></i>
                                        </span>
                                        <span
                                            class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                            title="Remove image">
                                            <i class="ki-outline ki-cross fs-2"></i>
                                        </span>
                                    </div>
                                    @error('main_image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2 text-muted fs-7">Only *.png, *.jpg and *.jpeg files</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-4">
                                <label class="form-label required">Main Heading</label>
                                <input type="text" name="main_heading" class="form-control"
                                    value="{{ old('main_heading', $wrappingArea->main_heading) }}" required>
                            </div>
                            <div>
                                <label class="form-label required">Main Description</label>
                                <textarea name="main_description" class="form-control" rows="4" required>{{ old('main_description', $wrappingArea->main_description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Why Partner Section Card -->
            <div class="mb-4 shadow-sm card">
                <div class="card-header bg-light">
                    <h3 class="mb-0 card-title">
                        <i class="ki-outline ki-medal-star fs-2 me-2"></i>Why Partner Section
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">Why Partner Image</label>
                            <div class="py-1 card card-flush">
                                <div class="pt-1 text-center card-body">
                                    <div class="image-input image-input-outline {{ $wrappingArea->why_partner_image ? '' : 'image-input-empty' }}"
                                        data-kt-image-input="true">
                                        <div class="image-input-wrapper w-150px h-150px"
                                            @if ($wrappingArea->why_partner_image) style="background-image: url('{{ $wrappingArea->why_partner_image }}')" @endif>
                                        </div>
                                        <label
                                            class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                            title="Change image">
                                            <i class="ki-outline ki-pencil fs-7"></i>
                                            <input type="file" name="why_partner_image" accept="image/*">
                                            <input type="hidden" name="why_partner_image_remove">
                                        </label>
                                        <span
                                            class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                            title="Cancel image">
                                            <i class="ki-outline ki-cross fs-2"></i>
                                        </span>
                                        <span
                                            class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                            title="Remove image">
                                            <i class="ki-outline ki-cross fs-2"></i>
                                        </span>
                                    </div>
                                    @error('why_partner_image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2 text-muted fs-7">Only *.png, *.jpg and *.jpeg files</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-4">
                                <label class="form-label required">Why Partner Heading</label>
                                <input type="text" name="why_partner_heading" class="form-control"
                                    value="{{ old('why_partner_heading', $wrappingArea->why_partner_heading) }}"
                                    required>
                            </div>
                            <div>
                                <label class="form-label required">Why Partner Description</label>
                                <textarea name="why_partner_description" class="form-control" rows="5" required>{{ old('why_partner_description', $wrappingArea->why_partner_description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="mt-6">
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <label class="mb-0 form-label">Features</label>
                            <button type="button" class="btn btn-sm btn-primary" id="add_feature">
                                <i class="ki-outline ki-plus fs-2"></i>Add Feature
                            </button>
                        </div>
                        <div id="features_container">
                            @foreach (old('features', $wrappingArea->features ?? []) as $index => $feature)
                                <div class="mb-3 card card-bordered feature-item">
                                    <div class="p-4 card-body">
                                        <div class="row g-3">
                                            <div class="col-md-11">
                                                <input type="text" name="features[{{ $index }}][title]"
                                                    class="mb-2 form-control" placeholder="Feature Title"
                                                    value="{{ $feature['title'] ?? '' }}">
                                                <textarea name="features[{{ $index }}][description]" class="form-control" rows="2"
                                                    placeholder="Feature Description">{{ $feature['description'] ?? '' }}</textarea>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-light-danger remove-feature">
                                                    <i class="ki-outline ki-trash fs-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Installation Guide Section Card -->
            <div class="mb-4 shadow-sm card">
                <div class="card-header bg-light">
                    <h3 class="mb-0 card-title">
                        <i class="ki-outline ki-book fs-2 me-2"></i>Installation Guide
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-6 row g-4">
                        <div class="col-md-6">
                            <label class="form-label required">Guide Heading</label>
                            <input type="text" name="guide_heading" class="form-control"
                                value="{{ old('guide_heading', $wrappingArea->guide_heading) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label required">Guide Description</label>
                            <textarea name="guide_description" class="form-control" rows="3" required>{{ old('guide_description', $wrappingArea->guide_description) }}</textarea>
                        </div>
                    </div>

                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <label class="mb-0 form-label">Guide Items</label>
                        <button type="button" class="btn btn-sm btn-primary" id="add_guide">
                            <i class="ki-outline ki-plus fs-2"></i>Add Guide Item
                        </button>
                    </div>

                    <div id="guide_container">
                        @foreach (old('guide', $wrappingArea->guide ?? []) as $gIndex => $item)
                            <div class="mb-3 card card-bordered guide-item">
                                <div class="p-4 card-body">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <input type="text" name="guide[{{ $gIndex }}][heading]"
                                                class="mb-2 form-control" placeholder="Guide Heading"
                                                value="{{ $item['heading'] ?? '' }}">
                                            <input type="text" name="guide[{{ $gIndex }}][subheading]"
                                                class="mb-2 form-control" placeholder="Guide Subheading"
                                                value="{{ $item['subheading'] ?? '' }}">
                                            <textarea name="guide[{{ $gIndex }}][description]" class="mb-2 form-control" rows="2"
                                                placeholder="Guide Description">{{ $item['description'] ?? '' }}</textarea>

                                            <div class="guide-features-container" data-index="{{ $gIndex }}">
                                                @foreach ($item['features'] ?? [] as $fIndex => $feature)
                                                    <div class="mb-2 input-group">
                                                        <input type="text"
                                                            name="guide[{{ $gIndex }}][features][{{ $fIndex }}][title]"
                                                            class="form-control" placeholder="Feature Title"
                                                            value="{{ $feature['title'] ?? '' }}">
                                                        <button type="button"
                                                            class="btn btn-sm btn-light-danger remove-guide-feature">
                                                            <i class="ki-outline ki-trash fs-2"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-sm btn-light-info add-guide-feature"
                                                data-index="{{ $gIndex }}">
                                                <i class="ki-outline ki-plus fs-2"></i>Add Feature
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <div class="image-input image-input-outline {{ !empty($item['image']) ? '' : 'image-input-empty' }}"
                                                    data-kt-image-input="true">
                                                    <div class="image-input-wrapper w-150px h-150px"
                                                        @if (!empty($item['image'])) style="background-image: url('{{ $item['image'] }}')" @endif>
                                                    </div>
                                                    <label
                                                        class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                        title="Change image">
                                                        <i class="ki-outline ki-pencil fs-7"></i>
                                                        <input type="file"
                                                            name="guide[{{ $gIndex }}][image]"
                                                            accept="image/*">
                                                        <input type="hidden"
                                                            name="guide[{{ $gIndex }}][existing_image]"
                                                            value="{{ $item['image'] ?? '' }}">
                                                    </label>
                                                    <span
                                                        class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                        title="Cancel image">
                                                        <i class="ki-outline ki-cross fs-2"></i>
                                                    </span>
                                                    <span
                                                        class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                        title="Remove image">
                                                        <i class="ki-outline ki-cross fs-2"></i>
                                                    </span>
                                                </div>
                                                <div class="mt-2 text-muted fs-7">Only *.png, *.jpg and *.jpeg files
                                                </div>
                                                <button type="button"
                                                    class="mt-3 btn btn-sm btn-light-danger remove-guide w-100">
                                                    <i class="ki-outline ki-trash fs-2"></i>Remove Guide
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Why Use Kointec Section Card -->
            <div class="mb-4 shadow-sm card">
                <div class="card-header bg-light">
                    <h3 class="mb-0 card-title">
                        <i class="ki-outline ki-star fs-2 me-2"></i>Why Use Kointec
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label required">Why Use Heading</label>
                            <input type="text" name="why_use_heading" class="form-control"
                                value="{{ old('why_use_heading', $wrappingArea->why_use_heading) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label required">Why Use Description</label>
                            <textarea name="why_use_description" class="form-control" rows="3" required>{{ old('why_use_description', $wrappingArea->why_use_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Section Card -->
            <div class="mb-4 shadow-sm card">
                <div class="card-header bg-light">
                    <h3 class="mb-0 card-title">
                        <i class="ki-outline ki-rocket fs-2 me-2"></i>Hero Section
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <label class="form-label required">Hero Text</label>
                                <input type="text" name="hero_text" class="form-control"
                                    value="{{ old('hero_text', $wrappingArea->hero_text) }}" required>
                            </div>
                            <div>
                                <label class="form-label">Hero Subtext</label>
                                <textarea name="hero_subtext" class="form-control" rows="2">{{ old('hero_subtext', $wrappingArea->hero_subtext) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hero Image</label>
                            <div class="py-1 card card-flush">
                                <div class="pt-1 text-center card-body">
                                    <div class="image-input image-input-outline {{ $wrappingArea->hero_image ? '' : 'image-input-empty' }}"
                                        data-kt-image-input="true">
                                        <div class="image-input-wrapper w-150px h-150px"
                                            @if ($wrappingArea->hero_image) style="background-image: url('{{ $wrappingArea->hero_image }}')" @endif>
                                        </div>
                                        <label
                                            class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                            title="Change image">
                                            <i class="ki-outline ki-pencil fs-7"></i>
                                            <input type="file" name="hero_image" accept="image/*">
                                            <input type="hidden" name="hero_image_remove">
                                        </label>
                                        <span
                                            class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                            title="Cancel image">
                                            <i class="ki-outline ki-cross fs-2"></i>
                                        </span>
                                        <span
                                            class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                            title="Remove image">
                                            <i class="ki-outline ki-cross fs-2"></i>
                                        </span>
                                    </div>
                                    @error('hero_image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2 text-muted fs-7">Only *.png, *.jpg and *.jpeg files</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products Card -->
            <div class="mb-4 shadow-sm card">
                <div class="card-header bg-light">
                    <h3 class="mb-0 card-title">
                        <i class="ki-outline ki-package fs-2 me-2"></i>Related Products
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Select Products to Display</label>
                        <select id="related_products_select" name="products[]" class="form-select"
                            data-control="select2" data-placeholder="Select products" multiple>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                    data-sku="{{ $product->sku ?? '' }}"
                                    data-thumbnail="{{ $product->thumbnail ?? '' }}"
                                    {{ in_array($product->id, old('products', $wrappingArea->products->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">These products will be displayed in the hero section</div>
                    </div>

                    <div class="mt-4">
                        <h5 class="mb-3">Preview</h5>
                        <div id="related_products_preview" class="row g-3"></div>
                    </div>
                </div>
            </div>

            <!-- Gallery Section Card -->
            <div class="mb-4 shadow-sm card">
                <div class="card-header bg-light">
                    <h3 class="mb-0 card-title">
                        <i class="ki-outline ki-gallery fs-2 me-2"></i>Gallery Section
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-6 row g-4">
                        <div class="col-12">
                            <label class="form-label required">Gallery Heading</label>
                            <input type="text" name="gallery_heading" class="form-control"
                                value="{{ old('gallery_heading', $wrappingArea->gallery_heading) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label required">Gallery Description</label>
                            <textarea name="gallery_description" class="form-control" rows="3" required>{{ old('gallery_description', $wrappingArea->gallery_description) }}</textarea>
                        </div>
                    </div>

                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <label class="mb-0 form-label">Gallery Photos</label>
                        <button type="button" class="btn btn-sm btn-primary" id="add_photo">
                            <i class="ki-outline ki-plus fs-2"></i>Add Photo
                        </button>
                    </div>

                    <div id="photos_container">
                        @foreach (old('photos', $wrappingArea->photos ?? []) as $pIndex => $photo)
                            <div class="mb-2 card card-bordered photo-item">
                                <div class="p-3 card-body">
                                    <div class="row g-3 align-items-center">
                                        @if (isset($photo['src']))
                                            <div class="col-auto">
                                                <img src="{{ $photo['src'] }}" alt="{{ $photo['alt'] ?? '' }}"
                                                    class="rounded"
                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                            </div>
                                        @endif
                                        <div class="col-md-4">
                                            <input type="file" name="photos[{{ $pIndex }}][file]"
                                                class="form-control" accept="image/*">
                                            <input type="hidden" name="photos[{{ $pIndex }}][existing_src]"
                                                value="{{ $photo['src'] ?? '' }}">
                                        </div>
                                        <div class="col-md">
                                            <input type="text" name="photos[{{ $pIndex }}][alt]"
                                                class="form-control" placeholder="Alt text"
                                                value="{{ $photo['alt'] ?? '' }}">
                                        </div>
                                        <div class="col-auto">
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-light-danger remove-photo">
                                                <i class="ki-outline ki-trash fs-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="shadow-sm card">
                <div class="card-body">
                    <div class="gap-3 d-flex justify-content-end">
                        <a href="{{ route('wrapping-areas.index') }}" class="btn btn-light">
                            <i class="ki-outline ki-arrow-left fs-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-outline ki-check fs-2"></i>Update Wrapping Area
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let featureIndex = {{ count($wrappingArea->features ?? []) }};
                let guideIndex = {{ count($wrappingArea->guide ?? []) }};
                let photoIndex = {{ count($wrappingArea->photos ?? []) }};

                // Keywords management (same as create page)
                let keywords = @json(old('keywords', $wrappingArea->keywords ?? []));

                function renderKeywords() {
                    const container = $('#keywords_container');
                    const hiddenContainer = $('#keywords_hidden_container');
                    container.empty();
                    hiddenContainer.empty();
                    keywords.forEach((keyword, index) => {
                        container.append(`
                            <span class="mb-2 badge badge-light-primary me-2">
                                ${keyword}
                                <i class="ki-duotone ki-cross fs-2 ms-2 remove-keyword" data-index="${index}" style="cursor: pointer;">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        `);
                        hiddenContainer.append(`<input type="hidden" name="keywords[]" value="${keyword}" />`);
                    });
                }

                $('#keywords_input').on('keypress', function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        const keyword = $(this).val().trim();
                        if (keyword && !keywords.includes(keyword)) {
                            keywords.push(keyword);
                            renderKeywords();
                            $(this).val('');
                        }
                    }
                });

                $(document).on('click', '.remove-keyword', function() {
                    const index = $(this).data('index');
                    keywords.splice(index, 1);
                    renderKeywords();
                });

                renderKeywords();

                // Dynamic form elements (same as create page)
                $('#add_feature').click(function() {
                    $('#features_container').append(`
                        <div class="p-4 mb-3 border rounded feature-item">
                            <div class="mb-3 row">
                                <div class="col-md-11">
                                    <input type="text" name="features[${featureIndex}][title]" class="mb-2 form-control" placeholder="Feature Title" />
                                    <textarea name="features[${featureIndex}][description]" class="form-control" rows="2" placeholder="Feature Description"></textarea>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="text-black btn btn-sm btn-icon btn-light-danger remove-feature">
                                        <i class="ki-outline ki-trash fs-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `);
                    featureIndex++;
                });

                $(document).on('click', '.remove-feature', function() {
                    $(this).closest('.feature-item').remove();
                });

                $(document).on('change', 'input[type="file"][name^="guide"][name$="[image]"]', function(event) {
                    var input = event.target;
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $(input).closest('.image-input').find('.image-input-wrapper')
                                .css('background-image', 'url(' + e.target.result + ')')
                                .removeClass('image-input-empty');
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                });

                $('#add_guide').click(function() {
                    $('#guide_container').append(`
                        <div class="p-4 mb-3 border rounded guide-item">
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="text" name="guide[${guideIndex}][heading]" class="mb-2 form-control" placeholder="Guide Heading" />
                                    <input type="text" name="guide[${guideIndex}][subheading]" class="mb-2 form-control" placeholder="Guide Subheading" />
                                    <textarea name="guide[${guideIndex}][description]" class="mb-2 form-control" rows="2" placeholder="Guide Description"></textarea>
                                    <div class="guide-features-container" data-index="${guideIndex}">
                                        <input type="text" name="guide[${guideIndex}][features][0][title]" class="mb-2 form-control" placeholder="Feature Title" />
                                    </div>
                                    <button type="button" class="btn btn-sm btn-light-info add-guide-feature" data-index="${guideIndex}">Add Feature</button>
                                </div>
                                <div class="col-md-4 d-flex flex-column align-items-center">
                                    <div class="pt-1 text-center card-body w-100">
                                        <style>
                                            .image-input-placeholder {
                                                background-image: url('assets/media/svg/files/blank-image.svg');
                                            }
                                            [data-bs-theme="dark"] .image-input-placeholder {
                                                background-image: url('assets/media/svg/files/blank-image-dark.svg');
                                            }
                                        </style>
                                        <div class="image-input image-input-empty image-input-outline image-input-placeholder"
                                            data-kt-image-input="true">
                                            <div class="image-input-wrapper w-150px h-150px"></div>
                                            <label
                                                class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body d-flex align-items-center justify-content-center"
                                                data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                title="Change image">
                                                <i class="ki-outline ki-pencil fs-7"></i>
                                                <input type="file" name="guide[${guideIndex}][image]" accept="image/*" />
                                                <input type="hidden" name="guide[${guideIndex}][image_remove]" />
                                            </label>
                                            <span
                                                class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                                data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                title="Cancel image">
                                                <i class="ki-outline ki-cross fs-2"></i>
                                            </span>
                                            <span
                                                class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                                data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                title="Remove image">
                                                <i class="ki-outline ki-cross fs-2"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <button type="button" class="mt-2 text-black btn btn-sm btn-icon btn-light-danger remove-guide float-end">
                                        <i class="ki-outline ki-trash fs-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `);
                    guideIndex++;
                });

                $(document).on('click', '.remove-guide', function() {
                    $(this).closest('.guide-item').remove();
                });

                let guideFeatureIndexes = {};
                $(document).on('click', '.add-guide-feature', function() {
                    const guideIdx = $(this).data('index');
                    const container = $(this).prev('.guide-features-container');
                    const currentCount = container.find('input').length;

                    container.append(`
                        <div class="mb-2 input-group">
                            <input type="text" name="guide[${guideIdx}][features][${currentCount}][title]" class="form-control" placeholder="Feature Title" />
                            <button type="button" class="btn btn-sm btn-light-danger remove-guide-feature">
                                <i class="ki-outline ki-trash fs-2"></i>
                            </button>
                        </div>
                    `);
                });

                $(document).on('click', '.remove-guide-feature', function() {
                    $(this).closest('.input-group').remove();
                });

                $('#add_photo').click(function() {
                    $('#photos_container').append(`
                        <div class="p-3 mb-2 border rounded photo-item">
                            <div class="row">
                                <div class="col-md-7">
                                    <input type="file" name="photos[${photoIndex}][file]" class="form-control" accept="image/*" />
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="photos[${photoIndex}][alt]" class="form-control" placeholder="Alt text" />
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="text-black btn btn-sm btn-icon btn-light-danger remove-photo">
                                        <i class="ki-outline ki-trash fs-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `);
                    photoIndex++;
                });

                $(document).on('click', '.remove-photo', function() {
                    $(this).closest('.photo-item').remove();
                });

                // Related products preview (cards)
                const relatedProductsPlaceholder = @json(asset('assets/media/misc/image.png'));

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }

                function renderRelatedProductsPreview() {
                    const $preview = $('#related_products_preview');
                    const $selected = $('#related_products_select').find('option:selected');

                    $preview.empty();

                    if ($selected.length === 0) {
                        $preview.append(`
                            <div class="col-12">
                                <div class="text-muted">No products selected.</div>
                            </div>
                        `);
                        return;
                    }

                    $selected.each(function() {
                        const $opt = $(this);
                        const id = $opt.val();
                        const name = $opt.data('name') || $opt.text();
                        const sku = $opt.data('sku') || '';
                        const thumbnail = ($opt.data('thumbnail') || '').toString().trim();
                        const src = thumbnail ? thumbnail : relatedProductsPlaceholder;

                        $preview.append(`
                            <div class="col-md-2 col-sm-6 col-6" data-id="${escapeHtml(id)}">
                                <div class="card">
                                    <img src="${escapeHtml(src)}" class="card-img-top" alt="${escapeHtml(name)}" style="height: 100px; object-fit: cover;" onerror="this.src='${escapeHtml(relatedProductsPlaceholder)}'" />
                                    <div class="p-2 text-center card-body">                                      
                                        ${sku ? `<p class="mb-0 text-black card-text">SKU: ${escapeHtml(sku)}</p>` : ''}
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }

                renderRelatedProductsPreview();
                $('#related_products_select').on('change', renderRelatedProductsPreview);
            });
        </script>
    @endpush
</x-default-layout>
