<x-default-layout>

    @section('title')
        Edit Category - {{ $category->name }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('catalog.categories.edit', $category) }}
    @endsection

    {{-- Page Header --}}
    <div class="mb-6 card card-flush mb-xl-9">
        <div class="pb-0 card-body pt-9">
            {{-- Back Button & Title --}}
            <div class="flex-wrap mb-6 d-flex flex-sm-nowrap">
                <a href="{{ route('catalog.categories.show', $category) }}"
                    class="btn btn-sm btn-icon btn-active-color-primary me-3">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </a>

                <div class="flex-grow-1">
                    <div class="flex-wrap mb-2 d-flex justify-content-between align-items-start">
                        <div class="d-flex flex-column">
                            <div class="mb-2 d-flex align-items-center">
                                <span class="text-gray-900 fs-2 fw-bold me-3">Edit Category</span>
                                <span class="badge badge-light-warning fs-7 fw-semibold">
                                    <i class="ki-duotone ki-pencil fs-5 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Editing
                                </span>
                            </div>
                            <div class="flex-wrap mb-4 d-flex fw-semibold fs-6 pe-2">
                                <span class="mb-2 text-gray-500 d-flex align-items-center">
                                    <i class="ki-duotone ki-category fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    Update category: {{ $category->name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Progress Steps --}}
            <div class="overflow-auto d-flex h-55px">
                <ul class="border-transparent nav nav-stretch nav-line-tabs nav-line-tabs-2x fs-5 fw-bold flex-nowrap">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 active" data-bs-toggle="tab" href="#kt_tab_basic">
                            <i class="ki-duotone ki-setting-2 fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Basic Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#kt_tab_media">
                            <i class="ki-duotone ki-picture fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Media
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#kt_tab_seo">
                            <i class="ki-duotone ki-chart-simple fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            SEO
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <form id="editCategoryForm" action="{{ route('catalog.categories.update', $category) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-6 g-xl-9">
            {{-- Main Content --}}
            <div class="col-lg-8">
                <div class="tab-content" id="kt_category_tab_content">
                    {{-- Basic Info Tab --}}
                    <div class="tab-pane fade show active" id="kt_tab_basic" role="tabpanel">
                        {{-- Category Details Card --}}
                        <div class="mb-6 card card-flush">
                            <div class="card-header pt-7">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="text-gray-800 card-label fw-bold">Category Details</span>
                                    <span class="mt-1 text-gray-500 fw-semibold fs-6">Update the basic information for
                                        your category</span>
                                </h3>
                            </div>
                            <div class="pt-6 card-body">
                                {{-- Name --}}
                                <div class="mb-8">
                                    <label class="mb-2 form-label fw-semibold fs-6">
                                        <span class="required">Category Name</span>
                                        <i class="ki-duotone ki-information-5 fs-7 ms-1" data-bs-toggle="tooltip"
                                            title="Enter a unique category name">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </label>
                                    <input type="text" name="name"
                                        class="form-control form-control-solid @error('name') is-invalid @enderror"
                                        placeholder="e.g., Electronics, Fashion, Home & Garden"
                                        value="{{ old('name', $category->name) }}" required />
                                    <div class="form-text text-muted">This will be displayed to customers</div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Slug --}}
                                <div class="mb-8">
                                    <label class="mb-2 form-label fw-semibold fs-6">
                                        URL Slug
                                        <i class="ki-duotone ki-information-5 fs-7 ms-1" data-bs-toggle="tooltip"
                                            title="Auto-generated from name if left blank">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </label>
                                    <input type="text" name="slug"
                                        class="form-control form-control-solid @error('slug') is-invalid @enderror"
                                        placeholder="electronics-gadgets (auto-generated if empty)"
                                        value="{{ old('slug', $category->slug) }}" />
                                    <div class="form-text text-muted">Leave blank to auto-generate from category name
                                    </div>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Description --}}
                                <div class="mb-0">
                                    <label class="mb-2 form-label fw-semibold fs-6">Description</label>
                                    <textarea name="description" class="form-control form-control-solid @error('description') is-invalid @enderror"
                                        rows="5" placeholder="Describe this category and what products it contains...">{{ old('description', $category->description) }}</textarea>
                                    <div class="form-text text-muted">Provide a brief description of this category
                                    </div>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Media Tab --}}
                    <div class="tab-pane fade" id="kt_tab_media" role="tabpanel">
                        <div class="mb-6 card card-flush">
                            <div class="card-header pt-7">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="text-gray-800 card-label fw-bold">Category Image</span>
                                    <span class="mt-1 text-gray-500 fw-semibold fs-6">Upload or update the thumbnail
                                        image</span>
                                </h3>
                            </div>
                            <div class="pt-6 card-body">
                                {{-- Current Image Preview --}}
                                @if ($category->image)
                                    <div class="mb-8 text-center">
                                        <label class="mb-3 form-label fw-semibold fs-6">Current Image</label>
                                        <div
                                            class="p-5 mb-3 border border-gray-300 border-dashed rounded d-inline-block">
                                            <img src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->name }}" class="rounded"
                                                style="max-width: 200px; max-height: 200px;">
                                        </div>
                                    </div>
                                    <div class="mb-8 separator separator-dashed"></div>
                                @endif

                                <div class="text-center">
                                    <label class="mb-3 form-label fw-semibold fs-6">
                                        {{ $category->image ? 'Update Image' : 'Upload Image' }}
                                    </label>

                                    <style>
                                        .image-input-placeholder {
                                            background-image: url('assets/media/svg/files/blank-image.svg');
                                        }

                                        [data-bs-theme="dark"] .image-input-placeholder {
                                            background-image: url('assets/media/svg/files/blank-image-dark.svg');
                                        }
                                    </style>

                                    <div class="image-input image-input-outline {{ $category->image ? '' : 'image-input-empty image-input-placeholder' }} mb-5"
                                        data-kt-image-input="true">
                                        <div class="image-input-wrapper w-200px h-200px"
                                            @if ($category->image) style="background-image: url('{{ asset('storage/' . $category->image) }}')" @endif>
                                        </div>

                                        <label
                                            class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                            title="Change image">
                                            <i class="ki-duotone ki-pencil fs-7">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <input type="file" name="image" accept=".png, .jpg, .jpeg" />
                                            <input type="hidden" name="image_remove" />
                                        </label>

                                        <span
                                            class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                            title="Cancel image">
                                            <i class="ki-duotone ki-cross fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>

                                        <span
                                            class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                            title="Remove image">
                                            <i class="ki-duotone ki-cross fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>

                                    <div class="text-muted fs-7">
                                        <strong>Recommended:</strong> 800x800 pixels or larger<br>
                                        <strong>Formats:</strong> PNG, JPG, JPEG only<br>
                                        <strong>Max size:</strong> 5MB
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SEO Tab --}}
                    <div class="tab-pane fade" id="kt_tab_seo" role="tabpanel">
                        <div class="mb-6 card card-flush">
                            <div class="card-header pt-7">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="text-gray-800 card-label fw-bold">Search Engine Optimization</span>
                                    <span class="mt-1 text-gray-500 fw-semibold fs-6">Improve your category's
                                        visibility in search engines</span>
                                </h3>
                            </div>
                            <div class="pt-6 card-body">
                                {{-- Meta Title --}}
                                <div class="mb-8">
                                    <label class="mb-2 form-label fw-semibold fs-6">
                                        Meta Title
                                        <i class="ki-duotone ki-information-5 fs-7 ms-1" data-bs-toggle="tooltip"
                                            title="Recommended: 50-60 characters">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </label>
                                    <input type="text" name="meta_title" class="form-control form-control-solid"
                                        placeholder="Enter meta title for search engines"
                                        value="{{ old('meta_title', $category->meta_title) }}"
                                        id="meta_title_input" />
                                    <div class="form-text">
                                        <span class="text-muted">Character count:</span>
                                        <span class="fw-bold ms-2"><span id="metaTitleCount"
                                                class="text-primary">{{ strlen($category->meta_title ?? '') }}</span>
                                            / 60</span>
                                    </div>
                                </div>

                                {{-- Meta Description --}}
                                <div class="mb-8">
                                    <label class="mb-2 form-label fw-semibold fs-6">
                                        Meta Description
                                        <i class="ki-duotone ki-information-5 fs-7 ms-1" data-bs-toggle="tooltip"
                                            title="Recommended: 150-160 characters">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </label>
                                    <textarea name="meta_description" class="form-control form-control-solid" rows="4"
                                        placeholder="Write a compelling description for search results..." id="meta_description_input">{{ old('meta_description', $category->meta_description) }}</textarea>
                                    <div class="form-text">
                                        <span class="text-muted">Character count:</span>
                                        <span class="fw-bold ms-2"><span id="metaDescCount"
                                                class="text-primary">{{ strlen($category->meta_description ?? '') }}</span>
                                            / 160</span>
                                    </div>
                                </div>

                                {{-- Meta Keywords --}}
                                <div class="mb-0">
                                    <label class="mb-2 form-label fw-semibold fs-6">
                                        Meta Keywords
                                        <i class="ki-duotone ki-information-5 fs-7 ms-1" data-bs-toggle="tooltip"
                                            title="Separate keywords with commas">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </label>
                                    <input type="text" name="meta_keywords"
                                        class="form-control form-control-solid"
                                        placeholder="electronics, gadgets, technology"
                                        value="{{ old('meta_keywords', $category->meta_keywords) }}" />
                                    <div class="form-text text-muted">Separate keywords with commas. Example:
                                        electronics, gadgets, technology</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="gap-3 mb-6 d-flex justify-content-end">
                    <a href="{{ route('catalog.categories.show', $category) }}"
                        class="btn btn-light btn-active-light-primary">
                        <i class="ki-duotone ki-cross fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="ki-duotone ki-check fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <span class="indicator-label">Update Category</span>
                        <span class="indicator-progress d-none">
                            Saving changes...
                            <span class="align-middle spinner-border spinner-border-sm ms-2"></span>
                        </span>
                    </button>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Settings Card --}}
                <div class="mb-6 card card-flush">
                    <div class="card-header pt-7">
                        <h3 class="card-title">
                            <span class="text-gray-800 card-label fw-bold">Settings</span>
                        </h3>
                    </div>
                    <div class="pt-5 card-body">
                        {{-- Catalog --}}
                        <div class="mb-8">
                            <label class="mb-2 form-label fw-semibold fs-6">
                                <span class="required">Catalog</span>
                            </label>
                            <select class="form-select form-select-solid @error('catalog_id') is-invalid @enderror"
                                name="catalog_id" data-control="select2" data-placeholder="Select a catalog"
                                data-hide-search="true" required>
                                <option value="">Select a catalog</option>
                                @foreach ($catalogs as $catalog)
                                    <option value="{{ $catalog->id }}"
                                        {{ old('catalog_id', $category->catalog_id) == $catalog->id ? 'selected' : '' }}>
                                        {{ $catalog->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">Choose which catalog this category belongs to</div>
                            @error('catalog_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-8">
                            <label class="mb-2 form-label fw-semibold fs-6">
                                <span class="required">Status</span>
                            </label>
                            <select class="form-select form-select-solid" name="is_active" data-control="select2"
                                data-placeholder="Select status" data-hide-search="true" required>
                                <option value="1"
                                    {{ old('is_active', $category->is_active) == 1 ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="0"
                                    {{ old('is_active', $category->is_active) == 0 ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>
                            <div class="form-text text-muted">Active categories are visible to customers</div>
                        </div>

                        {{-- Sort Order --}}
                        <div class="mb-0">
                            <label class="mb-2 form-label fw-semibold fs-6">Sort Order</label>
                            <input type="number" name="sort_order"
                                class="form-control form-control-solid @error('sort_order') is-invalid @enderror"
                                placeholder="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                                min="0" />
                            <div class="form-text text-muted">Lower numbers appear first (0 = highest priority)</div>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Related Product Card --}}
                <div class="mb-6 card card-flush">
                    <div class="card-header pt-7">
                        <h3 class="card-title">
                            <span class="text-gray-800 card-label fw-bold">Related Product</span>
                        </h3>
                    </div>
                    <div class="pt-5 card-body">
                        <div class="mb-0">
                            <label class="mb-2 form-label fw-semibold fs-6">
                                Select Product (Optional)
                                <i class="ki-duotone ki-information-5 fs-7 ms-1" data-bs-toggle="tooltip"
                                    title="Associate this category with a featured product">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </label>
                            <select class="form-select form-select-solid" name="related_product_id"
                                id="related_product_id" data-control="select2" data-placeholder="Choose a product">
                                <option value="">-- None --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        data-img="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : asset('assets/media/svg/files/blank-image.svg') }}"
                                        {{ old('related_product_id', $category->related_product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">Optionally link a featured product to this category</div>
                        </div>
                    </div>
                </div>

                {{-- Change Summary Card --}}
                <div class="card card-flush bg-light-warning">
                    <div class="card-body">
                        <div class="mb-3 d-flex align-items-center">
                            <i class="ki-duotone ki-information-2 fs-2hx text-warning me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <h3 class="m-0 text-gray-800 fw-bold">Important</h3>
                        </div>
                        <div class="mb-3 text-gray-700 fs-7 fw-semibold">
                            Changes made to this category will affect:
                        </div>
                        <ul class="mb-0 text-gray-700 ps-3 fs-7 fw-semibold">
                            <li class="mb-2">All {{ $category->products->count() }} products in this category</li>
                            <li class="mb-2">Category visibility on your website</li>
                            <li class="mb-0">Search engine indexing</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Image input initialization
                const imageInputElement = document.querySelector('[data-kt-image-input="true"]');
                if (imageInputElement) {
                    const imageInput = KTImageInput.getInstance(imageInputElement) || KTImageInput.createInstance(
                        imageInputElement);
                }

                // Character counters
                const metaTitleInput = document.getElementById('meta_title_input');
                const metaTitleCount = document.getElementById('metaTitleCount');

                if (metaTitleInput && metaTitleCount) {
                    metaTitleInput.addEventListener('input', function() {
                        metaTitleCount.textContent = this.value.length;
                        updateCountColor(metaTitleCount, this.value.length, 50, 60);
                    });
                    // Initial color
                    updateCountColor(metaTitleCount, metaTitleInput.value.length, 50, 60);
                }

                const metaDescInput = document.getElementById('meta_description_input');
                const metaDescCount = document.getElementById('metaDescCount');

                if (metaDescInput && metaDescCount) {
                    metaDescInput.addEventListener('input', function() {
                        metaDescCount.textContent = this.value.length;
                        updateCountColor(metaDescCount, this.value.length, 150, 160);
                    });
                    // Initial color
                    updateCountColor(metaDescCount, metaDescInput.value.length, 150, 160);
                }

                function updateCountColor(element, length, min, max) {
                    element.classList.remove('text-danger', 'text-warning', 'text-success', 'text-primary');

                    if (length === 0) {
                        element.classList.add('text-primary');
                    } else if (length < min) {
                        element.classList.add('text-warning');
                    } else if (length >= min && length <= max) {
                        element.classList.add('text-success');
                    } else {
                        element.classList.add('text-danger');
                    }
                }

                // Related product select2 with images
                $('#related_product_id').select2({
                    templateResult: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        var img = $(data.element).data('img');
                        if (img) {
                            return $('<span><img src="' + img +
                                '" class="rounded me-2" style="width:30px;height:30px;object-fit:cover;" />' +
                                data.text + '</span>');
                        }
                        return data.text;
                    },
                    templateSelection: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        var img = $(data.element).data('img');
                        if (img) {
                            return $('<span><img src="' + img +
                                '" class="rounded me-2" style="width:20px;height:20px;object-fit:cover;" />' +
                                data.text + '</span>');
                        }
                        return data.text;
                    },
                    escapeMarkup: function(m) {
                        return m;
                    }
                });

                // Initialize tooltips
                const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltips.forEach(tooltip => {
                    new bootstrap.Tooltip(tooltip);
                });

                // Form submission
                $('#editCategoryForm').on('submit', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const submitBtn = $('#submitBtn');
                    const formData = new FormData(this);

                    // Show loading state
                    submitBtn.find('.indicator-label').addClass('d-none');
                    submitBtn.find('.indicator-progress').removeClass('d-none');
                    submitBtn.prop('disabled', true);

                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            submitBtn.find('.indicator-label').removeClass('d-none');
                            submitBtn.find('.indicator-progress').addClass('d-none');
                            submitBtn.prop('disabled', false);

                            if (response.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message ||
                                        'Category updated successfully',
                                    icon: 'success',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok, got it!',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                }).then(function() {
                                    window.location.href =
                                        "{{ route('catalog.categories.show', $category) }}";
                                });
                            }
                        },
                        error: function(xhr) {
                            submitBtn.find('.indicator-label').removeClass('d-none');
                            submitBtn.find('.indicator-progress').addClass('d-none');
                            submitBtn.prop('disabled', false);

                            let errorMessage = 'An error occurred. Please try again.';

                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                errorMessage = '<ul class="mb-0 text-start">';
                                $.each(errors, function(key, value) {
                                    errorMessage += '<li>' + value[0] + '</li>';
                                });
                                errorMessage += '</ul>';
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'Error!',
                                html: errorMessage,
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush

</x-default-layout>
