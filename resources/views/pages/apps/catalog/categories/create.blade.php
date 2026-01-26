<x-default-layout>

    @section('title')
        Create New Sub Category
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('catalog.categories.create') }}
    @endsection


    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <h3>Create New Category</h3>
            </div>
        </div>
        <div class="card-body">
            <form id="createCategoryForm" action="{{ route('catalog.categories.store') }}" method="POST"
                enctype="multipart/form-data" class="form d-flex flex-column flex-lg-row">
                @csrf

                <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Thumbnail</h2>
                            </div>
                        </div>

                        <div class="pt-0 text-center card-body">
                            <style>
                                .image-input-placeholder {
                                    background-image: url('assets/media/svg/files/blank-image.svg');
                                }

                                [data-bs-theme="dark"] .image-input-placeholder {
                                    background-image: url('assets/media/svg/files/blank-image-dark.svg');
                                }
                            </style>

                            <div class="mb-3 image-input image-input-empty image-input-outline image-input-placeholder"
                                data-kt-image-input="true">
                                <div class="image-input-wrapper w-150px h-150px"></div>
                                <label
                                    class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body d-flex align-items-center justify-content-center"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                    <i class="ki-outline ki-pencil fs-7"></i>
                                    <input type="file" name="image" accept="image/*"
                                        onchange="previewImage(event)" />
                                    <input type="hidden" name="image_remove" />
                                </label>

                                <span
                                    class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                    <i class="ki-outline ki-cross fs-2"></i>
                                </span>

                                <span
                                    class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                    <i class="ki-outline ki-cross fs-2"></i>
                                </span>
                            </div>

                            <div class="text-muted fs-7">
                                Set the product thumbnail image. Only *.png, *.jpg and *.jpeg image files are accepted
                            </div>
                        </div>
                    </div>

                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Catalog</h2>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <select class="form-select @error('catalog_id') is-invalid @enderror" name="catalog_id"
                                id="catalogId" data-control="select2" required>
                                <option value="">Select a catalog</option>
                                @foreach ($catalogs as $catalog)
                                    <option value="{{ $catalog->id }}"
                                        {{ old('catalog_id', $selectedCatalogId) == $catalog->id ? 'selected' : '' }}>
                                        {{ $catalog->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-7">Set the category catalog.</div>
                            @error('catalog_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Status</h2>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <select class="form-select" name="is_active" id="isActive" data-control="select2" required>
                                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                            <div class="text-muted fs-7">Set the catalog status.</div>
                        </div>
                    </div>

                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Related Product (Optional)</h2>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <label for="related_product_id" class="form-label">Select Product</label>
                            <select class="form-select" name="related_product_id" id="related_product_id"
                                data-control="select2" data-placeholder="Choose a product">
                                <option value="">-- None --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        data-img="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : asset('assets/media/svg/files/blank-image.svg') }}"
                                        {{ old('related_product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-7">Optionally associate this category with a product.</div>
                            <script>
                                $(document).ready(function() {
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
                                });
                            </script>
                        </div>
                    </div>

                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h6>Sort Order</h6>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <input type="number" name="sort_order"
                                class="form-control @error('sort_order') is-invalid @enderror"
                                placeholder="Enter sort order" value="{{ old('sort_order', 0) }}" />
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <div class="mb-1 row">
                        <div class="col-lg-6">
                            <label class="fw-semibold text-muted">Name<span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Enter category name" value="{{ old('name') }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="fw-semibold text-muted">Slug</label>
                            <input type="text" name="slug"
                                class="form-control @error('slug') is-invalid @enderror"
                                placeholder="Enter slug (optional, will be auto-generated)"
                                value="{{ old('slug') }}" />
                            <div class="form-text">Leave blank to auto-generate from name</div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <label class="fw-semibold text-muted">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                                placeholder="Enter catalog description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Meta Options</h2>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <div class="mb-10">
                                <label class="form-label">Meta Tag Title</label>
                                <input type="text" class="mb-2 form-control" id="metaTitle" name="meta_title"
                                    placeholder="Meta tag name" value="{{ old('meta_title') }}" />
                                <div class="text-muted fs-7">Set a meta tag title. Recommended to be simple
                                    and precise keywords.</div>
                            </div>
                            <div class="mb-10">
                                <label class="form-label">Meta Tag Description</label>
                                <textarea id="metaDescriptionEditor" name="meta_description" class="mb-2 form-control" style="min-height: 100px;">{{ old('meta_description') }}</textarea>
                                <div class="text-muted fs-7">Set a meta tag description to the product for
                                    increased SEO ranking.</div>
                            </div>
                            <div>
                                <label class="form-label">Meta Tag Keywords</label>
                                <input id="kt_ecommerce_add_product_meta_keywords" name="meta_keywords"
                                    class="mb-2 form-control" value="{{ old('meta_keywords') }}" />
                                <div class="text-muted fs-7">Set a list of keywords that the product is
                                    related to. Separate the keywords by adding a comma
                                    <code>,</code>between each keyword.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('catalog.categories.index') }}" class="btn btn-light me-3">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="indicator-label">Create Category</span>
                            <span class="indicator-progress">Please wait...
                                <span class="align-middle spinner-border spinner-border-sm ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function() {
                // Image input initialization
                const imageInputElement = document.querySelector('[data-kt-image-input="true"]');
                if (imageInputElement) {
                    const imageInput = KTImageInput.getInstance(imageInputElement) || KTImageInput.createInstance(
                        imageInputElement);
                }

                // AJAX form submission
                $('#createCategoryForm').on('submit', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const submitBtn = $('#submitBtn');
                    const formData = new FormData(this);

                    // Show loading indicator
                    submitBtn.attr('data-kt-indicator', 'on');
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
                            submitBtn.removeAttr('data-kt-indicator');
                            submitBtn.prop('disabled', false);

                            if (response.success) {
                                Swal.fire({
                                    text: response.message,
                                    icon: 'success',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok, got it!',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                }).then(function() {
                                    window.location.href =
                                        "{{ route('catalog.categories.index') }}";
                                });
                            }
                        },
                        error: function(xhr) {
                            submitBtn.removeAttr('data-kt-indicator');
                            submitBtn.prop('disabled', false);

                            let errorMessage = 'An error occurred. Please try again.';

                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                errorMessage = '<ul class="text-start">';
                                $.each(errors, function(key, value) {
                                    errorMessage += '<li>' + value[0] + '</li>';
                                });
                                errorMessage += '</ul>';
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                html: errorMessage,
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush

</x-default-layout>
