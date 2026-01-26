<x-default-layout>
    @section('title')
        Orders
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('orders.index') }}
    @endsection

    <div class="card">
        <!--begin::Card header-->
        <div class="pt-6 border-0 card-header">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="my-1 d-flex align-items-center position-relative">
                    <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                    <input type="text" class="form-control form-control-solid w-250px ps-12" placeholder="Search Orders"
                        id="search-input" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->

            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end">
                    <!--begin::Filter-->
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
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
                        <div class="py-5 px-7" id="filter-form">
                            <!--begin::Input group-->
                            <div class="mb-5">
                                <label class="form-label fs-6 fw-semibold">Order Status:</label>
                                <select class="form-select form-select-solid fw-bold" id="status-filter">
                                    <option value="all">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-semibold">Payment Status:</label>
                                <select class="form-select form-select-solid fw-bold" id="payment-status-filter">
                                    <option value="all">All Payment Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="failed">Failed</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Actions-->
                            <div class="d-flex justify-content-end">
                                <button type="button" id="reset-filter"
                                    class="px-6 btn btn-light btn-active-light-primary fw-semibold me-2">Reset</button>
                                <button type="button" id="apply-filter"
                                    class="px-6 btn btn-primary fw-semibold">Apply</button>
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Menu-->
                    <!--end::Filter-->

                    <!--begin::Export-->
                    <button type="button" class="btn btn-light-primary me-3" id="export-btn">
                        <i class="ki-outline ki-exit-up fs-2"></i>
                        Export
                    </button>
                    <!--end::Export-->
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

            <!--begin::Table-->
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="orders-table">
                    <thead>
                        <tr class="text-gray-500 text-start fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Order Number</th>
                            <th class="min-w-125px">Customer</th>
                            <th class="min-w-100px">Date</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-100px">Payment</th>
                            <th class="text-end min-w-100px">Total</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize DataTable
                const table = $('#orders-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('orders.index') }}",
                        data: function(d) {
                            d.status = $('#status-filter').val();
                            d.payment_status = $('#payment-status-filter').val();
                        }
                    },
                    columns: [{
                            data: 'order_number',
                            name: 'order_number',
                            render: function(data, type, row) {
                                return '<a href="/orders/' + row.id +
                                    '" class="text-gray-800 text-hover-primary fw-bold">' + data +
                                    '</a>';
                            }
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name',
                            render: function(data, type, row) {
                                return '<div class="d-flex flex-column">' +
                                    '<span class="text-gray-800 fw-bold">' + data + '</span>' +
                                    '<span class="text-muted fs-7">' + row.customer_email + '</span>' +
                                    '</div>';
                            }
                        },
                        {
                            data: 'formatted_date',
                            name: 'created_at',
                            orderable: true
                        },
                        {
                            data: 'status_badge',
                            name: 'status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'payment_status_badge',
                            name: 'payment_status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'formatted_total',
                            name: 'total_amount',
                            className: 'text-end fw-bold',
                            orderable: true
                        },
                        {
                            data: 'action',
                            name: 'action',
                            className: 'text-end',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [2, 'desc']
                    ], // Sort by date descending
                    pageLength: 25,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    dom: '<"top"f>rt<"bottom"lip><"clear">',
                    language: {
                        emptyTable: "No orders found",
                        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                    }
                });

                // Custom search
                $('#search-input').on('keyup', function() {
                    table.search(this.value).draw();
                });

                // Apply filter
                $('#apply-filter').on('click', function() {
                    table.ajax.reload();
                });

                // Reset filter
                $('#reset-filter').on('click', function() {
                    $('#status-filter').val('all');
                    $('#payment-status-filter').val('all');
                    table.ajax.reload();
                });

                // Export functionality
                $('#export-btn').on('click', function() {
                    window.location.href = "{{ route('orders.export') }}";
                });
            });
        </script>
    @endpush
</x-default-layout>
