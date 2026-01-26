<x-default-layout>

    @section('title')
        {{ $category->name }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('catalog.categories.show', $category) }}
    @endsection

    <div class="mb-5 d-flex flex-column flex-lg-row gap-7 gap-lg-10">
        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
            <div class="py-4 card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Related Product</h2>
                    </div>
                </div>
                <div class="pt-0 card-body">
                    @if ($category->relatedProduct)
                        <div class="d-flex align-items-center">
                            <img src="{{ $category->relatedProduct->thumbnail ? asset('storage/' . $category->relatedProduct->thumbnail) : asset('assets/media/svg/files/blank-image.svg') }}"
                                class="rounded me-2" style="width:40px;height:40px;object-fit:cover;"
                                alt="{{ $category->relatedProduct->name }}" />
                            <span class="fw-bold">{{ $category->relatedProduct->name }}</span>
                        </div>
                    @else
                        <span class="text-muted">No related product selected.</span>
                    @endif
                </div>
            </div>
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
                    <div class="mb-3 image-input image-input-outline @if ($category->image) @else image-input-empty image-input-placeholder @endif"
                        data-kt-image-input="true">
                        <div class="image-input-wrapper w-150px h-150px"
                            @if ($category->image) style="background-image: url('{{ asset('storage/' . $category->image) }}')" @endif>
                        </div>
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
                    <select class="form-select" name="catalog_id" id="catalogId" data-control="select2" required
                        disabled>
                        <option value="{{ $category->catalog_id }}" selected>{{ $category->catalog->name }}</option>
                    </select>
                </div>
            </div>

            <div class="py-4 card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Status</h2>
                    </div>
                </div>
                <div class="pt-0 card-body">
                    <select class="form-select" name="is_active" id="isActive" data-control="select2" disabled>
                        <option value="1" {{ old('is_active', $category->is_active) == 1 ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="0" {{ old('is_active', $category->is_active) == 0 ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>
            </div>

            <div class="py-4 card">
                <div class="items-center px-8 justify-content-between d-flex">
                    <h5>Sort Order</h5>
                    <span class="text-gray-800 fw-bold fs-5">{{ $category->sort_order }}</span>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
            <div class="py-4 card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h2>General Information</h2>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('catalog.categories.edit', $category) }}" class="btn btn-sm btn-primary">
                            {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                            Edit Category
                        </a>
                    </div>
                </div>
                <div class="pt-0 card-body">
                    <div class="row mb-7">
                        <div class="col-lg-6">
                            <label class="fw-bold text-muted">Name</label>
                            <div class="text-gray-800 fw-bold fs-5">{{ $category->name }}</div>
                        </div>
                        <div class="col-lg-6">
                            <label class="fw-bold text-muted">Slug</label>
                            <div class="text-gray-600 fs-6">{{ $category->slug }}</div>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <div class="col-lg-12">
                            <label class="fw-bold text-muted">Description</label>
                            <div class="text-gray-600">{{ $category->description ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <label class="fw-bold text-muted">Created At</label>
                            <div class="text-gray-600">{{ $category->created_at->format('M d, Y h:i A') }}</div>
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
                    <div class="mb-7">
                        <label class="fw-bold text-muted">Meta Tag Title</label>
                        <div class="text-gray-600">{{ $category->meta_title ?? 'N/A' }}</div>
                    </div>

                    <div class="mb-7">
                        <label class="fw-bold text-muted">Meta Tag Description</label>
                        <div class="text-gray-600">{{ $category->meta_description ?? 'N/A' }}</div>
                    </div>

                    <div>
                        <label class="fw-bold text-muted">Meta Tag Keywords</label>
                        <div class="text-gray-600">{{ $category->meta_keywords ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <h3>Products ({{ $category->products->count() }})</h3>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('catalog.products.create', ['category_id' => $category->id]) }}"
                    class="btn btn-sm btn-primary">
                    {!! getIcon('plus', 'fs-3', '', 'i') !!}
                    Add Product
                </a>
            </div>
        </div>

        <div class="py-4 card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="productsTable">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-50px">Image</th>
                            <th class="min-w-125px">Name</th>
                            <th class="min-w-100px">Price</th>
                            <th class="min-w-100px">Featured</th>
                            <th class="min-w-100px">Popular</th>
                            <th class="min-w-100px">Status</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($category->products as $product)
                            <tr>
                                <td>
                                    @if ($product->images && count($product->images) > 0)
                                        <img src="{{ asset('storage/' . $product->images[0]) }}"
                                            alt="{{ $product->name }}" class="rounded w-50px h-50px">
                                    @else
                                        <div class="symbol symbol-50px">
                                            <span class="symbol-label bg-light-info text-info fs-6 fw-bold">
                                                {{ substr($product->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('catalog.products.show', $product) }}"
                                        class="text-gray-800 text-hover-primary">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>
                                    @if ($product->is_featured)
                                        <span class="badge badge-light-success">Yes</span>
                                    @else
                                        <span class="badge badge-light">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($product->is_popular)
                                        <span class="badge badge-light-success">Yes</span>
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
                                <td class="text-end">
                                    <a href="{{ route('catalog.products.show', $product) }}"
                                        class="btn btn-sm btn-light btn-active-light-primary">
                                        View
                                    </a>
                                    <a href="{{ route('catalog.products.edit', $product) }}"
                                        class="btn btn-sm btn-light btn-active-light-primary">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center">
                                    <div class="text-gray-600">No products found</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
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
                                <td colspan="7" class="py-10 text-center">
                                    <div class="text-gray-600">No products found</div>
                                </td>
                            </tr>
                        `);
                        return;
                    }

                    let html = '';
                    products.forEach(product => {
                        const imageHtml = product.images && product.images.length > 0 ?
                            `<img src="/storage/${product.images[0]}" alt="${product.name}" class="rounded w-50px h-50px">` :
                            `<div class="symbol symbol-50px">
                                <span class="symbol-label bg-light-info text-info fs-6 fw-bold">
                                    ${product.name.charAt(0)}
                                </span>
                            </div>`;

                        const statusBadge = product.is_active ?
                            '<span class="badge badge-light-success">Active</span>' :
                            '<span class="badge badge-light-danger">Inactive</span>';

                        const featuredBadge = product.is_featured ?
                            '<span class="badge badge-light-success">Yes</span>' :
                            '<span class="badge badge-light">No</span>';

                        const popularBadge = product.is_popular ?
                            '<span class="badge badge-light-success">Yes</span>' :
                            '<span class="badge badge-light">No</span>';

                        html += `
                            <tr>
                                <td>${imageHtml}</td>
                                <td>
                                    <a href="/catalog/products/${product.id}" class="text-gray-800 text-hover-primary">
                                        ${product.name}
                                    </a>
                                </td>
                                <td>$${parseFloat(product.price).toFixed(2)}</td>
                                <td>${featuredBadge}</td>
                                <td>${popularBadge}</td>
                                <td>${statusBadge}</td>
                                <td class="text-end">
                                    <a href="/catalog/products/${product.id}" class="btn btn-sm btn-light btn-active-light-primary">
                                        View
                                    </a>
                                    <a href="/catalog/products/${product.id}/edit" class="btn btn-sm btn-light btn-active-light-primary">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        `;
                    });

                    tbody.html(html);
                }
            });
        </script>
    @endpush

</x-default-layout>
