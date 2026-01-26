<x-default-layout>

    @section('title')
        Edit {{ $product->name }} Product
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('catalog.products.edit', $product) }}
    @endsection

    @php
        $currentCatalogId = old('catalog_id', $product->catalog_id ?? ($product->category->catalog_id ?? ''));
        $currentDiscountType = old('discount_type', $product->discount_type ?? 'none');
        $fixedDiscountedPriceValue = old('discounted_price');
        if ($fixedDiscountedPriceValue === null && $currentDiscountType === 'fixed') {
            $fixedDiscountedPriceValue = $product->discount_value;
        }
        $tagsValue = old('tags');
        if ($tagsValue === null) {
            $tagsValue = is_array($product->tags ?? null) ? implode(',', $product->tags) : '';
        }

        $spec = $product->specification;
        $certificationTagsValue = old('certification_tags');
        if ($certificationTagsValue === null) {
            $certificationTagsValue =
                $spec && is_array($spec->certifications ?? null) ? implode(',', $spec->certifications) : '';
        }

        $featuresRows = old('features');
        if (!is_array($featuresRows)) {
            $featuresRows = $spec && is_array($spec->features_items ?? null) ? $spec->features_items : [];
        }
        if (count($featuresRows) === 0) {
            $featuresRows = [['name' => '', 'image' => null]];
        }

        $rawVariationRows = old('kt_ecommerce_add_product_options');
        if (!is_array($rawVariationRows)) {
            $rawVariationRows = [];
            $existing = is_array($product->variations ?? null) ? $product->variations : [];
            foreach ($existing as $row) {
                if (is_array($row) && isset($row['type'], $row['value'])) {
                    $rawVariationRows[] = [
                        'product_option' => $row['type'],
                        'product_option_value' => $row['value'],
                    ];
                }
            }
        }
        if (count($rawVariationRows) === 0) {
            $rawVariationRows = [['product_option' => '', 'product_option_value' => '']];
        }

        $statusValue = old('status', $product->status ?? ($product->is_active ? 'published' : 'inactive'));
    @endphp

    <div id="kt_app_content" class="app-content flex-column-fluid">

        <div id="kt_app_content_container" class="app-container container-fluid">
            <form id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row"
                action="{{ route('catalog.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="mb-10 alert alert-danger d-flex align-items-start w-100" role="alert">
                        <i class="ki-outline ki-information-5 fs-2hx me-4"></i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1">Please fix the following:</h4>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

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
                            @php
                                $thumbUrl = $product->thumbnail ? asset('storage/' . $product->thumbnail) : null;
                            @endphp
                            <div class="image-input image-input-outline image-input-placeholder {{ $thumbUrl ? '' : 'image-input-empty' }} mb-3"
                                data-kt-image-input="true">
                                <div class="image-input-wrapper w-150px h-150px"
                                    @if ($thumbUrl) style="background-image:url('{{ $thumbUrl }}')" @endif>
                                </div>
                                <label
                                    class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                    <i class="ki-outline ki-pencil fs-7"></i>
                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg, .webp" />
                                    <input type="hidden" name="avatar_remove" />
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
                        </div>
                    </div>

                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Status</h2>
                            </div>
                            <div class="card-toolbar">
                                <div class="rounded-circle 
                                    @if ($statusValue === 'published') bg-success
                                    @elseif($statusValue === 'draft') bg-warning
                                    @elseif($statusValue === 'scheduled') bg-info
                                    @else bg-secondary @endif
                                    w-15px h-15px"
                                    id="kt_ecommerce_add_product_status"></div>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <select class="mb-2 form-select" name="status" data-control="select2"
                                data-hide-search="true" data-placeholder="Select an option"
                                id="kt_ecommerce_add_product_status_select">
                                <option></option>
                                <option value="published" @selected($statusValue === 'published')>Published</option>
                                <option value="draft" @selected($statusValue === 'draft')>Draft</option>
                                <option value="scheduled" @selected($statusValue === 'scheduled')>Scheduled</option>
                                <option value="inactive" @selected($statusValue === 'inactive')>Inactive</option>
                            </select>
                            <div class="text-muted fs-7">Set the product status.</div>
                            <div class="mt-10 @if ($statusValue !== 'scheduled') d-none @endif"
                                id="statusDateTimeWrapper">
                                <label for="kt_ecommerce_add_product_status_datepicker" class="form-label">Select
                                    publishing date and time</label>
                                <input class="form-control" name="published_at"
                                    id="kt_ecommerce_add_product_status_datepicker" placeholder="Pick date & time"
                                    value="{{ old('published_at', $product->published_at) }}" />
                            </div>
                        </div>
                    </div>
                    @push('scripts')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const statusSelect = document.getElementById('kt_ecommerce_add_product_status_select');
                                const statusDot = document.getElementById('kt_ecommerce_add_product_status');
                                const dateTimeWrapper = document.getElementById('statusDateTimeWrapper');

                                function updateStatusDot() {
                                    statusDot.classList.remove('bg-success', 'bg-warning', 'bg-info', 'bg-secondary');
                                    switch (statusSelect.value) {
                                        case 'published':
                                            statusDot.classList.add('bg-success');
                                            break;
                                        case 'draft':
                                            statusDot.classList.add('bg-warning');
                                            break;
                                        case 'scheduled':
                                            statusDot.classList.add('bg-info');
                                            break;
                                        default:
                                            statusDot.classList.add('bg-secondary');
                                    }
                                    if (dateTimeWrapper) {
                                        dateTimeWrapper.classList.toggle('d-none', statusSelect.value !== 'scheduled');
                                    }
                                }
                                if (statusSelect) {
                                    statusSelect.addEventListener('change', updateStatusDot);
                                    updateStatusDot();
                                }
                            });
                        </script>
                    @endpush

                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Product Details</h2>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <label class="form-label">Category</label>
                            <select class="mb-2 form-select" id="catalogSelect" name="catalog_id" data-control="select2"
                                data-placeholder="Select catalog" data-allow-clear="true">
                                <option></option>
                                @foreach ($catalogs as $catalog)
                                    <option value="{{ $catalog->id }}" @selected($currentCatalogId == $catalog->id)>
                                        {{ $catalog->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-7 mb-7">Add product to a category.</div>
                            <a href={{ route('catalog.catalogs.create') }} class="mb-10 btn btn-light-primary btn-sm">
                                <i class="ki-outline ki-plus fs-2"></i>Create new category</a>

                            <label class="form-label">Sub Categories</label>
                            <select class="mb-2 form-select" id="categorySelect" name="category_id"
                                data-control="select2" data-placeholder="Select category" data-allow-clear="true">
                                <option></option>
                                @foreach ($catalogs as $catalog)
                                    @foreach ($catalog->categories as $category)
                                        <option value="{{ $category->id }}" data-catalog="{{ $catalog->id }}"
                                            @selected(old('category_id', $product->category_id) == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            <div class="text-muted fs-7 mb-7">Add product to a sub category.</div>

                            <a href={{ route('catalog.categories.create') }}
                                class="mb-10 btn btn-light-primary btn-sm">
                                <i class="ki-outline ki-plus fs-2"></i>Create new sub category</a>

                            <label class="form-label d-block">Tags</label>
                            <input id="kt_ecommerce_add_product_tags" name="tags" class="mb-2 form-control"
                                value="{{ $tagsValue }}" />
                            <div class="text-muted fs-7">Add tags to a product.</div>
                            <div class="mt-5">
                                <label class="form-label d-block">Flags</label>
                                <div class="flex-row gap-5 align-item-center d-flex">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="is_popular"
                                            value="1" id="isPopular" @checked(old('is_popular', $product->is_popular)) />
                                        <label class="form-check-label" for="isPopular">Popular</label>
                                    </div>
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="is_featured"
                                            value="1" id="isFeatured" @checked(old('is_featured', $product->is_featured)) />
                                        <label class="form-check-label" for="isFeatured">Featured</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <ul class="border-0 nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x fs-4 fw-semibold mb-n2">
                        <li class="nav-item">
                            <a class="pb-4 nav-link text-active-primary active" data-bs-toggle="tab"
                                href="#kt_ecommerce_add_product_general">General</a>
                        </li>
                        <li class="nav-item">
                            <a class="pb-4 nav-link text-active-primary" data-bs-toggle="tab"
                                href="#kt_ecommerce_add_product_advanced">Advanced</a>
                        </li>
                        <li class="nav-item">
                            <a class="pb-4 nav-link text-active-primary" data-bs-toggle="tab"
                                href="#kt_ecommerce_add_product_specifications">Specifications</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                            role="tab-panel">
                            <div class="d-flex flex-column gap-7 gap-lg-10">
                                <div class="py-4 card card-flush">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>General</h2>
                                        </div>
                                    </div>
                                    <div class="pt-0 card-body">
                                        <div class="row">
                                            <div class="mb-10 col-md-6 fv-row">
                                                <label class="required form-label">Product Name</label>
                                                <input type="text" id="productName" name="name"
                                                    class="mb-2 form-control" placeholder="Product name"
                                                    value="{{ old('name', $product->name) }}" required />
                                                <div class="text-muted fs-7">A product name is required and recommended
                                                    to be unique.</div>
                                            </div>
                                            <div class="mb-10 col-md-6 fv-row">
                                                <label class="required form-label">Slug</label>
                                                <input type="text" id="productSlug" name="slug"
                                                    class="mb-2 form-control" placeholder="product-slug"
                                                    value="{{ old('slug', $product->slug) }}" required />
                                                <div class="text-muted fs-7">A slug is required and will be
                                                    auto-generated from the product name, but you can edit it.</div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="form-label">Description</label>
                                            <textarea id="descriptionEditor" name="description" class="mb-2 form-control" style="min-height: 200px;">{{ old('description', $product->description) }}</textarea>
                                            <div class="text-muted fs-7">Set a description to the product for better
                                                visibility.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="py-4 card card-flush">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>Media</h2>
                                        </div>
                                    </div>
                                    <div class="pt-0 card-body">
                                        <div class="mb-2 fv-row">
                                            {{-- Existing Images --}}
                                            <div id="existingImagesContainer" class="mb-3"
                                                style="display: flex; gap: 10px; flex-wrap: wrap;">
                                                @if (!empty($product->images))
                                                    @foreach ($product->images as $index => $image)
                                                        <div class="position-relative existing-image-wrapper"
                                                            data-image-path="{{ $image }}"
                                                            style="width: 150px; height: 150px;">
                                                            <img src="{{ asset('storage/' . $image) }}"
                                                                class="rounded w-100 h-100"
                                                                style="object-fit: cover; border: 2px solid #e4e6ef;"
                                                                alt="Product Image">
                                                            <button type="button"
                                                                class="btn btn-icon btn-sm btn-light-danger position-absolute top-0 end-0 m-1 remove-existing-image"
                                                                data-image="{{ $image }}"
                                                                title="Remove image">
                                                                <i class="ki-outline ki-cross fs-2"></i>
                                                            </button>
                                                            <div class="position-absolute bottom-0 start-0 m-1">
                                                                <span class="badge badge-light-primary">Existing</span>
                                                            </div>
                                                            <input type="hidden" class="existing-image-input"
                                                                name="existing_images[]" value="{{ $image }}">
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>

                                            {{-- Upload New Images --}}
                                            <div class="dropzone" id="product_media_dropzone"
                                                style="cursor: pointer;">
                                                <div class="dz-message needsclick">
                                                    <i class="ki-outline ki-file-up text-primary fs-3x"></i>
                                                    <div class="ms-4">
                                                        <h3 class="mb-1 text-gray-900 fs-5 fw-bold">Drop files here or
                                                            click to upload</h3>
                                                        <span class="text-gray-500 fs-7 fw-semibold">Upload up to 10
                                                            images (PNG, JPG, JPEG)</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="file" class="d-none" id="productMediaInput"
                                                name="images[]" accept="image/png,image/jpeg,image/jpg" multiple />

                                            {{-- New Images Preview --}}
                                            <div class="mt-3" id="newImagesPreview"
                                                style="display: flex; gap: 10px; flex-wrap: wrap;"></div>

                                            {{-- Hidden inputs for images to remove --}}
                                            <div id="removedImagesContainer"></div>
                                        </div>
                                        <div class="text-muted fs-7">Set the product media gallery. You can upload new
                                            images or remove existing ones.</div>
                                    </div>
                                </div>

                                <div class="py-4 card card-flush">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>Pricing</h2>
                                        </div>
                                    </div>
                                    <div class="pt-0 card-body">
                                        <div class="mb-10 fv-row">
                                            <label class="required form-label">Base Price</label>
                                            <input type="text" name="price" class="mb-2 form-control"
                                                placeholder="Product price"
                                                value="{{ old('price', $product->price) }}" required />
                                            <div class="text-muted fs-7">Set the product price.</div>
                                        </div>

                                        <div class="mb-10 fv-row">
                                            <label class="mb-2 fs-6 fw-semibold">Discount Type
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Select a discount type that will be applied to this product">
                                                    <i class="text-gray-500 ki-outline ki-information-5 fs-6"></i>
                                                </span>
                                            </label>
                                            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-1 row-cols-xl-3 g-9"
                                                data-kt-buttons="true"
                                                data-kt-buttons-target="[data-kt-button='true']">
                                                <div class="col">
                                                    <label
                                                        class="p-6 btn btn-outline btn-outline-dashed btn-active-light-primary @if (old('discount_type', $currentDiscountType) === 'none') active @endif d-flex text-start"
                                                        data-kt-button="true">
                                                        <span
                                                            class="mt-1 form-check form-check-custom form-check-solid form-check-sm align-items-start">
                                                            <input class="form-check-input" type="radio"
                                                                name="discount_type" value="none"
                                                                @checked(old('discount_type', $currentDiscountType) === 'none') />
                                                        </span>
                                                        <span class="ms-5">
                                                            <span class="text-gray-800 fs-4 fw-bold d-block">
                                                                No Discount
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="col">
                                                    <label
                                                        class="p-6 btn btn-outline btn-outline-dashed btn-active-light-primary @if (old('discount_type', $currentDiscountType) === 'percentage') active @endif d-flex text-start"
                                                        data-kt-button="true">
                                                        <span
                                                            class="mt-1 form-check form-check-custom form-check-solid form-check-sm align-items-start">
                                                            <input class="form-check-input" type="radio"
                                                                name="discount_type" value="percentage"
                                                                @checked(old('discount_type', $currentDiscountType) === 'percentage') />
                                                        </span>
                                                        <span class="ms-5">
                                                            <span class="text-gray-800 fs-4 fw-bold d-block">
                                                                Percentage %
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="col">
                                                    <label
                                                        class="p-6 btn btn-outline btn-outline-dashed btn-active-light-primary @if (old('discount_type', $currentDiscountType) === 'fixed') active @endif d-flex text-start"
                                                        data-kt-button="true">
                                                        <span
                                                            class="mt-1 form-check form-check-custom form-check-solid form-check-sm align-items-start">
                                                            <input class="form-check-input" type="radio"
                                                                name="discount_type" value="fixed"
                                                                @checked(old('discount_type', $currentDiscountType) === 'fixed') />
                                                        </span>
                                                        <span class="ms-5">
                                                            <span class="text-gray-800 fs-4 fw-bold d-block">
                                                                Fixed Price
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-10 fv-row @if (old('discount_type', $currentDiscountType) !== 'percentage') d-none @endif"
                                            id="kt_ecommerce_add_product_discount_percentage">
                                            <label class="form-label">Set Discount Percentage</label>
                                            <div class="mb-5 text-center d-flex flex-column">
                                                <div class="d-flex align-items-start justify-content-center mb-7">
                                                    <span class="fw-bold fs-3x"
                                                        id="kt_ecommerce_add_product_discount_label">
                                                        {{ old('discount_value', $product->discount_value ?? 0) ?: 0 }}
                                                    </span>
                                                    <span class="mt-1 fw-bold fs-4 ms-2">%</span>
                                                </div>
                                                <div id="kt_ecommerce_add_product_discount_slider" class="noUi-sm">
                                                </div>
                                            </div>

                                            <input type="number" name="discount_value" class="mb-2 form-control"
                                                step="0.01" min="0" max="100"
                                                placeholder="Discount %"
                                                value="{{ old('discount_value', $product->discount_value) }}" />

                                            <div class="text-muted fs-7">
                                                Set a percentage discount to be applied on this product.
                                            </div>
                                        </div>

                                        <div class="mb-10 fv-row @if (old('discount_type', $currentDiscountType) !== 'fixed') d-none @endif"
                                            id="kt_ecommerce_add_product_discount_fixed">
                                            <label class="form-label">Fixed Discounted Price</label>
                                            <input type="number" name="discounted_price" class="mb-2 form-control"
                                                step="0.01" min="0" placeholder="Discounted price"
                                                value="{{ $fixedDiscountedPriceValue }}" />
                                            <div class="text-muted fs-7">
                                                Set the discounted product price. The product will be reduced at the
                                                determined fixed price
                                            </div>
                                        </div>

                                        <div class="mt-7 row">
                                            <div class="col-md-6 fv-row">
                                                <label class="form-label">Tax Class</label>
                                                <select name="tax_class_id" class="form-select"
                                                    data-control="select2" data-placeholder="Select tax class">
                                                    <option value="">Select a tax class</option>
                                                    @foreach ($taxClasses ?? [] as $taxClass)
                                                        <option value="{{ $taxClass->id }}"
                                                            @selected(old('tax_class_id', $product->tax_class_id) == $taxClass->id)>
                                                            {{ $taxClass->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="text-muted fs-7">Choose a tax class for this product.</div>
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <label class="form-label">VAT</label>
                                                <input type="number" name="vat" class="form-control"
                                                    step="0.01" min="0" placeholder="VAT amount"
                                                    value="{{ old('vat', $product->vat) }}" />
                                                <div class="text-muted fs-7">Set VAT for this product.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="kt_ecommerce_add_product_advanced" role="tab-panel">
                            <div class="d-flex flex-column gap-7 gap-lg-10">
                                <div class="py-4 card card-flush">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>Inventory</h2>
                                        </div>
                                    </div>
                                    <div class="pt-0 card-body">
                                        <div class="mb-10 row">
                                            <div class="col-md-6 fv-row">
                                                <label class="required form-label">SKU</label>
                                                <input type="text" name="sku" class="mb-2 form-control"
                                                    placeholder="SKU Number"
                                                    value="{{ old('sku', $product->sku) }}" />
                                                <div class="text-muted fs-7">Enter the product SKU.</div>
                                            </div>
                                            <div class="col-md-6 fv-row">
                                                <label class="required form-label">Quantity</label>
                                                <input type="number" name="stock_warehouse"
                                                    class="mb-2 form-control" placeholder="In warehouse"
                                                    value="{{ old('stock_warehouse', $product->stock_warehouse ?? 0) }}"
                                                    min="0" required />
                                                <div class="text-muted fs-7">Enter the product quantity.</div>
                                            </div>
                                        </div>

                                        <div class="fv-row">
                                            <label class="form-label">Allow Backorders</label>
                                            <div class="mb-2 form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox"
                                                    name="allow_backorders" value="1"
                                                    @checked(old('allow_backorders', $product->allow_backorders ?? false)) />
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="text-muted fs-7">Allow customers to purchase products that are
                                                out of stock.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="py-4 card card-flush">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>Variations</h2>
                                        </div>
                                    </div>
                                    <div class="pt-0 card-body">
                                        <div class="" data-kt-ecommerce-catalog-add-product="auto-options">
                                            <label class="form-label">Add Product Variations</label>
                                            <div id="kt_ecommerce_add_product_options">
                                                <div class="form-group">
                                                    <div data-repeater-list="kt_ecommerce_add_product_options"
                                                        class="gap-3 d-flex flex-column">
                                                        @foreach ($rawVariationRows as $index => $row)
                                                            <div data-repeater-item=""
                                                                class="flex-wrap gap-5 form-group d-flex align-items-center">
                                                                <div class="w-100 w-md-200px">
                                                                    <select class="form-select" name="product_option"
                                                                        data-control="select2"
                                                                        data-placeholder="Select a variation"
                                                                        data-kt-ecommerce-catalog-add-product="product_option">
                                                                        <option></option>
                                                                        <option value="color"
                                                                            @selected(($row['product_option'] ?? '') === 'color')>Color</option>
                                                                        <option value="size"
                                                                            @selected(($row['product_option'] ?? '') === 'size')>Size</option>
                                                                        <option value="material"
                                                                            @selected(($row['product_option'] ?? '') === 'material')>Material
                                                                        </option>
                                                                        <option value="style"
                                                                            @selected(($row['product_option'] ?? '') === 'style')>Style</option>
                                                                    </select>
                                                                </div>
                                                                <input type="text"
                                                                    class="form-control mw-100 w-200px"
                                                                    name="product_option_value"
                                                                    placeholder="Variation"
                                                                    value="{{ $row['product_option_value'] ?? '' }}" />
                                                                <button type="button" data-repeater-delete=""
                                                                    class="btn btn-sm btn-icon btn-light-danger">
                                                                    <i class="ki-outline ki-cross fs-1"></i>
                                                                </button>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="mt-5 form-group">
                                                    <button type="button" data-repeater-create=""
                                                        class="btn btn-sm btn-light-primary">
                                                        <i class="ki-outline ki-plus fs-2"></i>
                                                        Add another variation
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="py-4 card card-flush">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>Shipping</h2>
                                        </div>
                                    </div>
                                    <div class="pt-0 card-body">
                                        <div class="fv-row">
                                            <div class="mb-2 form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" name="is_physical"
                                                    id="kt_ecommerce_add_product_shipping_checkbox" value="1"
                                                    @checked(old('is_physical', $product->is_physical ?? false)) />
                                                <label class="form-check-label">This is a physical product</label>
                                            </div>
                                            <div class="text-muted fs-7">Set if the product is a physical or digital
                                                item. Physical products may require shipping.</div>
                                        </div>
                                        <div id="kt_ecommerce_add_product_shipping"
                                            class="mt-10 @if (!old('is_physical', $product->is_physical ?? false)) d-none @endif">
                                            <div class="mb-10 fv-row">
                                                <label class="form-label">Weight</label>
                                                <input type="text" name="weight" class="mb-2 form-control"
                                                    placeholder="Product weight"
                                                    value="{{ old('weight', $product->weight) }}" />
                                                <div class="text-muted fs-7">
                                                    Set a product weight in kilograms (kg).
                                                </div>
                                            </div>

                                            <div class="fv-row">
                                                <label class="form-label">Dimension</label>
                                                <div class="flex-wrap gap-3 d-flex flex-sm-nowrap">
                                                    <input type="number" name="width" class="mb-2 form-control"
                                                        placeholder="Width (w)"
                                                        value="{{ old('width', $product->width) }}" />
                                                    <input type="number" name="height" class="mb-2 form-control"
                                                        placeholder="Height (h)"
                                                        value="{{ old('height', $product->height) }}" />
                                                    <input type="number" name="length" class="mb-2 form-control"
                                                        placeholder="Lengtn (l)"
                                                        value="{{ old('length', $product->length) }}" />
                                                </div>
                                                <div class="text-muted fs-7">
                                                    Enter the product dimensions in centimeters (cm).
                                                </div>
                                            </div>
                                        </div>
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
                                            <input type="text" class="mb-2 form-control" id="metaTitle"
                                                name="meta_title" placeholder="Meta tag name"
                                                value="{{ old('meta_title', $product->meta_title) }}" />
                                            <div class="text-muted fs-7">Set a meta tag title. Recommended to be simple
                                                and precise keywords.</div>
                                        </div>

                                        <div class="mb-10">
                                            <label class="form-label">Meta Tag Description</label>
                                            <textarea id="metaDescriptionEditor" name="meta_description" class="mb-2 form-control" style="min-height: 100px;">{{ old('meta_description', $product->meta_description) }}</textarea>
                                            <div class="text-muted fs-7">Set a meta tag description to the product for
                                                increased SEO ranking.</div>
                                        </div>

                                        <div>
                                            <label class="form-label">Meta Tag Keywords</label>
                                            <input id="kt_ecommerce_add_product_meta_keywords" name="meta_keywords"
                                                class="mb-2 form-control"
                                                value="{{ old('meta_keywords', $product->meta_keywords) }}" />
                                            <div class="text-muted fs-7">Set a list of keywords that the product is
                                                related to. Separate the keywords by adding a comma
                                                <code>,</code>between each keyword.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="kt_ecommerce_add_product_specifications" role="tab-panel">
                            <div class="d-flex flex-column gap-7 gap-lg-10">
                                <div class="py-4 card card-flush">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>Specifications</h2>
                                        </div>
                                    </div>
                                    <div class="pt-0 card-body">
                                        {{-- Certification Tags --}}
                                        <div class="mb-10">
                                            <label class="form-label">Certifications</label>
                                            <input id="certificationInput" name="certifications"
                                                class="mb-2 form-control"
                                                placeholder="Type and press comma, space or enter" />
                                            <div id="certificationTags" class="flex-wrap gap-2 d-flex"></div>
                                            <input type="hidden" name="certification_tags"
                                                id="certificationTagsHidden" value="{{ $certificationTagsValue }}">
                                            <div class="text-muted fs-7">Add certifications. Separate with comma, space
                                                or enter.</div>
                                        </div>

                                        {{-- Product Features (Repeater with Image) --}}
                                        <div class="mb-10">
                                            <label class="form-label">Product Features</label>
                                            <div id="productFeaturesRepeater">
                                                <div data-feature-list class="gap-3 d-flex flex-column">
                                                    @foreach ($featuresRows as $i => $feature)
                                                        @php
                                                            $featureName = old(
                                                                "features.$i.name",
                                                                is_array($feature) ? $feature['name'] ?? '' : '',
                                                            );
                                                            $featureExistingImage = old(
                                                                "features.$i.existing_image",
                                                                is_array($feature)
                                                                    ? $feature['existing_image'] ??
                                                                        ($feature['image'] ?? null)
                                                                    : null,
                                                            );
                                                        @endphp
                                                        <div data-feature-item
                                                            class="gap-3 mb-2 d-flex align-items-center">
                                                            <input type="text" class="form-control"
                                                                name="features[{{ $i }}][name]"
                                                                placeholder="Feature name" style="max-width: 250px;"
                                                                value="{{ $featureName }}">
                                                            <input type="hidden"
                                                                name="features[{{ $i }}][existing_image]"
                                                                value="{{ $featureExistingImage }}">
                                                            <input type="file" class="form-control"
                                                                name="features[{{ $i }}][image]"
                                                                accept="image/*" style="max-width: 200px;">
                                                            {{-- Image preview --}}
                                                            <div class="feature-image-preview"
                                                                style="width: 50px; height: 50px;">
                                                                @if (!empty($featureExistingImage))
                                                                    <img src="{{ asset('storage/' . $featureExistingImage) }}"
                                                                        alt="Feature Image"
                                                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                                                                @endif
                                                            </div>
                                                            <button type="button"
                                                                class="btn btn-sm btn-icon btn-light-danger"
                                                                data-feature-delete>
                                                                <i class="ki-outline ki-cross fs-1"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="mt-2 btn btn-sm btn-light-primary"
                                                    id="addFeatureBtn">
                                                    <i class="ki-outline ki-plus fs-2"></i> Add another feature
                                                </button>
                                            </div>
                                            <div class="text-muted fs-7">Add product features with optional image.
                                            </div>
                                        </div>

                                        {{-- Dimensions --}}
                                        <div class="mb-10">
                                            <label class="form-label">Dimensions</label>
                                            <div class="flex-wrap gap-2 d-flex">
                                                <input type="text" class="form-control" name="spec_dimensions"
                                                    placeholder="e.g. 1220mm x 2440mm"
                                                    value="{{ old('spec_dimensions', $spec?->spec_dimensions) }}">
                                            </div>
                                            <div class="text-muted fs-7">Enter product dimensions.</div>
                                        </div>

                                        {{-- Surface Finish --}}
                                        <div class="mb-10">
                                            <label class="form-label">Surface Finish</label>
                                            <input type="text" class="form-control" name="surface_finish"
                                                placeholder="e.g. Glossy, Matte"
                                                value="{{ old('surface_finish', $spec?->surface_finish) }}">
                                        </div>

                                        {{-- Tensile Strength --}}
                                        <div class="mb-10">
                                            <label class="form-label">Tensile Strength</label>
                                            <input type="text" class="form-control" name="tensile_strength"
                                                placeholder="e.g. 3000 N/25mm"
                                                value="{{ old('tensile_strength', $spec?->tensile_strength) }}">
                                        </div>

                                        {{-- Application Temperature --}}
                                        <div class="mb-10 row">
                                            <div class="col-md-4">
                                                <label class="form-label">Application Temperature</label>
                                                <input type="text" class="form-control"
                                                    name="application_temperature" placeholder="e.g. 10°C - 40°C"
                                                    value="{{ old('application_temperature', $spec?->application_temperature) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Elongation</label>
                                                <input type="text" class="form-control" name="elongation"
                                                    placeholder="e.g. 150%"
                                                    value="{{ old('elongation', $spec?->elongation) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Service Temperature</label>
                                                <input type="text" class="form-control" name="service_temperature"
                                                    placeholder="e.g. -20°C to 80°C"
                                                    value="{{ old('service_temperature', $spec?->service_temperature) }}">
                                            </div>
                                        </div>

                                        {{-- Storage --}}
                                        <div class="mb-10">
                                            <label class="form-label">Storage</label>
                                            <input type="text" class="form-control" name="storage"
                                                placeholder="e.g. Cool, dry place"
                                                value="{{ old('storage', $spec?->storage) }}">
                                        </div>

                                        {{-- Dimensional Stability --}}
                                        <div class="mb-10">
                                            <label class="form-label">Dimensional Stability</label>
                                            <input type="text" class="form-control" name="dimensional_stability"
                                                placeholder="e.g. ≤ 0.5%"
                                                value="{{ old('dimensional_stability', $spec?->dimensional_stability) }}">
                                        </div>

                                        {{-- Release Paper --}}
                                        <div class="mb-10">
                                            <label class="form-label">Release Paper</label>
                                            <input type="text" class="form-control" name="release_paper"
                                                placeholder="e.g. 140g/m², double side PE coated"
                                                value="{{ old('release_paper', $spec?->release_paper) }}">
                                        </div>

                                        {{-- Adhesive --}}
                                        <div class="mb-10">
                                            <label class="form-label">Adhesive</label>
                                            <input type="text" class="form-control" name="adhesive"
                                                placeholder="e.g. Solvent acrylic"
                                                value="{{ old('adhesive', $spec?->adhesive) }}">
                                        </div>

                                        {{-- Adhesive Strength --}}
                                        <div class="mb-10">
                                            <label class="form-label">Adhesive Strength</label>
                                            <input type="text" class="form-control" name="adhesive_strength"
                                                placeholder="e.g. 1200 N/m"
                                                value="{{ old('adhesive_strength', $spec?->adhesive_strength) }}">
                                        </div>

                                        <div class="mb-10 row">
                                            <div class="col-md-6">
                                                {{-- Shelf Life --}}
                                                <label class="form-label">Shelf Life</label>
                                                <input type="text" class="form-control" name="shelf_life"
                                                    placeholder="e.g. 2 years"
                                                    value="{{ old('shelf_life', $spec?->shelf_life) }}">
                                            </div>
                                            <div class="col-md-6">
                                                {{-- Warranty --}}
                                                <label class="form-label">Warranty</label>
                                                <input type="text" class="form-control" name="warranty"
                                                    placeholder="e.g. 5 years"
                                                    value="{{ old('warranty', $spec?->warranty) }}">
                                            </div>
                                        </div>

                                        @push('scripts')
                                            <script>
                                                // Certification tags input logic
                                                (function() {
                                                    const input = document.getElementById('certificationInput');
                                                    const tagsContainer = document.getElementById('certificationTags');
                                                    const hiddenInput = document.getElementById('certificationTagsHidden');
                                                    let tags = [];

                                                    function renderTags() {
                                                        tagsContainer.innerHTML = '';
                                                        tags.forEach((tag, idx) => {
                                                            const tagEl = document.createElement('span');
                                                            tagEl.className = 'badge badge-light-primary fw-normal px-3 py-2 d-flex align-items-center';
                                                            tagEl.textContent = tag;
                                                            const removeBtn = document.createElement('button');
                                                            removeBtn.type = 'button';
                                                            removeBtn.className = 'btn btn-xs btn-icon btn-light-danger ms-2';
                                                            removeBtn.innerHTML = '<i class="ki-outline ki-cross fs-2"></i>';
                                                            removeBtn.onclick = () => {
                                                                tags.splice(idx, 1);
                                                                renderTags();
                                                            };
                                                            tagEl.appendChild(removeBtn);
                                                            tagsContainer.appendChild(tagEl);
                                                        });
                                                        hiddenInput.value = tags.join(',');
                                                    }

                                                    function addTag(tag) {
                                                        tag = tag.trim();
                                                        if (tag && !tags.includes(tag)) {
                                                            tags.push(tag);
                                                            renderTags();
                                                        }
                                                    }

                                                    input.addEventListener('keydown', function(e) {
                                                        if (e.key === 'Enter' || e.key === ',' || e.key === ' ') {
                                                            e.preventDefault();
                                                            addTag(input.value);
                                                            input.value = '';
                                                        }
                                                    });

                                                    input.addEventListener('blur', function() {
                                                        addTag(input.value);
                                                        input.value = '';
                                                    });

                                                    // Initialize from hidden input (old value or existing spec value)
                                                    if (hiddenInput && hiddenInput.value) {
                                                        tags = hiddenInput.value.split(',').map(t => t.trim()).filter(Boolean);
                                                        renderTags();
                                                    }
                                                })();

                                                // Product Features repeater logic
                                                (function() {
                                                    const repeater = document.getElementById('productFeaturesRepeater');
                                                    const list = repeater.querySelector('[data-feature-list]');
                                                    const addBtn = document.getElementById('addFeatureBtn');

                                                    function wireImagePreview(item) {
                                                        const input = item.querySelector('input[type="file"]');
                                                        if (!input || input.__wiredPreview) return;
                                                        input.__wiredPreview = true;
                                                        input.addEventListener('change', function() {
                                                            const preview = item.querySelector('.feature-image-preview');
                                                            if (!preview) return;
                                                            preview.innerHTML = '';
                                                            if (input.files && input.files[0]) {
                                                                const reader = new FileReader();
                                                                reader.onload = function(ev) {
                                                                    const img = document.createElement('img');
                                                                    img.src = ev.target.result;
                                                                    img.style.width = '100%';
                                                                    img.style.height = '100%';
                                                                    img.style.objectFit = 'cover';
                                                                    img.style.borderRadius = '4px';
                                                                    preview.appendChild(img);
                                                                };
                                                                reader.readAsDataURL(input.files[0]);
                                                            }
                                                        });
                                                    }

                                                    function getItems() {
                                                        return Array.from(list.querySelectorAll('[data-feature-item]'));
                                                    }

                                                    function renumberInputs() {
                                                        getItems().forEach((item, idx) => {
                                                            const nameInput = item.querySelector('input[type="text"]');
                                                            const fileInput = item.querySelector('input[type="file"]');
                                                            const existingInput = item.querySelector('input[type="hidden"]');
                                                            if (nameInput) nameInput.name = `features[${idx}][name]`;
                                                            if (fileInput) fileInput.name = `features[${idx}][image]`;
                                                            if (existingInput) existingInput.name = `features[${idx}][existing_image]`;
                                                        });
                                                    }

                                                    function wireDeleteButtons() {
                                                        getItems().forEach(item => {
                                                            const del = item.querySelector('[data-feature-delete]');
                                                            if (!del || del.__wired) return;
                                                            del.__wired = true;
                                                            del.addEventListener('click', function() {
                                                                if (getItems().length > 1) {
                                                                    item.remove();
                                                                    renumberInputs();
                                                                } else {
                                                                    // Clear values if only one left
                                                                    const nameInput = item.querySelector('input[type="text"]');
                                                                    const fileInput = item.querySelector('input[type="file"]');
                                                                    const existingInput = item.querySelector('input[type="hidden"]');
                                                                    if (nameInput) nameInput.value = '';
                                                                    if (fileInput) fileInput.value = '';
                                                                    if (existingInput) existingInput.value = '';

                                                                    const preview = item.querySelector('.feature-image-preview');
                                                                    if (preview) preview.innerHTML = '';
                                                                }
                                                            });
                                                        });
                                                    }

                                                    addBtn.addEventListener('click', function() {
                                                        const items = getItems();
                                                        const template = items[0];
                                                        if (!template) return;
                                                        const clone = template.cloneNode(true);
                                                        // Reset values
                                                        const nameInput = clone.querySelector('input[type="text"]');
                                                        const fileInput = clone.querySelector('input[type="file"]');
                                                        const existingInput = clone.querySelector('input[type="hidden"]');
                                                        if (nameInput) nameInput.value = '';
                                                        if (fileInput) fileInput.value = '';
                                                        if (existingInput) existingInput.value = '';

                                                        const preview = clone.querySelector('.feature-image-preview');
                                                        if (preview) preview.innerHTML = '';

                                                        list.appendChild(clone);
                                                        wireDeleteButtons();
                                                        wireImagePreview(clone);
                                                        renumberInputs();
                                                    });

                                                    // Initial setup
                                                    wireDeleteButtons();
                                                    getItems().forEach(wireImagePreview);
                                                    renumberInputs();
                                                })();
                                            </script>
                                        @endpush
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('catalog.products.index') }}" id="kt_ecommerce_add_product_cancel"
                            class="btn btn-light me-5">Cancel</a>
                        <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                            <span class="indicator-label">Save Changes</span>
                            <span class="indicator-progress">Please wait...
                                <span class="align-middle spinner-border spinner-border-sm ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Handle existing images removal - Execute immediately
        (function() {
            function initRemoveButtons() {
                const removedContainer = document.getElementById('removedImagesContainer');

                document.querySelectorAll('.remove-existing-image').forEach(function(btn) {
                    if (btn.dataset.wired) return; // Already wired
                    btn.dataset.wired = 'true';

                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const imagePath = this.getAttribute('data-image');
                        const wrapper = this.closest('.existing-image-wrapper');

                        if (wrapper && imagePath) {
                            console.log('Removing image:', imagePath);

                            // Remove the existing_images[] input so it won't be sent
                            const existingInput = wrapper.querySelector('.existing-image-input');
                            if (existingInput) {
                                existingInput.remove();
                            }

                            // Add to remove_images[] array
                            const removeInput = document.createElement('input');
                            removeInput.type = 'hidden';
                            removeInput.name = 'remove_images[]';
                            removeInput.value = imagePath;
                            removeInput.dataset.imagePath = imagePath;
                            if (removedContainer) {
                                removedContainer.appendChild(removeInput);
                            }

                            // Animate and remove from DOM
                            wrapper.style.transition = 'opacity 0.3s';
                            wrapper.style.opacity = '0.3';
                            wrapper.style.pointerEvents = 'none';

                            setTimeout(() => {
                                wrapper.remove();
                            }, 300);
                        }
                    });
                });
            }

            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initRemoveButtons);
            } else {
                initRemoveButtons();
            }
        })();
    </script>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const catalogSelect = document.getElementById('catalogSelect');
                const categorySelect = document.getElementById('categorySelect');

                // Store all category options
                const allCategoryOptions = Array.from(categorySelect.querySelectorAll('option[data-catalog]'));

                function filterCategories() {
                    const selectedCatalogId = catalogSelect.value;

                    // Clear current options except the first one
                    categorySelect.innerHTML = '<option value="">Select a sub category</option>';

                    if (selectedCatalogId) {
                        // Filter and add only matching categories
                        allCategoryOptions.forEach(option => {
                            if (option.getAttribute('data-catalog') === selectedCatalogId) {
                                categorySelect.appendChild(option.cloneNode(true));
                            }
                        });
                    } else {
                        // If no catalog selected, show all categories
                        allCategoryOptions.forEach(option => {
                            categorySelect.appendChild(option.cloneNode(true));
                        });
                    }

                    // Reinitialize Select2 if it's being used
                    if (typeof $(categorySelect).select2 === 'function') {
                        $(categorySelect).select2('destroy');
                        $(categorySelect).select2({
                            placeholder: 'Select an option',
                            allowClear: true
                        });
                    }
                }

                // Listen for catalog selection changes
                catalogSelect.addEventListener('change', filterCategories);

                // If using Select2, also listen to its change event
                if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                    $(catalogSelect).on('select2:select select2:clear', filterCategories);
                }

                // Initialize on page load
                filterCategories();
            });
        </script>
    @endpush
    <script>
        // Slugify function
        function slugify(text) {
            return text
                .toString()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remove accents
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, '-') // Replace non-alphanumeric with hyphens
                .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
        }

        const nameInput = document.getElementById('productName');
        const slugInput = document.getElementById('productSlug');
        let slugManuallyChanged = false;

        if (nameInput && slugInput) {
            // If user edits slug, don't auto-update anymore
            slugInput.addEventListener('input', function() {
                slugManuallyChanged = true;
            });

            nameInput.addEventListener('input', function() {
                if (!slugManuallyChanged || !slugInput.value) {
                    slugInput.value = slugify(this.value);
                }
            });
        }
    </script>

    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
        <script>
            // Catalog and Category relationship
            function setVisibleCategories(catalogId) {
                const categorySelect = document.getElementById('categorySelect');
                const categoryOptions = categorySelect.querySelectorAll('option[data-catalog]');

                // Show/hide categories based on selected catalog
                categoryOptions.forEach(option => {
                    if (!catalogId) {
                        // No catalog selected, hide all categories
                        option.style.display = 'none';
                        option.disabled = true;
                    } else if (option.dataset.catalog == catalogId) {
                        // Show categories that belong to selected catalog
                        option.style.display = '';
                        option.disabled = false;
                    } else {
                        // Hide categories that don't belong to selected catalog
                        option.style.display = 'none';
                        option.disabled = true;
                    }
                });

                // Clear category selection if it doesn't belong to selected catalog
                if (catalogId && categorySelect.value) {
                    const selectedOption = categorySelect.querySelector(`option[value="${categorySelect.value}"]`);
                    if (selectedOption && selectedOption.dataset.catalog != catalogId) {
                        categorySelect.value = '';
                    }
                } else if (!catalogId) {
                    categorySelect.value = '';
                }

                // Refresh Select2 UI if present
                try {
                    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
                        const $el = window.jQuery(categorySelect);
                        if ($el.data('select2')) {
                            // Destroy and reinitialize Select2 to properly update options
                            $el.select2('destroy');
                            $el.select2({
                                placeholder: 'Select an option',
                                allowClear: true
                            });
                        }
                    }
                } catch (e) {
                    console.error('Select2 error:', e);
                }
            }

            document.getElementById('catalogSelect').addEventListener('change', function() {
                setVisibleCategories(this.value);
            });

            // Discount type show/hide
            function toggleDiscountInputs(type) {
                const percentage = document.getElementById('kt_ecommerce_add_product_discount_percentage');
                const fixed = document.getElementById('kt_ecommerce_add_product_discount_fixed');

                percentage.classList.toggle('d-none', type !== 'percentage');
                fixed.classList.toggle('d-none', type !== 'fixed');

                // Ensure required discount fields have a value when enabled
                const percentageInput = document.querySelector('input[name="discount_value"]');
                const fixedInput = document.querySelector('input[name="discounted_price"]');
                const discountLabel = document.getElementById('kt_ecommerce_add_product_discount_label');
                const sliderEl = document.getElementById('kt_ecommerce_add_product_discount_slider');

                if (type === 'percentage' && percentageInput) {
                    if (percentageInput.value === '' || percentageInput.value === null) {
                        percentageInput.value = '0';
                    }
                    if (discountLabel) {
                        const v = parseFloat(percentageInput.value);
                        discountLabel.textContent = Number.isFinite(v) ? String(Math.round(v)) : '0';
                    }
                    if (sliderEl && sliderEl.noUiSlider) {
                        const v = parseFloat(percentageInput.value);
                        if (!Number.isNaN(v)) sliderEl.noUiSlider.set(v);
                    }
                }

                if (type === 'fixed' && fixedInput) {
                    if (fixedInput.value === '' || fixedInput.value === null) {
                        fixedInput.value = '0';
                    }
                }

                // Keep Keenthemes button UI in sync
                document.querySelectorAll('input[name="discount_type"]').forEach(radio => {
                    const label = radio.closest('[data-kt-button="true"]');
                    if (label) {
                        label.classList.toggle('active', radio.checked);
                    }
                });
            }

            document.querySelectorAll('input[name="discount_type"]').forEach(radio => {
                radio.addEventListener('change', () => toggleDiscountInputs(radio.value));
            });

            // Shipping show/hide
            function toggleShipping(show) {
                const shipping = document.getElementById('kt_ecommerce_add_product_shipping');
                shipping.classList.toggle('d-none', !show);
            }

            document.getElementById('kt_ecommerce_add_product_shipping_checkbox').addEventListener('change', function() {
                toggleShipping(this.checked);
            });

            // Handle new image uploads with preview
            const mediaInput = document.getElementById('productMediaInput');
            const mediaDropzone = document.getElementById('product_media_dropzone');
            const newImagesPreview = document.getElementById('newImagesPreview');
            let selectedNewFiles = [];

            function setMediaInputFiles(files) {
                try {
                    const dt = new DataTransfer();
                    files.forEach(f => dt.items.add(f));
                    mediaInput.files = dt.files;
                } catch (e) {
                    console.warn('DataTransfer not supported:', e);
                }
            }

            function renderNewImagesPreviews() {
                if (!newImagesPreview) return;
                newImagesPreview.innerHTML = '';

                if (selectedNewFiles.length === 0) {
                    return;
                }

                selectedNewFiles.forEach((file, idx) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'position-relative';
                    wrapper.style.width = '150px';
                    wrapper.style.height = '150px';

                    const img = document.createElement('img');
                    img.className = 'rounded w-100 h-100';
                    img.style.objectFit = 'cover';
                    img.style.border = '2px solid #009ef7';
                    img.alt = file.name;

                    const badge = document.createElement('div');
                    badge.className = 'position-absolute bottom-0 start-0 m-1';
                    badge.innerHTML = '<span class="badge badge-primary">New</span>';

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-icon btn-sm btn-light-danger position-absolute top-0 end-0 m-1';
                    removeBtn.title = 'Remove image';
                    removeBtn.innerHTML = '<i class="ki-outline ki-cross fs-2"></i>';
                    removeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        selectedNewFiles = selectedNewFiles.filter((_, i) => i !== idx);
                        setMediaInputFiles(selectedNewFiles);
                        renderNewImagesPreviews();
                    });

                    wrapper.appendChild(img);
                    wrapper.appendChild(badge);
                    wrapper.appendChild(removeBtn);
                    newImagesPreview.appendChild(wrapper);

                    // Read and display the image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            function addIncomingFiles(incoming) {
                const incomingArr = Array.from(incoming || []).filter(f => {
                    // Validate file type and size
                    if (!f.type.startsWith('image/')) {
                        console.warn('File is not an image:', f.name);
                        return false;
                    }
                    // Optional: Limit file size to 5MB
                    if (f.size > 5 * 1024 * 1024) {
                        console.warn('File is too large:', f.name);
                        alert(`File "${f.name}" is too large. Maximum size is 5MB.`);
                        return false;
                    }
                    return true;
                });

                if (!incomingArr.length) return;

                // Check total image count
                const totalImages = selectedNewFiles.length + incomingArr.length;
                if (totalImages > 10) {
                    alert('You can only upload up to 10 images at a time.');
                    return;
                }

                selectedNewFiles = selectedNewFiles.concat(incomingArr);
                setMediaInputFiles(selectedNewFiles);
                renderNewImagesPreviews();
            }

            if (mediaDropzone && mediaInput) {
                mediaDropzone.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    mediaInput.click();
                });

                mediaInput.addEventListener('change', function() {
                    const files = Array.from(this.files || []);
                    if (files.length > 0) {
                        addIncomingFiles(files);
                    }
                });
            }

            // Auto-generate meta title from product name
            document.getElementById('productName').addEventListener('input', function() {
                const metaTitle = document.getElementById('metaTitle');
                if (!metaTitle.value) {
                    metaTitle.value = this.value;
                }
            });

            window.addEventListener('DOMContentLoaded', function() {
                // CKEditor (Description + Meta Description)
                if (window.ClassicEditor) {
                    const editorConfig = {
                        toolbar: {
                            items: [
                                'heading', '|',
                                'bold', 'italic', 'link', '|',
                                'bulletedList', 'numberedList', '|',
                                'blockQuote', '|',
                                'undo', 'redo'
                            ]
                        }
                    };

                    const descEl = document.getElementById('descriptionEditor');
                    if (descEl) {
                        ClassicEditor.create(descEl, editorConfig).catch(() => {});
                    }

                    const metaDescEl = document.getElementById('metaDescriptionEditor');
                    if (metaDescEl) {
                        ClassicEditor.create(metaDescEl, editorConfig).catch(() => {});
                    }
                }

                // initial filter
                const catalogSelect = document.getElementById('catalogSelect');
                const categorySelect = document.getElementById('categorySelect');
                const initialCatalog = catalogSelect.value;

                // If there's a selected category, make sure its catalog is selected first
                if (categorySelect.value && !initialCatalog) {
                    const selectedCategoryOption = categorySelect.querySelector(
                        `option[value="${categorySelect.value}"]`);
                    if (selectedCategoryOption) {
                        const catalogId = selectedCategoryOption.dataset.catalog;
                        if (catalogId) {
                            catalogSelect.value = catalogId;
                            // Trigger Select2 update if exists
                            if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
                                try {
                                    window.jQuery(catalogSelect).trigger('change.select2');
                                } catch (e) {}
                            }
                        }
                    }
                }

                // Wait a bit for Select2 to initialize, then set visible categories
                setTimeout(function() {
                    setVisibleCategories(catalogSelect.value);
                }, 100);

                // initial toggles
                const checkedDiscount = document.querySelector('input[name="discount_type"]:checked');
                toggleDiscountInputs(checkedDiscount ? checkedDiscount.value : 'none');
                toggleShipping(document.getElementById('kt_ecommerce_add_product_shipping_checkbox').checked);

                // Keep percentage label in sync (works even without noUiSlider)
                const discountLabel = document.getElementById('kt_ecommerce_add_product_discount_label');
                const percentageInput = document.querySelector('input[name="discount_value"]');
                if (discountLabel && percentageInput) {
                    const updateLabel = () => {
                        const v = parseFloat(percentageInput.value);
                        discountLabel.textContent = Number.isFinite(v) ? String(Math.round(v)) : '0';
                    };
                    percentageInput.addEventListener('input', updateLabel);
                    updateLabel();
                }

                // If noUiSlider is initialized by the theme, keep the percentage input in sync
                const sliderEl = document.getElementById('kt_ecommerce_add_product_discount_slider');
                const percentageInput2 = document.querySelector('input[name="discount_value"]');
                const discountLabel2 = document.getElementById('kt_ecommerce_add_product_discount_label');
                if (sliderEl && percentageInput2) {
                    const attach = () => {
                        if (sliderEl.noUiSlider) {
                            sliderEl.noUiSlider.on('update', function(values, handle) {
                                const val = Math.round(values[handle]);
                                percentageInput2.value = val;
                                if (discountLabel2) discountLabel2.textContent = String(val);
                            });

                            const current = parseFloat(percentageInput2.value);
                            if (!Number.isNaN(current)) {
                                sliderEl.noUiSlider.set(current < 1 ? 1 : current);
                            }
                            return true;
                        }
                        return false;
                    };
                    if (!attach()) {
                        setTimeout(attach, 250);
                        setTimeout(attach, 750);
                        setTimeout(attach, 1500);
                    }
                }

                // Variations repeater (vanilla JS)
                const optionsRoot = document.getElementById('kt_ecommerce_add_product_options');
                if (optionsRoot) {
                    const list = optionsRoot.querySelector('[data-repeater-list]');
                    const createBtn = optionsRoot.querySelector('[data-repeater-create]');
                    const getItems = () => Array.from(list ? list.querySelectorAll('[data-repeater-item]') : []);

                    const initSelect2For = (selectEl) => {
                        if (!selectEl) return;

                        // Remove any cloned Select2 UI
                        const next = selectEl.nextElementSibling;
                        if (next && next.classList && next.classList.contains('select2')) {
                            next.remove();
                        }
                        selectEl.classList.remove('select2-hidden-accessible');
                        selectEl.removeAttribute('data-select2-id');
                        selectEl.removeAttribute('tabindex');
                        selectEl.removeAttribute('aria-hidden');
                        selectEl.querySelectorAll('option[data-select2-id]').forEach(opt => opt.removeAttribute(
                            'data-select2-id'));

                        // Re-init Select2 if available
                        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
                            const $el = window.jQuery(selectEl);
                            try {
                                if ($el.data('select2')) {
                                    $el.select2('destroy');
                                }
                            } catch (_) {}

                            const placeholder = selectEl.getAttribute('data-placeholder') || 'Select a variation';
                            $el.select2({
                                placeholder,
                                allowClear: true,
                                width: '100%'
                            });
                        }
                    };

                    const destroySelect2For = (selectEl) => {
                        if (!selectEl) return;
                        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
                            const $el = window.jQuery(selectEl);
                            try {
                                if ($el.data('select2')) {
                                    $el.select2('destroy');
                                }
                            } catch (_) {}
                        }
                    };

                    const renumberVariationInputs = () => {
                        if (!list) return;
                        const listName = list.getAttribute('data-repeater-list') ||
                            'kt_ecommerce_add_product_options';
                        getItems().forEach((item, index) => {
                            const select = item.querySelector('select[name]');
                            const input = item.querySelector('input[name]');
                            if (select) select.name = `${listName}[${index}][product_option]`;
                            if (input) input.name = `${listName}[${index}][product_option_value]`;
                        });
                    };

                    const wireDeleteButtons = () => {
                        getItems().forEach(item => {
                            const del = item.querySelector('[data-repeater-delete]');
                            if (!del || del.__wired) return;
                            del.__wired = true;
                            del.addEventListener('click', function() {
                                destroySelect2For(item.querySelector('select'));
                                item.remove();
                                renumberVariationInputs();
                            });
                        });
                    };

                    if (createBtn && list) {
                        createBtn.addEventListener('click', function() {
                            const items = getItems();
                            const template = items[0];
                            if (!template) return;

                            const clone = template.cloneNode(true);
                            // reset values
                            const sel = clone.querySelector('select');
                            if (sel) sel.value = '';
                            const txt = clone.querySelector('input[type="text"], input[name]');
                            if (txt) txt.value = '';

                            list.appendChild(clone);
                            initSelect2For(clone.querySelector('select'));
                            wireDeleteButtons();
                            renumberVariationInputs();
                        });
                    }

                    // initial setup
                    wireDeleteButtons();
                    renumberVariationInputs();
                }
            });

            // Bypass Keenthemes demo submit handler (capture phase runs before their bubble handler)
            const submitBtn = document.getElementById('kt_ecommerce_add_product_submit');
            if (submitBtn) {
                submitBtn.addEventListener('click', function(e) {
                    e.stopImmediatePropagation();
                }, true);
            }

            // Enhanced drag and drop support
            const dz = document.getElementById('product_media_dropzone');
            if (dz && mediaInput) {
                // Prevent default drag behaviors
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dz.addEventListener(eventName, function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }, false);
                });

                // Add visual feedback for drag over
                ['dragenter', 'dragover'].forEach(eventName => {
                    dz.addEventListener(eventName, function() {
                        dz.style.borderColor = '#009ef7';
                        dz.style.backgroundColor = '#f1faff';
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dz.addEventListener(eventName, function() {
                        dz.style.borderColor = '';
                        dz.style.backgroundColor = '';
                    }, false);
                });

                // Handle dropped files
                dz.addEventListener('drop', function(e) {
                    const files = e.dataTransfer.files;
                    if (files && files.length > 0) {
                        addIncomingFiles(files);
                    }
                }, false);
            }
        </script>
    @endpush

</x-default-layout>
