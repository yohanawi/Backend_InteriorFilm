<x-default-layout>
    @section('title')
        Customer Details
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('customers.show', $customer) }}
    @endsection

    <!--begin::Row-->
    <div class="row g-5 g-xl-8">
        <!--begin::Col-->
        <div class="col-xl-4">
            <!--begin::Card-->
            <div class="card card-flush h-xl-100">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Customer Information</h2>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-primary">
                            <i class="ki-outline ki-pencil fs-3"></i>
                            Edit
                        </a>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body pt-5">
                    <!--begin::User-->
                    <div class="d-flex flex-column text-center mb-7">
                        <div class="symbol symbol-100px symbol-circle mb-5 mx-auto">
                            <div class="symbol-label fs-2x fw-bold bg-light-primary text-primary">
                                {{ $customer->initials }}
                            </div>
                        </div>
                        <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1">
                            {{ $customer->full_name }}
                        </a>
                        <div class="fs-5 fw-semibold text-muted mb-3">
                            {{ $customer->email }}
                        </div>
                        <div class="mb-3">
                            @if ($customer->status === 'active')
                                <span class="badge badge-light-success fs-7 fw-bold">Active</span>
                            @elseif($customer->status === 'inactive')
                                <span class="badge badge-light-warning fs-7 fw-bold">Inactive</span>
                            @else
                                <span class="badge badge-light-danger fs-7 fw-bold">Suspended</span>
                            @endif

                            @if ($customer->is_verified)
                                <span class="badge badge-light-success fs-7 fw-bold">Email Verified</span>
                            @else
                                <span class="badge badge-light-warning fs-7 fw-bold">Not Verified</span>
                            @endif
                        </div>
                    </div>
                    <!--end::User-->

                    <!--begin::Stats-->
                    <div class="d-flex flex-stack fs-4 py-3 border-bottom border-gray-300">
                        <div class="fw-bold">Total Orders</div>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-light-primary fs-7 fw-bold">{{ $totalOrders }}</span>
                        </div>
                    </div>
                    <div class="d-flex flex-stack fs-4 py-3 border-bottom border-gray-300">
                        <div class="fw-bold">Total Spent</div>
                        <div class="d-flex align-items-center">
                            <span class="text-gray-800 fw-bold">${{ number_format($totalSpent, 2) }}</span>
                        </div>
                    </div>
                    <div class="d-flex flex-stack fs-4 py-3">
                        <div class="fw-bold">Wishlist Items</div>
                        <div class="d-flex align-items-center">
                            <span
                                class="badge badge-light fs-7 fw-bold">{{ $customer->wishlistProducts->count() }}</span>
                        </div>
                    </div>
                    <!--end::Stats-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col-xl-8">
            <!--begin::Card-->
            <div class="card card-flush mb-5">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Personal Details</h2>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Details-->
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">Full Name</label>
                        <div class="col-lg-8">
                            <span class="text-gray-800 fw-bold fs-6">{{ $customer->full_name }}</span>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">Email</label>
                        <div class="col-lg-8">
                            <a href="mailto:{{ $customer->email }}" class="text-gray-800 fw-semibold fs-6">
                                {{ $customer->email }}
                            </a>
                            @if ($customer->is_verified)
                                <span class="badge badge-light-success ms-2">Verified</span>
                            @else
                                <span class="badge badge-light-warning ms-2">Not Verified</span>
                                <form action="{{ route('customers.verify-email', $customer) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light-primary ms-2">
                                        Verify Now
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">Phone</label>
                        <div class="col-lg-8">
                            <span class="text-gray-800 fw-bold fs-6">{{ $customer->phone ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">Date of Birth</label>
                        <div class="col-lg-8">
                            <span class="text-gray-800 fw-semibold fs-6">
                                @if ($customer->date_of_birth)
                                    {{ $customer->date_of_birth->format('M d, Y') }}
                                    <span class="text-muted">({{ $customer->age }} years old)</span>
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">Address</label>
                        <div class="col-lg-8">
                            <span class="text-gray-800 fw-semibold fs-6">
                                {{ $customer->full_address ?: 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">Created At</label>
                        <div class="col-lg-8">
                            <span class="text-gray-800 fw-semibold fs-6">
                                {{ $customer->created_at->format('M d, Y H:i A') }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">Last Updated</label>
                        <div class="col-lg-8">
                            <span class="text-gray-800 fw-semibold fs-6">
                                {{ $customer->updated_at->format('M d, Y H:i A') }}
                            </span>
                        </div>
                    </div>
                    <!--end::Details-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->

            <!--begin::Card - Recent Orders-->
            <div class="card card-flush">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Recent Orders</h2>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body pt-0">
                    @if ($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th class="min-w-120px">Order ID</th>
                                        <th class="min-w-100px">Date</th>
                                        <th class="min-w-100px">Total</th>
                                        <th class="min-w-100px">Status</th>
                                        <th class="min-w-100px text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('orders.show', $order) }}"
                                                    class="text-gray-800 fw-bold text-hover-primary">
                                                    #{{ $order->order_number ?? $order->id }}
                                                </a>
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                            <td>${{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-light-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('orders.show', $order) }}"
                                                    class="btn btn-sm btn-light">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($totalOrders > 5)
                            <div class="text-center mt-5">
                                <a href="{{ route('orders.index', ['customer' => $customer->id]) }}"
                                    class="btn btn-light-primary">
                                    View All Orders ({{ $totalOrders }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-10">
                            <i class="ki-outline ki-package fs-3x text-gray-400 mb-3"></i>
                            <p class="text-gray-600 fs-5">No orders yet</p>
                        </div>
                    @endif
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
</x-default-layout>
