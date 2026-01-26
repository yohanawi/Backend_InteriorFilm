<x-default-layout>
    @section('title')
        Order #{{ $order->order_number }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('orders.show', $order) }}
    @endsection

    <div class="d-flex flex-column flex-lg-row gap-7 gap-lg-10">
        <!--begin::Order details-->
        <div class="flex-lg-row-fluid">
            <!--begin::Order Summary-->
            <div class="card card-flush mb-7 mb-lg-10">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Order #{{ $order->order_number }}</h2>
                    </div>
                    <div class="card-toolbar">
                        <span class="badge badge-light-{{ $statusColors[$order->status] ?? 'secondary' }} fs-5 fw-bold">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>  
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="pt-0 card-body">
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table mb-0 align-middle table-row-bordered fs-6 gy-5">
                            <thead>
                                <tr class="text-gray-700 border-bottom fs-7 fw-bold text-uppercase">
                                    <th class="min-w-175px">Product</th>
                                    <th class="min-w-100px text-end">SKU</th>
                                    <th class="min-w-70px text-end">Qty</th>
                                    <th class="min-w-100px text-end">Unit Price</th>
                                    <th class="min-w-100px text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($item->product_image)
                                                    <a href="#" class="symbol symbol-50px me-3">
                                                        <span class="symbol-label"
                                                            style="background-image:url({{ asset('storage/' . $item->product_image) }});"></span>
                                                    </a>
                                                @endif
                                                <div class="ms-3">
                                                    <div class="text-gray-800 text-hover-primary fs-6 fw-bold">
                                                        {{ $item->product_name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">{{ $item->product_sku ?? 'N/A' }}</td>
                                        <td class="text-end">{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end fw-bold">${{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" class="text-end">Subtotal</td>
                                    <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">Tax</td>
                                    <td class="text-end">${{ number_format($order->tax, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">Shipping</td>
                                    <td class="text-end">${{ number_format($order->shipping_cost, 2) }}</td>
                                </tr>
                                @if ($order->discount > 0)
                                    <tr>
                                        <td colspan="4" class="text-end">Discount</td>
                                        <td class="text-end text-danger">-${{ number_format($order->discount, 2) }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="4" class="text-gray-900 fs-3 text-end">Grand Total</td>
                                    <td class="text-gray-900 fs-3 fw-bolder text-end">
                                        ${{ number_format($order->total, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Order Summary-->

            <!--begin::Customer Details-->
            <div class="card card-flush mb-7 mb-lg-10">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Customer Details</h2>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="pt-0 card-body">
                    <div class="row">
                        <!--begin::Shipping Address-->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-gray-700 fw-bold">Shipping Address</h5>
                            <div class="text-gray-600">
                                <div class="fw-bold">{{ $order->shipping_full_name }}</div>
                                <div>{{ $order->shipping_address }}</div>
                                <div>{{ $order->shipping_city }}, {{ $order->shipping_state }}
                                    {{ $order->shipping_postal_code }}</div>
                                <div>{{ $order->shipping_country }}</div>
                                <div class="mt-2">
                                    <div>Email: {{ $order->shipping_email }}</div>
                                    <div>Phone: {{ $order->shipping_phone }}</div>
                                </div>
                            </div>
                        </div>
                        <!--end::Shipping Address-->

                        <!--begin::Billing Address-->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-gray-700 fw-bold">Billing Address</h5>
                            <div class="text-gray-600">
                                @if ($order->billing_address)
                                    <div class="fw-bold">{{ $order->billing_full_name }}</div>
                                    <div>{{ $order->billing_address }}</div>
                                    <div>{{ $order->billing_city }}, {{ $order->billing_state }}
                                        {{ $order->billing_postal_code }}</div>
                                    <div>{{ $order->billing_country }}</div>
                                    <div class="mt-2">
                                        <div>Email: {{ $order->billing_email }}</div>
                                        <div>Phone: {{ $order->billing_phone }}</div>
                                    </div>
                                @else
                                    <div class="text-muted">Same as shipping address</div>
                                @endif
                            </div>
                        </div>
                        <!--end::Billing Address-->
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Customer Details-->

            <!--begin::Order History-->
            <div class="card card-flush">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Order History</h2>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="pt-0 card-body">
                    <div class="timeline-label">
                        @foreach ($order->statusHistories as $history)
                            <div class="timeline-item">
                                <div class="text-gray-800 timeline-label fw-bold fs-6">
                                    {{ $history->created_at->format('M d, Y') }}
                                    <div class="text-gray-500 fs-7">{{ $history->created_at->format('h:i A') }}</div>
                                </div>
                                <div class="timeline-badge">
                                    <i
                                        class="fa fa-genderless text-{{ $statusColors[$history->status] ?? 'secondary' }} fs-1"></i>
                                </div>
                                <div class="text-gray-700 fw-semibold ps-3">
                                    <div class="fw-bold">{{ ucfirst($history->status) }}</div>
                                    @if ($history->comment)
                                        <div class="text-gray-600 fs-7">{{ $history->comment }}</div>
                                    @endif
                                    @if ($history->user)
                                        <div class="text-muted fs-7">by {{ $history->user->name }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Order History-->
        </div>
        <!--end::Order details-->

        <!--begin::Sidebar-->
        <div class="flex-lg-row-auto w-lg-300px">
            <!--begin::Order Info-->
            <div class="card card-flush mb-7">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Order Info</h2>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="pt-0 card-body fs-6">
                    <!--begin::Info-->
                    <div class="mb-7">
                        <div class="mb-1 text-gray-600 fw-bold">Order Date</div>
                        <div class="text-gray-800 fw-bold">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                    <!--end::Info-->

                    <!--begin::Info-->
                    <div class="mb-7">
                        <div class="mb-1 text-gray-600 fw-bold">Payment Status</div>
                        <span
                            class="badge badge-light-{{ $paymentStatusColors[$order->payment_status] ?? 'secondary' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    <!--end::Info-->

                    <!--begin::Info-->
                    <div class="mb-7">
                        <div class="mb-1 text-gray-600 fw-bold">Payment Method</div>
                        <div class="text-gray-800 fw-bold">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}
                        </div>
                    </div>
                    <!--end::Info-->

                    @if ($order->tracking_number)
                        <!--begin::Info-->
                        <div class="mb-7">
                            <div class="mb-1 text-gray-600 fw-bold">Tracking Number</div>
                            <div class="text-gray-800 fw-bold">{{ $order->tracking_number }}</div>
                        </div>
                        <!--end::Info-->
                    @endif

                    @if ($order->notes)
                        <!--begin::Info-->
                        <div class="mb-7">
                            <div class="mb-1 text-gray-600 fw-bold">Order Notes</div>
                            <div class="text-gray-800">{{ $order->notes }}</div>
                        </div>
                        <!--end::Info-->
                    @endif
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Order Info-->

            <!--begin::Actions-->
            <div class="card card-flush">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Actions</h2>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="pt-0 card-body">
                    <form action="{{ route('orders.update-status', $order) }}" method="POST" class="mb-5">
                        @csrf
                        @method('PUT')
                        <label class="form-label">Update Status</label>
                        <select name="status" class="mb-3 form-select form-select-solid">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                Processing
                            </option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed
                            </option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped
                            </option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered
                            </option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                            <option value="refunded" {{ $order->status == 'refunded' ? 'selected' : '' }}>Refunded
                            </option>
                        </select>
                        <button type="submit" class="mb-3 btn btn-primary w-100">Update Status</button>
                    </form>

                    <form action="{{ route('orders.update-payment-status', $order) }}" method="POST"
                        class="mb-5">
                        @csrf
                        @method('PUT')
                        <label class="form-label">Update Payment Status</label>
                        <select name="payment_status" class="mb-3 form-select form-select-solid">
                            <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>
                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid
                            </option>
                            <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed
                            </option>
                            <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>
                                Refunded</option>
                        </select>
                        <button type="submit" class="mb-3 btn btn-primary w-100">Update Payment</button>
                    </form>

                    @if (!$order->tracking_number)
                        <form action="{{ route('orders.add-tracking', $order) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <label class="form-label">Add Tracking Number</label>
                            <input type="text" name="tracking_number" class="mb-3 form-control form-control-solid"
                                placeholder="Enter tracking number" required>
                            <button type="submit" class="btn btn-primary w-100">Add Tracking</button>
                        </form>
                    @endif
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Sidebar-->
    </div>
</x-default-layout>
