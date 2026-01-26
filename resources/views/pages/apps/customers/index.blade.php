<x-default-layout>
    @section('title')
        Customers
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('customers.index') }}
    @endsection

    <div class="card">
        <!--begin::Card header-->
        <div class="pt-6 border-0 card-header">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="my-1 d-flex align-items-center position-relative">
                    <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                    <input type="text" data-kt-customer-table-filter="search"
                        class="form-control form-control-solid w-250px ps-12" placeholder="Search Customers"
                        id="search-input" value="{{ request('search') }}" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->

            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end gap-2" data-kt-customer-table-toolbar="base">
                    <!--begin::Export-->
                    <a href="{{ route('customers.export', request()->all()) }}" class="btn btn-light-success">
                        <i class="ki-outline ki-exit-up fs-2"></i>
                        Export
                    </a>
                    <!--end::Export-->

                    <!--begin::Filter-->
                    <button type="button" class="btn btn-light-primary" data-kt-menu-trigger="click"
                        data-kt-menu-placement="bottom-end">
                        <i class="ki-outline ki-filter fs-2"></i>
                        Filter
                    </button>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                        <!--begin::Header-->
                        <div class="py-5 px-7">
                            <div class="text-gray-900 fs-5 fw-bold">Filter Options</div>
                        </div>
                        <!--end::Header-->

                        <!--begin::Separator-->
                        <div class="border-gray-200 separator"></div>
                        <!--end::Separator-->

                        <!--begin::Content-->
                        <form action="{{ route('customers.index') }}" method="GET"
                            data-kt-customer-table-filter="form">
                            <div class="py-5 px-7">
                                <!--begin::Input group-->
                                <div class="mb-5">
                                    <label class="form-label fs-6 fw-semibold">Status:</label>
                                    <select class="form-select form-select-solid fw-bold" name="status"
                                        data-placeholder="Select option" data-allow-clear="true">
                                        <option value="">All</option>
                                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="inactive"
                                            {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended"
                                            {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="mb-5">
                                    <label class="form-label fs-6 fw-semibold">Email Verified:</label>
                                    <select class="form-select form-select-solid fw-bold" name="verified">
                                        <option value="">All</option>
                                        <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="no" {{ request('verified') === 'no' ? 'selected' : '' }}>No
                                        </option>
                                    </select>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="mb-5">
                                    <label class="form-label fs-6 fw-semibold">Date Range:</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="date" name="date_from"
                                                class="form-control form-control-solid"
                                                value="{{ request('date_from') }}" placeholder="From">
                                        </div>
                                        <div class="col-6">
                                            <input type="date" name="date_to" class="form-control form-control-solid"
                                                value="{{ request('date_to') }}" placeholder="To">
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Actions-->
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('customers.index') }}"
                                        class="px-6 btn btn-light btn-active-light-primary fw-semibold me-2">
                                        Reset
                                    </a>
                                    <button type="submit" class="px-6 btn btn-primary fw-semibold">
                                        Apply
                                    </button>
                                </div>
                                <!--end::Actions-->
                            </div>
                        </form>
                        <!--end::Content-->
                    </div>
                    <!--end::Menu-->
                    <!--end::Filter-->

                    <!--begin::Add customer-->
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <i class="ki-outline ki-plus fs-2"></i>
                        Add Customer
                    </a>
                    <!--end::Add customer-->
                </div>
                <!--end::Toolbar-->
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="pt-0 card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!--begin::Bulk actions-->
            <div class="d-flex align-items-center mb-5" id="bulk-actions" style="display: none !important;">
                <div class="me-5">
                    <span id="selected-count">0</span> selected
                </div>
                <form action="{{ route('customers.bulk-update-status') }}" method="POST" class="d-inline me-2">
                    @csrf
                    <input type="hidden" name="customer_ids" id="bulk-customer-ids">
                    <select name="status" class="form-select form-select-sm d-inline w-auto me-2" required>
                        <option value="">Change Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                </form>
                <form action="{{ route('customers.bulk-delete') }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Are you sure you want to delete selected customers?');">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="customer_ids" id="bulk-customer-ids-delete">
                    <button type="submit" class="btn btn-sm btn-danger">Delete Selected</button>
                </form>
            </div>
            <!--end::Bulk actions-->

            <!--begin::Table-->
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                    <thead>
                        <tr class="text-gray-500 text-start fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="checkbox" id="select-all" />
                                </div>
                            </th>
                            <th class="min-w-125px">Customer</th>
                            <th class="min-w-125px">Email</th>
                            <th class="min-w-125px">Phone</th>
                            <th class="min-w-100px">Location</th>
                            <th class="min-w-100px">Orders</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-100px">Created Date</th>
                            <th class="text-end min-w-70px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($customers as $customer)
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input customer-checkbox" type="checkbox"
                                            value="{{ $customer->id }}" />
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="overflow-hidden symbol symbol-circle symbol-50px me-3">
                                            <div class="symbol-label fs-3 bg-light-primary text-primary">
                                                {{ $customer->initials }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('customers.show', $customer) }}"
                                                class="mb-1 text-gray-800 text-hover-primary fs-6 fw-bold">
                                                {{ $customer->full_name }}
                                            </a>
                                            @if ($customer->is_verified)
                                                <span class="badge badge-light-success badge-sm">Verified</span>
                                            @else
                                                <span class="badge badge-light-warning badge-sm">Not Verified</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $customer->email }}" class="text-gray-600 text-hover-primary">
                                        {{ $customer->email }}
                                    </a>
                                </td>
                                <td>{{ $customer->phone ?? 'N/A' }}</td>
                                <td>{{ $customer->city ? $customer->city . ', ' . $customer->country : 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-light">{{ $customer->orders->count() }} orders</span>
                                </td>
                                <td>
                                    @if ($customer->status === 'active')
                                        <span class="badge badge-light-success">Active</span>
                                    @elseif($customer->status === 'inactive')
                                        <span class="badge badge-light-warning">Inactive</span>
                                    @else
                                        <span class="badge badge-light-danger">Suspended</span>
                                    @endif
                                </td>
                                <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="#"
                                        class="btn btn-sm btn-light btn-active-light-primary btn-flex btn-center"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Actions
                                        <i class="ki-outline ki-down fs-5 ms-1"></i>
                                    </a>
                                    <!--begin::Menu-->
                                    <div class="py-4 menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px"
                                        data-kt-menu="true">
                                        <!--begin::Menu item-->
                                        <div class="px-3 menu-item">
                                            <a href="{{ route('customers.show', $customer) }}"
                                                class="px-3 menu-link">
                                                <i class="ki-outline ki-eye fs-5 me-2"></i>
                                                View
                                            </a>
                                        </div>
                                        <!--end::Menu item-->

                                        <!--begin::Menu item-->
                                        <div class="px-3 menu-item">
                                            <a href="{{ route('customers.edit', $customer) }}"
                                                class="px-3 menu-link">
                                                <i class="ki-outline ki-pencil fs-5 me-2"></i>
                                                Edit
                                            </a>
                                        </div>
                                        <!--end::Menu item-->

                                        @if (!$customer->is_verified)
                                            <!--begin::Menu item-->
                                            <div class="px-3 menu-item">
                                                <form action="{{ route('customers.verify-email', $customer) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="p-0 px-3 menu-link btn btn-link w-100 text-start">
                                                        <i class="ki-outline ki-verify fs-5 me-2"></i>
                                                        Verify Email
                                                    </button>
                                                </form>
                                            </div>
                                            <!--end::Menu item-->
                                        @endif

                                        <!--begin::Menu item-->
                                        <div class="px-3 menu-item">
                                            <form action="{{ route('customers.destroy', $customer) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-0 px-3 menu-link btn btn-link text-danger w-100 text-start">
                                                    <i class="ki-outline ki-trash fs-5 me-2"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                        <!--end::Menu item-->
                                    </div>
                                    <!--end::Menu-->
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-10">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ki-outline ki-user-square fs-3x text-gray-400 mb-3"></i>
                                        <span class="text-gray-600 fs-5">No customers found.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!--end::Table-->

            <!--begin::Pagination-->
            <div class="d-flex justify-content-between align-items-center mt-5">
                <div class="text-gray-600">
                    Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of
                    {{ $customers->total() }} customers
                </div>
                <div>
                    {{ $customers->links() }}
                </div>
            </div>
            <!--end::Pagination-->
        </div>
        <!--end::Card body-->
    </div>

    @push('scripts')
        <script>
            // Search functionality
            document.getElementById('search-input').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    const url = new URL(window.location.href);
                    url.searchParams.set('search', this.value);
                    window.location.href = url.toString();
                }
            });

            // Select all checkboxes
            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.customer-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });

            // Individual checkbox change
            document.querySelectorAll('.customer-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });

            function updateBulkActions() {
                const checkboxes = document.querySelectorAll('.customer-checkbox:checked');
                const bulkActions = document.getElementById('bulk-actions');
                const selectedCount = document.getElementById('selected-count');

                if (checkboxes.length > 0) {
                    bulkActions.style.display = 'flex !important';
                    bulkActions.classList.remove('d-none');
                    bulkActions.classList.add('d-flex');
                    selectedCount.textContent = checkboxes.length;

                    // Update hidden inputs with selected IDs
                    const ids = Array.from(checkboxes).map(cb => cb.value);
                    document.getElementById('bulk-customer-ids').value = JSON.stringify(ids);
                    document.getElementById('bulk-customer-ids-delete').value = JSON.stringify(ids);
                } else {
                    bulkActions.style.display = 'none !important';
                    bulkActions.classList.add('d-none');
                    bulkActions.classList.remove('d-flex');
                }
            }
        </script>
    @endpush
</x-default-layout>
