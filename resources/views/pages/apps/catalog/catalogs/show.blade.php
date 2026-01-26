<x-default-layout>

    @section('title')
        {{ $catalog->name }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('catalog.catalogs.show', $catalog) }}
    @endsection

    <div class="mb-5 d-flex flex-column flex-lg-row gap-7 gap-lg-10">
        <!--begin::Sidebar-->
        <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7">
            <!--begin::Thumbnail-->
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
                    <div class="mb-3 image-input image-input-outline @if ($catalog->image) @else image-input-empty image-input-placeholder @endif"
                        data-kt-image-input="true">
                        <div class="image-input-wrapper w-150px h-150px"
                            @if ($catalog->image) style="background-image: url('{{ asset('storage/' . $catalog->image) }}')" @endif>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Thumbnail-->

            <!--begin::Status-->
            <div class="py-4 card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Status</h2>
                    </div>
                </div>
                <div class="pt-0 card-body">
                    <select class="form-select" name="is_active" id="isActive" data-control="select2" disabled>
                        <option value="1" {{ old('is_active', $catalog->is_active) == 1 ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="0" {{ old('is_active', $catalog->is_active) == 0 ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>
            </div>
            <!--end::Status-->

            <div class="py-4 card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Related Product</h2>
                    </div>
                </div>
                <div class="pt-0 card-body">
                    @if ($catalog->relatedProduct)
                        <div class="d-flex align-items-center">
                            <img src="{{ $catalog->relatedProduct->thumbnail ? asset('storage/' . $catalog->relatedProduct->thumbnail) : asset('assets/media/svg/files/blank-image.svg') }}"
                                class="rounded me-2" style="width:40px;height:40px;object-fit:cover;"
                                alt="{{ $catalog->relatedProduct->name }}" />
                            <span class="fw-bold">{{ $catalog->relatedProduct->name }}</span>
                        </div>
                    @else
                        <span class="text-muted">No related product selected.</span>
                    @endif
                </div>
            </div>
            <!--begin::Related Product-->

            <!--begin::Sort Order-->
            <div class="py-4 card">
                <div class="items-center px-8 justify-content-between d-flex">
                    <h5>Sort Order</h5>
                    <span class="text-gray-800 fw-bold fs-5">{{ $catalog->sort_order }}</span>
                </div>
            </div>
            <!--end::Sort Order-->

        </div>
        <!--end::Sidebar-->

        <!--begin::Main column-->
        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
            <!--begin::General Info-->
            <div class="py-4 card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h2>General Information</h2>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('catalog.catalogs.edit', $catalog) }}" class="btn btn-sm btn-primary">
                            {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                            Edit Catalog
                        </a>
                    </div>
                </div>
                <div class="pt-0 card-body">
                    <div class="row mb-7">
                        <div class="col-lg-6">
                            <label class="fw-bold text-muted">Name</label>
                            <div class="text-gray-800 fw-bold fs-5">{{ $catalog->name }}</div>
                        </div>
                        <div class="col-lg-6">
                            <label class="fw-bold text-muted">Slug</label>
                            <div class="text-gray-600 fs-6">{{ $catalog->slug }}</div>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <div class="col-lg-12">
                            <label class="fw-bold text-muted">Description</label>
                            <div class="text-gray-600">{{ $catalog->description ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <label class="fw-bold text-muted">Created At</label>
                            <div class="text-gray-600">{{ $catalog->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::General Info-->

            <!--begin::Meta Options-->
            <div class="py-4 card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Meta Options</h2>
                    </div>
                </div>
                <div class="pt-0 card-body">
                    <div class="mb-7">
                        <label class="fw-bold text-muted">Meta Tag Title</label>
                        <div class="text-gray-600">{{ $catalog->meta_title ?? 'N/A' }}</div>
                    </div>

                    <div class="mb-7">
                        <label class="fw-bold text-muted">Meta Tag Description</label>
                        <div class="text-gray-600">{{ $catalog->meta_description ?? 'N/A' }}</div>
                    </div>

                    <div>
                        <label class="fw-bold text-muted">Meta Tag Keywords</label>
                        <div class="text-gray-600">{{ $catalog->meta_keywords ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <!--end::Meta Options-->
        </div>
        <!--end::Main column-->
    </div>

    <!--begin::Categories Card-->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <h3>Categories ({{ $catalog->categories->count() }})</h3>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('catalog.categories.create', ['catalog_id' => $catalog->id]) }}"
                    class="btn btn-sm btn-primary">
                    {!! getIcon('plus', 'fs-3', '', 'i') !!}
                    Add Category
                </a>
            </div>
        </div>

        <div class="py-4 card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="categoriesTable">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-50px">Image</th>
                            <th class="min-w-125px">Name</th>
                            <th class="min-w-100px">Products</th>
                            <th class="min-w-100px">Status</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($catalog->categories as $category)
                            <tr>
                                <td>
                                    @if ($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}"
                                            alt="{{ $category->name }}" class="rounded w-50px h-50px">
                                    @else
                                        <div class="symbol symbol-50px">
                                            <span class="symbol-label bg-light-info text-info fs-6 fw-bold">
                                                {{ substr($category->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('catalog.categories.show', $category) }}"
                                        class="text-gray-800 text-hover-primary">
                                        {{ $category->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-light-success">
                                        {{ $category->products->count() }}
                                    </span>
                                </td>
                                <td>
                                    @if ($category->is_active)
                                        <span class="badge badge-light-success">Active</span>
                                    @else
                                        <span class="badge badge-light-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('catalog.categories.show', $category) }}"
                                        class="btn btn-sm btn-light btn-active-light-primary">
                                        View
                                    </a>
                                    <a href="{{ route('catalog.categories.edit', $category) }}"
                                        class="btn btn-sm btn-light btn-active-light-primary">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center">
                                    <div class="text-gray-600">No categories found</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--end::Categories Card-->

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Load categories via AJAX
                function loadCategories() {
                    $.ajax({
                        url: "{{ route('catalog.catalogs.show', $catalog) }}",
                        type: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            if (response.success && response.data.categories) {
                                updateCategoriesTable(response.data.categories);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading categories:', xhr);
                        }
                    });
                }

                function updateCategoriesTable(categories) {
                    const tbody = $('#categoriesTable tbody');

                    if (categories.length === 0) {
                        tbody.html(`
                            <tr>
                                <td colspan="5" class="py-10 text-center">
                                    <div class="text-gray-600">No categories found</div>
                                </td>
                            </tr>
                        `);
                        return;
                    }

                    let html = '';
                    categories.forEach(category => {
                        const imageHtml = category.image ?
                            `<img src="/storage/${category.image}" alt="${category.name}" class="rounded w-50px h-50px">` :
                            `<div class="symbol symbol-50px">
                                <span class="symbol-label bg-light-info text-info fs-6 fw-bold">
                                    ${category.name.charAt(0)}
                                </span>
                            </div>`;

                        const statusBadge = category.is_active ?
                            '<span class="badge badge-light-success">Active</span>' :
                            '<span class="badge badge-light-danger">Inactive</span>';

                        html += `
                            <tr>
                                <td>${imageHtml}</td>
                                <td>
                                    <a href="/catalog/categories/${category.id}" class="text-gray-800 text-hover-primary">
                                        ${category.name}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-light-success">
                                        ${category.products_count || 0}
                                    </span>
                                </td>
                                <td>${statusBadge}</td>
                                <td class="text-end">
                                    <a href="/catalog/categories/${category.id}" class="btn btn-sm btn-light btn-active-light-primary">
                                        View
                                    </a>
                                    <a href="/catalog/categories/${category.id}/edit" class="btn btn-sm btn-light btn-active-light-primary">
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
