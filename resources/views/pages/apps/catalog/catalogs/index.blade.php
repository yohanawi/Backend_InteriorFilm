<x-default-layout>

    @section('title')
        Categories
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('catalog.catalogs.index') }}
    @endsection

    <div class="card">
        <div class="pt-6 border-0 card-header">
            <div class="card-title">
                <div class="my-1 d-flex align-items-center position-relative">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" class="form-control form-control-solid w-250px ps-13"
                        placeholder="Search Categories" id="searchInput" />
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('catalog.catalogs.create') }}" class="btn btn-primary">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add Categories
                    </a>
                </div>
            </div>
        </div>

        <div class="py-4 card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="catalogsTable">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-50px">Image</th>
                            <th class="min-w-125px">Name</th>
                            <th class="min-w-100px">Categories</th>
                            <th class="min-w-100px">Products</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-100px">Sort Order</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($catalogs as $catalog)
                            <tr>
                                <td>
                                    @if ($catalog->image)
                                        <img src="{{ asset('storage/' . $catalog->image) }}" alt="{{ $catalog->name }}"
                                            class="rounded w-50px h-50px">
                                    @else
                                        <div class="symbol symbol-50px">
                                            <span class="symbol-label bg-light-primary text-primary fs-6 fw-bold">
                                                {{ substr($catalog->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('catalog.catalogs.show', $catalog) }}"
                                        class="mb-1 text-gray-800 text-hover-primary">
                                        {{ $catalog->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-light-info">
                                        {{ $catalog->categories_count }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light-success">
                                        {{ $catalog->products_count }}
                                    </span>
                                </td>
                                <td>
                                    @if ($catalog->is_active)
                                        <span class="badge badge-light-success">Active</span>
                                    @else
                                        <span class="badge badge-light-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $catalog->sort_order }}</td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-light btn-active-light-primary btn-sm"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Actions
                                        {!! getIcon('down', 'fs-5 m-0') !!}
                                    </a>
                                    <div class="py-4 menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px"
                                        data-kt-menu="true">
                                        <div class="px-3 menu-item">
                                            <a href="{{ route('catalog.catalogs.show', $catalog) }}"
                                                class="px-3 menu-link">
                                                View
                                            </a>
                                        </div>
                                        <div class="px-3 menu-item">
                                            <a href="{{ route('catalog.catalogs.edit', $catalog) }}"
                                                class="px-3 menu-link">
                                                Edit
                                            </a>
                                        </div>
                                        <div class="px-3 menu-item">
                                            <a href="#" class="px-3 menu-link"
                                                onclick="deleteCatalog({{ $catalog->id }}); return false;">
                                                Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center">
                                    <div class="text-gray-600">No catalogs found</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $catalogs->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Search functionality
            document.getElementById('searchInput').addEventListener('keyup', function() {
                let searchValue = this.value.toLowerCase();
                let tableRows = document.querySelectorAll('#catalogsTable tbody tr');

                tableRows.forEach(row => {
                    let text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchValue) ? '' : 'none';
                });
            });

            // Delete function
            function deleteCatalog(id) {
                Swal.fire({
                    title: 'Move to Trash?',
                    text: "This catalog will be moved to trash. You can restore it later!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, move to trash!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/catalog/categories/${id}`, {
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
