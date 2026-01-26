<x-default-layout>

    @section('title')
        Products
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('catalog.products.index') }}
    @endsection

    <div class="card">
        <!--begin::Card header-->
        <div class="pt-6 border-0 card-header">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="my-1 d-flex align-items-center position-relative">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" class="form-control form-control-solid w-250px ps-13"
                        placeholder="Search product" id="searchInput" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->

            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="gap-3 d-flex justify-content-end align-items-center">
                    <!--begin::Catalog Filter-->
                    <select class="form-select form-select-solid w-150px" id="catalogFilter" data-control="select2">
                        <option value="">All Catalogs</option>
                        @foreach ($catalogs as $catalog)
                            <option value="{{ $catalog->id }}" @selected(request('catalog_id') == $catalog->id)>
                                {{ $catalog->name }}
                            </option>
                        @endforeach
                    </select>
                    <!--end::Catalog Filter-->

                    <!--begin::Category Filter-->
                    <select class="form-select form-select-solid w-150px" id="categoryFilter" data-control="select2">
                        <option value="">All Categories</option>
                        @foreach ($catalogs as $catalog)
                            @foreach ($catalog->categories as $category)
                                <option value="{{ $category->id }}" data-catalog="{{ $catalog->id }}"
                                    @selected(request('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                    <!--end::Category Filter-->

                    <!--begin::Status Filter-->
                    <select class="form-select form-select-solid w-150px" id="statusFilter" data-control="select2">
                        <option value="">All Status</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <!--end::Status Filter-->

                    <!--begin::Add product-->
                    <a href="{{ route('catalog.products.create') }}" class="btn btn-primary">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add Product
                    </a>
                    <!--end::Add product-->
                </div>
                <!--end::Toolbar-->
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="py-4 card-body">
            <!--begin::Loading-->
            <div id="loadingIndicator" class="py-10 text-center d-none">
                <span class="align-middle spinner-border spinner-border-lg me-2"></span>
                <span>Loading products...</span>
            </div>
            <!--end::Loading-->

            <!--begin::Table-->
            <div class="table-responsive" id="productsTableContainer">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="productsTable">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-50px">Image</th>
                            <th class="cursor-pointer min-w-100px" data-sort="name">
                                Name
                                <i class="ki-outline ki-sort-up-down fs-6 ms-1"></i>
                            </th>
                            <th class="min-w-100px">Category</th>
                            <th class="min-w-100px">Catalog</th>
                            <th class="cursor-pointer text-end min-w-70px" data-sort="price">
                                Price
                                <i class="ki-outline ki-sort-up-down fs-6 ms-1"></i>
                            </th>
                            <th class="text-end min-w-70px">Stock</th>
                            <th class="text-center min-w-70px">Status</th>
                            <th class="text-center cursor-pointer min-w-70px" data-sort="sort_order">
                                Sort
                                <i class="ki-outline ki-sort-up-down fs-6 ms-1"></i>
                            </th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold" id="productsTableBody">
                        <!-- Ajax content will be loaded here -->
                    </tbody>
                </table>
            </div>
            <!--end::Table-->

            <!--begin::No Results-->
            <div id="noResults" class="py-10 text-center d-none">
                <div class="py-10">
                    <i class="ki-outline ki-information-5 fs-3x text-muted"></i>
                    <h3 class="mt-5">No products found</h3>
                    <p class="text-muted">Try adjusting your search or filter to find what you're looking for.</p>
                </div>
            </div>
            <!--end::No Results-->

            <!--begin::Pagination-->
            <div class="mt-5 d-flex justify-content-between align-items-center" id="paginationContainer">
                <div class="text-muted fs-7" id="paginationInfo"></div>
                <ul class="pagination" id="paginationControls"></ul>
            </div>
            <!--end::Pagination-->
        </div>
        <!--end::Card body-->
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Product management with Ajax
            const ProductManager = {
                currentPage: 1,
                perPage: 15,
                sortField: 'sort_order',
                sortDirection: 'asc',
                searchTerm: '',
                catalogId: '',
                categoryId: '',
                statusFilter: '',

                init() {
                    this.bindEvents();
                    this.loadProducts();
                },

                bindEvents() {
                    // Search with debounce
                    let searchTimeout;
                    document.getElementById('searchInput').addEventListener('input', (e) => {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            this.searchTerm = e.target.value;
                            this.currentPage = 1;
                            this.loadProducts();
                        }, 500);
                    });

                    // Catalog filter
                    document.getElementById('catalogFilter').addEventListener('change', (e) => {
                        this.catalogId = e.target.value;
                        this.currentPage = 1;
                        this.filterCategoriesByCatalog();
                        this.loadProducts();
                    });

                    // Category filter
                    document.getElementById('categoryFilter').addEventListener('change', (e) => {
                        this.categoryId = e.target.value;
                        this.currentPage = 1;
                        this.loadProducts();
                    });

                    // Status filter
                    document.getElementById('statusFilter').addEventListener('change', (e) => {
                        this.statusFilter = e.target.value;
                        this.currentPage = 1;
                        this.loadProducts();
                    });

                    // Sortable columns
                    document.querySelectorAll('[data-sort]').forEach(th => {
                        th.addEventListener('click', () => {
                            const field = th.getAttribute('data-sort');
                            if (this.sortField === field) {
                                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                            } else {
                                this.sortField = field;
                                this.sortDirection = 'asc';
                            }
                            this.loadProducts();
                        });
                    });
                },

                filterCategoriesByCatalog() {
                    const categoryFilter = document.getElementById('categoryFilter');
                    const catalogId = this.catalogId;
                    const options = categoryFilter.querySelectorAll('option[data-catalog]');

                    options.forEach(option => {
                        if (!catalogId || option.dataset.catalog == catalogId) {
                            option.style.display = 'block';
                            option.disabled = false;
                        } else {
                            option.style.display = 'none';
                            option.disabled = true;
                        }
                    });

                    // Reset category if it doesn't match catalog
                    if (catalogId && categoryFilter.value) {
                        const selectedOption = categoryFilter.querySelector(
                            `option[value="${categoryFilter.value}"]`);
                        if (selectedOption && selectedOption.dataset.catalog != catalogId) {
                            categoryFilter.value = '';
                            $(categoryFilter).trigger('change');
                        }
                    }
                },

                async loadProducts() {
                    const loadingIndicator = document.getElementById('loadingIndicator');
                    const tableBody = document.getElementById('productsTableBody');
                    const noResults = document.getElementById('noResults');

                    // Show loading
                    loadingIndicator.classList.remove('d-none');
                    tableBody.innerHTML = '';
                    noResults.classList.add('d-none');

                    try {
                        const params = new URLSearchParams({
                            page: this.currentPage,
                            per_page: this.perPage,
                            sort_field: this.sortField,
                            sort_direction: this.sortDirection,
                            search: this.searchTerm,
                            catalog_id: this.catalogId,
                            category_id: this.categoryId,
                            status: this.statusFilter
                        });

                        const response = await fetch(`{{ route('catalog.products.index') }}?${params}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) throw new Error('Failed to load products');

                        const data = await response.json();

                        loadingIndicator.classList.add('d-none');

                        if (data.data && data.data.length > 0) {
                            this.renderProducts(data.data);
                            this.renderPagination(data.pagination);
                        } else {
                            noResults.classList.remove('d-none');
                            document.getElementById('paginationContainer').classList.add('d-none');
                        }
                    } catch (error) {
                        console.error('Error loading products:', error);
                        loadingIndicator.classList.add('d-none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load products. Please try again.'
                        });
                    }
                },

                renderProducts(products) {
                    const tableBody = document.getElementById('productsTableBody');
                    tableBody.innerHTML = '';

                    products.forEach(product => {
                        const row = document.createElement('tr');

                        // Thumbnail
                        const thumbnail = product.thumbnail ?
                            `<img src="/storage/${product.thumbnail}" alt="${product.name}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">` :
                            `<div class="symbol symbol-50px"><span class="symbol-label bg-light-primary text-primary">${product.name.charAt(0).toUpperCase()}</span></div>`;

                        // Status badge
                        let statusBadge = '';
                        switch (product.status) {
                            case 'published':
                                statusBadge =
                                    '<span class="badge badge-light-success">Published</span>';
                                break;
                            case 'draft':
                                statusBadge = '<span class="badge badge-light-warning">Draft</span>';
                                break;
                            case 'scheduled':
                                statusBadge = '<span class="badge badge-light-info">Scheduled</span>';
                                break;
                            case 'inactive':
                                statusBadge = '<span class="badge badge-light-danger">Inactive</span>';
                                break;
                        }

                        // Stock info
                        const totalStock = product.stock_warehouse || 0;
                        const stockClass = totalStock > 0 ? 'text-success' : 'text-danger';

                        row.innerHTML = `
                            <td>${thumbnail}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <a href="/catalog/products/${product.id}/edit" class="mb-1 text-gray-800 text-hover-primary fw-bold">
                                        ${product.name}
                                    </a>
                                    ${product.sku ? `<span class="text-muted fs-7">SKU: ${product.sku}</span>` : ''}
                                </div>
                            </td>
                            <td>
                                ${product.category ? `<span class="badge badge-light">${product.category.name}</span>` : 'N/A'}
                            </td>
                            <td>
                                ${product.catalog ? `<span class="badge badge-light-primary">${product.catalog.name}</span>` : 
                                  (product.category && product.category.catalog ? `<span class="badge badge-light-primary">${product.category.catalog.name}</span>` : 'N/A')}
                            </td>
                            <td class="text-end">
                                <span class="fw-bold">${product.price ? '$' + parseFloat(product.price).toFixed(2) : 'N/A'}</span>
                                ${product.discount_value && product.discount_type !== 'none' ? 
                                    `<br><span class="badge badge-sm badge-light-danger">
                                                        ${product.discount_type === 'percentage' ? product.discount_value + '%' : '$' + product.discount_value} OFF
                                                    </span>` : ''}
                            </td>
                            <td class="text-end ${stockClass}">
                                <span class="fw-bold">${totalStock}</span>
                            </td>
                            <td class="text-center">${statusBadge}</td>
                            <td class="text-center">
                                <span class="badge badge-light-info">${product.sort_order || 0}</span>
                            </td>
                            <td class="text-end">
                                <a href="#" class="btn btn-sm btn-icon btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <i class="ki-outline ki-dots-horizontal fs-2"></i>
                                </a>
                                <div class="py-4 menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px" data-kt-menu="true">
                               
                                    <div class="px-3 menu-item">
                                        <a href="/catalog/products/${product.id}/edit" class="px-3 menu-link">
                                            Edit
                                        </a>
                                    </div>
                                    <div class="px-3 menu-item">
                                        <a href="#" class="px-3 menu-link text-danger" onclick="ProductManager.deleteProduct(${product.id}, '${product.name}'); return false;">
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        `;

                        tableBody.appendChild(row);
                    });

                    // Initialize KTMenu for dropdowns
                    KTMenu.createInstances();
                },

                renderPagination(pagination) {
                    const paginationContainer = document.getElementById('paginationContainer');
                    const paginationInfo = document.getElementById('paginationInfo');
                    const paginationControls = document.getElementById('paginationControls');

                    paginationContainer.classList.remove('d-none');

                    // Pagination info
                    paginationInfo.textContent =
                        `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total} products`;

                    // Pagination controls
                    paginationControls.innerHTML = '';

                    if (pagination.last_page > 1) {
                        // Previous button
                        const prevLi = document.createElement('li');
                        prevLi.className = `page-item ${pagination.current_page === 1 ? 'disabled' : ''}`;
                        prevLi.innerHTML =
                            `<a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>`;
                        paginationControls.appendChild(prevLi);

                        // Page numbers
                        const maxPages = 5;
                        let startPage = Math.max(1, pagination.current_page - Math.floor(maxPages / 2));
                        let endPage = Math.min(pagination.last_page, startPage + maxPages - 1);

                        if (endPage - startPage < maxPages - 1) {
                            startPage = Math.max(1, endPage - maxPages + 1);
                        }

                        if (startPage > 1) {
                            const firstLi = document.createElement('li');
                            firstLi.className = 'page-item';
                            firstLi.innerHTML = `<a class="page-link" href="#" data-page="1">1</a>`;
                            paginationControls.appendChild(firstLi);

                            if (startPage > 2) {
                                const dotsLi = document.createElement('li');
                                dotsLi.className = 'page-item disabled';
                                dotsLi.innerHTML = `<span class="page-link">...</span>`;
                                paginationControls.appendChild(dotsLi);
                            }
                        }

                        for (let i = startPage; i <= endPage; i++) {
                            const pageLi = document.createElement('li');
                            pageLi.className = `page-item ${i === pagination.current_page ? 'active' : ''}`;
                            pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                            paginationControls.appendChild(pageLi);
                        }

                        if (endPage < pagination.last_page) {
                            if (endPage < pagination.last_page - 1) {
                                const dotsLi = document.createElement('li');
                                dotsLi.className = 'page-item disabled';
                                dotsLi.innerHTML = `<span class="page-link">...</span>`;
                                paginationControls.appendChild(dotsLi);
                            }

                            const lastLi = document.createElement('li');
                            lastLi.className = 'page-item';
                            lastLi.innerHTML =
                                `<a class="page-link" href="#" data-page="${pagination.last_page}">${pagination.last_page}</a>`;
                            paginationControls.appendChild(lastLi);
                        }

                        // Next button
                        const nextLi = document.createElement('li');
                        nextLi.className =
                            `page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}`;
                        nextLi.innerHTML =
                            `<a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>`;
                        paginationControls.appendChild(nextLi);

                        // Add click event to pagination links
                        paginationControls.querySelectorAll('a[data-page]').forEach(link => {
                            link.addEventListener('click', (e) => {
                                e.preventDefault();
                                const page = parseInt(link.getAttribute('data-page'));
                                if (page > 0 && page <= pagination.last_page) {
                                    this.currentPage = page;
                                    this.loadProducts();
                                }
                            });
                        });
                    }
                },

                async deleteProduct(id, name) {
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: `You are about to delete "${name}". This action cannot be undone!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    });

                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/catalog/products/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: data.message || 'Product has been deleted.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                this.loadProducts();
                            } else {
                                throw new Error(data.message || 'Failed to delete product');
                            }
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'Failed to delete product. Please try again.'
                            });
                        }
                    }
                }
            };

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                ProductManager.init();
            });

            // Show success message if present
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        </script>
    @endpush

</x-default-layout>
