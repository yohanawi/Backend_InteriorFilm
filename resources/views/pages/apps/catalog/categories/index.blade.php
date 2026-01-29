<x-default-layout>

    @section('title')
        Sub Categories
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('catalog.categories.index') }}
    @endsection

    <div class="card">
        <div class="pt-6 border-0 card-header">

            <div class="card-title">
                <div class="d-flex position-relative align-items-center">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" id="searchInput" class="form-control form-control-solid w-250px ps-13"
                        placeholder="Search category" />
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end align-items-center">
                    <select class="form-select form-select-solid" id="catalogFilter" data-control="select2"
                        data-hide-search="true">
                        <option value="">All Catalogs</option>
                        @foreach ($catalogs as $catalog)
                            <option value="{{ $catalog->id }}">
                                {{ $catalog->name }}
                            </option>
                        @endforeach
                    </select>

                    <a href="{{ route('catalog.categories.create') }}" class="btn btn-primary w-300px ms-3">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add Category
                    </a>
                </div>
            </div>

        </div>

        <div class="py-4 card-body">
            <div id="categoriesTableWrapper">
                @include('pages.apps.catalog.categories.partials.table', [
                    'categories' => $categories,
                ])
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // After replacing HTML via AJAX
            document.querySelectorAll('[data-kt-menu="true"]').forEach(menuEl => {
                // Destroy previous instance if exists
                if (menuEl.__ktMenuInstance) menuEl.__ktMenuInstance.destroy();

                // Initialize new menu
                menuEl.__ktMenuInstance = new KTMenu(menuEl);
            });
            let debounceTimer = null;

            function loadCategories(page = 1) {
                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(() => {
                    const search = document.getElementById('searchInput').value;
                    const catalogId = document.getElementById('catalogFilter').value;

                    const params = new URLSearchParams({
                        search: search,
                        catalog_id: catalogId,
                        page: page
                    });

                    fetch(`{{ route('catalog.categories.index') }}?${params.toString()}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.text())
                        .then(html => {
                            // Replace table
                            document.getElementById('categoriesTableWrapper').innerHTML = html;

                            // 🔥 Reinitialize all dropdowns properly
                            document.querySelectorAll('[data-kt-menu="true"]').forEach(menuEl => {
                                // Destroy previous instance
                                if (menuEl.__ktMenuInstance) menuEl.__ktMenuInstance.destroy();

                                // Initialize new menu
                                menuEl.__ktMenuInstance = new KTMenu(menuEl);
                            });
                        });
                }, 400);
            }


            // Search input
            document.getElementById('searchInput')
                .addEventListener('input', () => loadCategories());

            document.getElementById('catalogFilter')
                .addEventListener('change', () => loadCategories());

            document.addEventListener('click', function(e) {
                const link = e.target.closest('.pagination a');
                if (!link) return;

                e.preventDefault();
                const page = new URL(link.href).searchParams.get('page');
                loadCategories(page);
            });

            // Delete function
            function deleteCategory(id) {
                Swal.fire({
                    title: 'Move to Trash?',
                    text: "This category will be moved to trash. You can restore it later!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, move to trash!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/catalog/sub_categories/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Moved to Trash!',
                                        data.message,
                                        'success'
                                    ).then(() => {
                                        window.location.reload();
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong.',
                                    'error'
                                );
                            });
                    }
                });
            }

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
