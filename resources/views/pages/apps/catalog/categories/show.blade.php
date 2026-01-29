<x-default-layout>

    @section('title')
        {{ $category->name }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('catalog.categories.show', $category) }}
    @endsection

    {{-- Page Header --}}
    <div class="mb-6 card card-flush mb-xl-9">
        <div class="pb-0 card-body pt-9">
            {{-- Back Button & Title --}}
            <div class="flex-wrap d-flex flex-sm-nowrap">
                <a href="{{ route('catalog.categories.index') }}"
                    class="btn btn-sm btn-icon btn-active-color-primary me-3">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </a>

                <div class="flex-grow-1">
                    <div class="flex-wrap d-flex justify-content-between align-items-start">
                        {{-- Category Title & Info --}}
                        <div class="d-flex flex-column">
                            <div class="mb-2 d-flex align-items-center">
                                <span class="text-gray-900 fs-2 fw-bold me-3">{{ $category->name }}</span>
                                @if ($category->is_active)
                                    <span class="badge badge-light-success fs-7 fw-semibold">
                                        <i class="ki-duotone ki-check-circle fs-5 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Active
                                    </span>
                                @else
                                    <span class="badge badge-light-danger fs-7 fw-semibold">
                                        <i class="ki-duotone ki-cross-circle fs-5 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Inactive
                                    </span>
                                @endif
                            </div>

                            {{-- Meta Info --}}
                            <div class="flex-wrap mb-4 d-flex fw-semibold fs-6 pe-2">
                                <span class="mb-2 text-gray-500 d-flex align-items-center me-5">
                                    <i class="ki-duotone ki-code fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    <code class="text-gray-700">{{ $category->slug }}</code>
                                </span>

                                <span class="mb-2 text-gray-500 d-flex align-items-center me-5">
                                    <i class="ki-duotone ki-tag fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    {{ $category->catalog->name }}
                                </span>

                                <span class="mb-2 text-gray-500 d-flex align-items-center">
                                    <i class="ki-duotone ki-package fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    {{ $category->products->count() }} Products
                                </span>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="gap-2 my-4 d-flex">
                            <a href="{{ route('catalog.categories.edit', $category) }}" class="btn btn-sm btn-primary">
                                <i class="ki-duotone ki-pencil fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Edit Category
                            </a>
                            <button type="button" class="btn btn-sm btn-light-danger" onclick="deleteCategory()">
                                <i class="ki-duotone ki-trash fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs Navigation --}}
            <ul class="border-transparent nav nav-stretch nav-line-tabs nav-line-tabs-2x fs-5 fw-bold">
                <li class="mt-2 nav-item">
                    <a class="py-5 nav-link text-active-primary ms-0 me-10 active" data-bs-toggle="tab"
                        href="#kt_tab_overview">
                        <i class="ki-duotone ki-element-11 fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        Overview
                    </a>
                </li>
                <li class="mt-2 nav-item">
                    <a class="py-5 nav-link text-active-primary me-10" data-bs-toggle="tab" href="#kt_tab_products">
                        <i class="ki-duotone ki-package fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Products ({{ $category->products->count() }})
                    </a>
                </li>
                <li class="mt-2 nav-item">
                    <a class="py-5 nav-link text-active-primary me-10" data-bs-toggle="tab" href="#kt_tab_seo">
                        <i class="ki-duotone ki-chart-simple fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        SEO Details
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row g-6 g-xl-9">
        {{-- Main Content --}}
        <div class="col-lg-8">
            <div class="tab-content" id="kt_category_tab_content">
                {{-- Overview Tab --}}
                <div class="tab-pane fade show active" id="kt_tab_overview" role="tabpanel">
                    {{-- Category Details Card --}}
                    <div class="mb-6 card card-flush">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="text-gray-800 card-label fw-bold">Category Information</span>
                                <span class="mt-1 text-gray-500 fw-semibold fs-6">Essential details about this
                                    category</span>
                            </h3>
                        </div>
                        <div class="pt-6 card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-bordered table-row-dashed gy-4 fw-bold">
                                    <tbody class="text-gray-600 fs-6">
                                        <tr>
                                            <td class="text-gray-800 min-w-200px">Category Name</td>
                                            <td class="text-end">
                                                <span
                                                    class="badge badge-light-primary fs-7">{{ $category->name }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800">URL Slug</td>
                                            <td class="text-end">
                                                <code class="fs-6">{{ $category->slug }}</code>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800">Catalog</td>
                                            <td class="text-end">
                                                <span
                                                    class="badge badge-light-info">{{ $category->catalog->name }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800">Status</td>
                                            <td class="text-end">
                                                @if ($category->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800">Sort Order</td>
                                            <td class="text-end">
                                                <span class="badge badge-light">{{ $category->sort_order }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800">Products Count</td>
                                            <td class="text-end">
                                                <span
                                                    class="badge badge-light-success fs-7">{{ $category->products->count() }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800">Created At</td>
                                            <td class="text-end">{{ $category->created_at->format('F d, Y h:i A') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800">Last Updated</td>
                                            <td class="text-end">{{ $category->updated_at->format('F d, Y h:i A') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Description Card --}}
                    @if ($category->description)
                        <div class="mb-6 card card-flush">
                            <div class="card-header pt-7">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="text-gray-800 card-label fw-bold">Description</span>
                                </h3>
                            </div>
                            <div class="pt-6 card-body">
                                <div class="text-gray-700 fs-6 lh-lg">
                                    {{ $category->description }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Category Image --}}
                    @if ($category->image)
                        <div class="card card-flush">
                            <div class="card-header pt-7">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="text-gray-800 card-label fw-bold">Category Image</span>
                                </h3>
                            </div>
                            <div class="pt-6 text-center card-body">
                                <div class="p-5 border border-gray-300 border-dashed rounded d-inline-block">
                                    <img src="{{ asset('storage/' . $category->image) }}"
                                        alt="{{ $category->name }}" class="rounded mw-100"
                                        style="max-height: 400px;">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Products Tab --}}
                <div class="tab-pane fade" id="kt_tab_products" role="tabpanel">
                    <div class="card card-flush">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="text-gray-800 card-label fw-bold">Products in this Category</span>
                                <span class="mt-1 text-gray-500 fw-semibold fs-6">{{ $category->products->count() }}
                                    products found</span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('catalog.products.create', ['category_id' => $category->id]) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ki-duotone ki-plus fs-2"></i>
                                    Add Product
                                </a>
                            </div>
                        </div>
                        <div class="pt-6 card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed table-row-gray-300 gs-0 gy-4"
                                    id="productsTable">
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="ps-4 min-w-50px rounded-start">Image</th>
                                            <th class="min-w-200px">Product Name</th>
                                            <th class="min-w-100px">Price</th>
                                            <th class="text-center min-w-100px">Featured</th>
                                            <th class="text-center min-w-100px">Popular</th>
                                            <th class="min-w-100px">Status</th>
                                            <th class="min-w-100px text-end rounded-end pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-semibold">
                                        @forelse($category->products as $product)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="symbol symbol-50px">
                                                        @if ($product->images && count($product->images) > 0)
                                                            <img src="{{ asset('storage/' . $product->images[0]) }}"
                                                                alt="{{ $product->name }}" class="symbol-label">
                                                        @else
                                                            <span
                                                                class="symbol-label bg-light-info text-info fs-5 fw-bold">
                                                                {{ substr($product->name, 0, 1) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('catalog.products.show', $product) }}"
                                                        class="text-gray-800 text-hover-primary fw-bold">
                                                        {{ $product->name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span
                                                        class="text-gray-800 fw-bold">${{ number_format($product->price, 2) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if ($product->is_featured)
                                                        <span class="badge badge-light-success">
                                                            <i class="ki-duotone ki-check fs-5"></i>
                                                            Yes
                                                        </span>
                                                    @else
                                                        <span class="badge badge-light">No</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($product->is_popular)
                                                        <span class="badge badge-light-success">
                                                            <i class="ki-duotone ki-check fs-5"></i>
                                                            Yes
                                                        </span>
                                                    @else
                                                        <span class="badge badge-light">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($product->is_active)
                                                        <span class="badge badge-light-success">Active</span>
                                                    @else
                                                        <span class="badge badge-light-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="{{ route('catalog.products.show', $product) }}"
                                                        class="btn btn-sm btn-icon btn-light btn-active-light-primary me-2"
                                                        data-bs-toggle="tooltip" title="View Product">
                                                        <i class="ki-duotone ki-eye fs-3">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                    </a>
                                                    <a href="{{ route('catalog.products.edit', $product) }}"
                                                        class="btn btn-sm btn-icon btn-light btn-active-light-primary"
                                                        data-bs-toggle="tooltip" title="Edit Product">
                                                        <i class="ki-duotone ki-pencil fs-3">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="py-20 text-center">
                                                    <i class="mb-5 text-gray-400 ki-duotone ki-file-deleted fs-5x">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <h3 class="mb-3 text-gray-800 fw-bold">No Products Yet</h3>
                                                    <p class="text-gray-500 fw-semibold fs-6 mb-7">Start adding
                                                        products to this category</p>
                                                    <a href="{{ route('catalog.products.create', ['category_id' => $category->id]) }}"
                                                        class="btn btn-primary">
                                                        <i class="ki-duotone ki-plus fs-2"></i>
                                                        Add First Product
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEO Details Tab --}}
                <div class="tab-pane fade" id="kt_tab_seo" role="tabpanel">
                    <div class="card card-flush">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="text-gray-800 card-label fw-bold">SEO Meta Information</span>
                                <span class="mt-1 text-gray-500 fw-semibold fs-6">Search engine optimization
                                    details</span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('catalog.categories.edit', $category) }}"
                                    class="btn btn-sm btn-light-primary">
                                    <i class="ki-duotone ki-pencil fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Edit SEO
                                </a>
                            </div>
                        </div>
                        <div class="pt-6 card-body">
                            <div class="table-responsive">
                                <table class="table table-row-bordered gy-5">
                                    <tbody>
                                        <tr>
                                            <td class="text-gray-800 fw-bold min-w-200px">Meta Title</td>
                                            <td>
                                                @if ($category->meta_title)
                                                    <span class="text-gray-700">{{ $category->meta_title }}</span>
                                                    <span
                                                        class="badge badge-light-success ms-2">{{ strlen($category->meta_title) }}
                                                        chars</span>
                                                @else
                                                    <span class="text-gray-500 fst-italic">Not set</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800 fw-bold">Meta Description</td>
                                            <td>
                                                @if ($category->meta_description)
                                                    <span
                                                        class="text-gray-700">{{ $category->meta_description }}</span>
                                                    <span
                                                        class="badge badge-light-success ms-2">{{ strlen($category->meta_description) }}
                                                        chars</span>
                                                @else
                                                    <span class="text-gray-500 fst-italic">Not set</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800 fw-bold">Meta Keywords</td>
                                            <td>
                                                @if ($category->meta_keywords)
                                                    @foreach (explode(',', $category->meta_keywords) as $keyword)
                                                        <span
                                                            class="badge badge-light me-1">{{ trim($keyword) }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-gray-500 fst-italic">Not set</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- SEO Preview --}}
                            @if ($category->meta_title || $category->meta_description)
                                <div class="my-10 separator separator-dashed"></div>
                                <div class="mb-0">
                                    <h4 class="mb-5 text-gray-800 fw-bold">
                                        <i class="bi bi-google fs-2 text-primary me-2"></i>
                                        Search Engine Preview
                                    </h4>
                                    <div class="p-8 border border-gray-300 rounded bg-light">
                                        <div class="mb-2">
                                            <span class="text-success fs-7">
                                                {{ url('/') }}/{{ $category->slug }}
                                            </span>
                                        </div>
                                        <h3 class="mb-2 text-primary fs-4 fw-bold text-hover-underline"
                                            style="cursor: pointer;">
                                            {{ $category->meta_title ?: $category->name }}
                                        </h3>
                                        <p class="mb-0 text-gray-700 fs-6">
                                            {{ $category->meta_description ?: Str::limit($category->description, 160) }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Quick Stats Card --}}
            <div class="mb-6 card card-flush bg-light-info">
                <div class="card-body">
                    <div class="mb-5 d-flex align-items-center">
                        <i class="ki-duotone ki-chart-simple fs-3x text-info me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        <div>
                            <div class="text-gray-900 fs-2hx fw-bold">{{ $category->products->count() }}</div>
                            <div class="text-gray-600 fs-7 fw-semibold">Total Products</div>
                        </div>
                    </div>

                    <div class="mb-5 separator separator-dashed"></div>

                    <div class="mb-3 d-flex justify-content-between">
                        <span class="text-gray-700 fw-semibold">Active Products:</span>
                        <span
                            class="badge badge-light-success">{{ $category->products->where('is_active', 1)->count() }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="text-gray-700 fw-semibold">Featured Products:</span>
                        <span
                            class="badge badge-light-primary">{{ $category->products->where('is_featured', 1)->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-gray-700 fw-semibold">Popular Products:</span>
                        <span
                            class="badge badge-light-warning">{{ $category->products->where('is_popular', 1)->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Related Product Card --}}
            @if ($category->relatedProduct)
                <div class="mb-6 card card-flush">
                    <div class="card-header pt-7">
                        <h3 class="card-title">
                            <span class="text-gray-800 card-label fw-bold">Related Product</span>
                        </h3>
                    </div>
                    <div class="pt-5 card-body">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-50px me-3">
                                @if ($category->relatedProduct->thumbnail)
                                    <img src="{{ asset('storage/' . $category->relatedProduct->thumbnail) }}"
                                        alt="{{ $category->relatedProduct->name }}" class="symbol-label">
                                @else
                                    <span class="symbol-label bg-light-primary text-primary fs-5 fw-bold">
                                        {{ substr($category->relatedProduct->name, 0, 1) }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <a href="{{ route('catalog.products.show', $category->relatedProduct) }}"
                                    class="text-gray-800 text-hover-primary fw-bold d-block">
                                    {{ $category->relatedProduct->name }}
                                </a>
                                <span
                                    class="text-gray-500 fs-7">${{ number_format($category->relatedProduct->price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Settings Info Card --}}
            <div class="mb-6 card card-flush">
                <div class="card-header pt-7">
                    <h3 class="card-title">
                        <span class="text-gray-800 card-label fw-bold">Settings</span>
                    </h3>
                </div>
                <div class="pt-5 card-body">
                    <div class="mb-5 d-flex justify-content-between align-items-center">
                        <span class="text-gray-700 fw-semibold">Catalog:</span>
                        <span class="badge badge-light-info">{{ $category->catalog->name }}</span>
                    </div>

                    <div class="mb-5 d-flex justify-content-between align-items-center">
                        <span class="text-gray-700 fw-semibold">Status:</span>
                        @if ($category->is_active)
                            <span class="badge badge-light-success">Active</span>
                        @else
                            <span class="badge badge-light-danger">Inactive</span>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-gray-700 fw-semibold">Sort Order:</span>
                        <span class="badge badge-light">{{ $category->sort_order }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card card-flush bg-light-primary">
                <div class="card-body">
                    <h3 class="mb-5 text-gray-800 fw-bold">Quick Actions</h3>
                    <a href="{{ route('catalog.categories.edit', $category) }}" class="mb-3 btn btn-primary w-100">
                        <i class="ki-duotone ki-pencil fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Edit Category
                    </a>
                    <a href="{{ route('catalog.products.create', ['category_id' => $category->id]) }}"
                        class="mb-3 btn btn-light-success w-100">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Add Product
                    </a>
                    <div class="my-3 separator"></div>
                    <button type="button" class="btn btn-light-danger w-100" onclick="deleteCategory()">
                        <i class="ki-duotone ki-trash fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                        Delete Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize tooltips
                const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltips.forEach(tooltip => {
                    new bootstrap.Tooltip(tooltip);
                });

                // Load products via AJAX
                function loadProducts() {
                    $.ajax({
                        url: "{{ route('catalog.categories.show', $category) }}",
                        type: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            if (response.success && response.data.products) {
                                updateProductsTable(response.data.products);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading products:', xhr);
                        }
                    });
                }

                function updateProductsTable(products) {
                    const tbody = $('#productsTable tbody');

                    if (products.length === 0) {
                        tbody.html(`
                            <tr>
                                <td colspan="7" class="py-20 text-center">
                                    <i class="mb-5 text-gray-400 ki-duotone ki-file-deleted fs-5x">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <h3 class="mb-3 text-gray-800 fw-bold">No Products Yet</h3>
                                    <p class="text-gray-500 fw-semibold fs-6 mb-7">Start adding products to this category</p>
                                    <a href="{{ route('catalog.products.create', ['category_id' => $category->id]) }}" class="btn btn-primary">
                                        <i class="ki-duotone ki-plus fs-2"></i>
                                        Add First Product
                                    </a>
                                </td>
                            </tr>
                        `);
                        return;
                    }

                    let html = '';
                    products.forEach(product => {
                        const imageHtml = product.images && product.images.length > 0 ?
                            `<div class="symbol symbol-50px">
                                <img src="/storage/${product.images[0]}" alt="${product.name}" class="symbol-label">
                            </div>` :
                            `<div class="symbol symbol-50px">
                                <span class="symbol-label bg-light-info text-info fs-5 fw-bold">
                                    ${product.name.charAt(0)}
                                </span>
                            </div>`;

                        const statusBadge = product.is_active ?
                            '<span class="badge badge-light-success">Active</span>' :
                            '<span class="badge badge-light-danger">Inactive</span>';

                        const featuredBadge = product.is_featured ?
                            '<span class="badge badge-light-success"><i class="ki-duotone ki-check fs-5"></i> Yes</span>' :
                            '<span class="badge badge-light">No</span>';

                        const popularBadge = product.is_popular ?
                            '<span class="badge badge-light-success"><i class="ki-duotone ki-check fs-5"></i> Yes</span>' :
                            '<span class="badge badge-light">No</span>';

                        html += `
                            <tr>
                                <td class="ps-4">${imageHtml}</td>
                                <td>
                                    <a href="/catalog/products/${product.id}" class="text-gray-800 text-hover-primary fw-bold">
                                        ${product.name}
                                    </a>
                                </td>
                                <td><span class="text-gray-800 fw-bold">$${parseFloat(product.price).toFixed(2)}</span></td>
                                <td class="text-center">${featuredBadge}</td>
                                <td class="text-center">${popularBadge}</td>
                                <td>${statusBadge}</td>
                                <td class="text-end pe-4">
                                    <a href="/catalog/products/${product.id}" class="btn btn-sm btn-icon btn-light btn-active-light-primary me-2" data-bs-toggle="tooltip" title="View Product">
                                        <i class="ki-duotone ki-eye fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </a>
                                    <a href="/catalog/products/${product.id}/edit" class="btn btn-sm btn-icon btn-light btn-active-light-primary" data-bs-toggle="tooltip" title="Edit Product">
                                        <i class="ki-duotone ki-pencil fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });

                    tbody.html(html);

                    // Reinitialize tooltips
                    const newTooltips = tbody[0].querySelectorAll('[data-bs-toggle="tooltip"]');
                    newTooltips.forEach(tooltip => {
                        new bootstrap.Tooltip(tooltip);
                    });
                }
            });

            function deleteCategory() {
                Swal.fire({
                    title: 'Delete Category?',
                    text: "This action cannot be undone! All products in this category will also be affected.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f1416c',
                    cancelButtonColor: '#7e8299',
                    confirmButtonText: 'Yes, Delete It',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-light'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add your delete logic here
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Category has been deleted successfully.',
                            icon: 'success',
                            confirmButtonColor: '#50cd89',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        }).then(() => {
                            window.location.href = "{{ route('catalog.categories.index') }}";
                        });
                    }
                });
            }
        </script>
    @endpush

</x-default-layout>
