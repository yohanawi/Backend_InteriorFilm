<x-default-layout>
    @section('title')
        Wrapping Areas Management
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('wrapping-areas.index') }}
    @endsection

    <div class="card">
        <div class="border-0 card-header">
            <div class="card-title">
                <div class="my-1 d-flex align-items-center position-relative">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" data-kt-wrapping-table-filter="search"
                        class="form-control form-control-solid w-250px ps-13" placeholder="Search wrapping areas" />
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-wrapping-table-toolbar="base">
                    <a href="{{ route('wrapping-areas.create') }}" class="btn btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Add Wrapping Area
                    </a>
                </div>
            </div>
        </div>

        <div class="py-4 card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_wrapping_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-125px">Title</th>
                        <th class="min-w-125px">Slug</th>
                        <th class="min-w-100px">Status</th>
                        <th class="min-w-100px">Product Count</th>
                        <th class="min-w-100px">Sort Order</th>
                        <th class="text-end min-w-100px">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @forelse($wrappingAreas as $area)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if ($area->main_image)
                                        <div class="symbol symbol-50px me-3">
                                            <img src="{{ $area->main_image }}" alt="{{ $area->title }}" />
                                        </div>
                                    @endif
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('wrapping-areas.show', $area) }}"
                                            class="mb-1 text-gray-800 text-hover-primary">
                                            {{ $area->title }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-info">{{ $area->slug }}</span>
                            </td>
                            <td>
                                @if ($area->is_active)
                                    <span class="badge badge-light-success">Active</span>
                                @else
                                    <span class="badge badge-light-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $area->product_count }}</td>
                            <td>{{ $area->sort_order }}</td>
                            <td class="text-end">
                                <a href="#"
                                    class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm"
                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    Actions
                                    <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                </a>
                                <div class="py-4 menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px"
                                    data-kt-menu="true">
                                    <div class="px-3 menu-item">
                                        <a href="{{ route('wrapping-areas.show', $area) }}" class="px-3 menu-link">
                                            View
                                        </a>
                                    </div>
                                    <div class="px-3 menu-item">
                                        <a href="{{ route('wrapping-areas.edit', $area) }}" class="px-3 menu-link">
                                            Edit
                                        </a>
                                    </div>
                                    <div class="px-3 menu-item">
                                        <a href="#" class="px-3 menu-link"
                                            data-kt-wrapping-table-filter="delete_row" data-id="{{ $area->slug }}">
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center">
                                <div class="text-gray-600">No wrapping areas found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="flex-wrap d-flex justify-content-between align-items-center">
                <div class="py-3 d-flex align-items-center">
                    Showing {{ $wrappingAreas->firstItem() ?? 0 }} to {{ $wrappingAreas->lastItem() ?? 0 }} of
                    {{ $wrappingAreas->total() }} entries
                </div>
                {{ $wrappingAreas->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Toggle active status
                $('.toggle-active').on('change', function() {
                    const id = $(this).data('id');
                    const isChecked = $(this).is(':checked');

                    $.ajax({
                        url: `{{ url('/wrapping-areas') }}/${id}/toggle-active`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            toastr.success('Status updated successfully');
                        },
                        error: function() {
                            toastr.error('Failed to update status');
                            $(this).prop('checked', !isChecked);
                        }
                    });
                });

                // Delete row
                $('[data-kt-wrapping-table-filter="delete_row"]').on('click', function(e) {
                    e.preventDefault();
                    const slug = $(this).data('id');
                    Swal.fire({
                        text: "Are you sure you want to delete this wrapping area?",
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Yes, delete!",
                        cancelButtonText: "No, cancel",
                        customClass: {
                            confirmButton: "btn fw-bold btn-danger",
                            cancelButton: "btn fw-bold btn-active-light-primary"
                        }
                    }).then(function(result) {
                        if (result.value) {
                            $.ajax({
                                url: `/wrapping-areas/${slug}`,
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function() {
                                    Swal.fire({
                                        text: "Wrapping area deleted successfully.",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary",
                                        }
                                    }).then(function() {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        text: "Failed to delete wrapping area. Error: " +
                                            (xhr.responseJSON?.message || xhr
                                                .statusText),
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary",
                                        }
                                    });
                                }
                            });
                        }
                    });
                });
                // Search functionality
                $('[data-kt-wrapping-table-filter="search"]').on('keyup', function() {
                    const value = $(this).val().toLowerCase();
                    $('#kt_wrapping_table tbody tr').filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                    });
                });
            });
        </script>
    @endpush
</x-default-layout>
