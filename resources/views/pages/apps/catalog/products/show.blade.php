<x-default-layout>

    @section('title')
        {{ $product->name }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('catalog.products.show', $product) }}
    @endsection

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            {{-- Action Toolbar --}}
            <div class="flex-wrap d-flex flex-stack mb-7">
                <div class="d-flex align-items-center">
                    <a href="{{ route('catalog.products.index') }}" class="btn btn-sm btn-light me-3">
                        <i class="ki-outline ki-arrow-left fs-3"></i>
                        Back to Products
                    </a>
                </div>
                <div class="gap-2 d-flex">
                    <a href="{{ route('catalog.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                        <i class="ki-outline ki-pencil fs-3"></i>
                        Edit Product
                    </a> 
                    <form action="{{ route('catalog.products.destroy', $product->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="ki-outline ki-trash fs-3"></i>
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row gap-7 gap-lg-10">

                {{-- Sidebar --}}
                <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px">

                    {{-- Thumbnail --}}
                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Product Image</h2>
                            </div>
                        </div>
                        <div class="pt-0 text-center card-body">
                            @php
                                $thumbUrl = $product->thumbnail
                                    ? asset('storage/' . $product->thumbnail)
                                    : asset('assets/media/svg/files/blank-image.svg');
                            @endphp
                            <div class="mb-3">
                                <img src="{{ $thumbUrl }}" alt="{{ $product->name }}" class="rounded w-100"
                                    style="max-width: 250px;">
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Status</h2>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            @php
                                $status = $product->status ?? ($product->is_active ? 'published' : 'inactive');
                                $badgeClass = match ($status) {
                                    'published' => 'badge-light-success',
                                    'draft' => 'badge-light-warning',
                                    'scheduled' => 'badge-light-info',
                                    default => 'badge-light-danger',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} fs-5">{{ ucfirst($status) }}</span>
                        </div>
                    </div>

                    {{-- Product Details --}}
                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Product Details</h2>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <div class="mb-5">
                                <label class="form-label fw-bold">Category</label>
                                <div class="text-gray-800">
                                    {{ $product->catalog->name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="mb-5">
                                <label class="form-label fw-bold">Sub Category</label>
                                <div class="text-gray-800">
                                    {{ $product->category->name ?? 'N/A' }}
                                </div>
                            </div>
                            @if ($product->tags && count($product->tags) > 0)
                                <div class="mb-5">
                                    <label class="form-label fw-bold">Tags</label>
                                    <div class="flex-wrap gap-2 d-flex">
                                        @foreach ($product->tags as $tag)
                                            <span class="badge badge-light-primary">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">

                    {{-- General Information --}}
                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>General Information</h2>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <div class="mb-5 row">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Product Name</label>
                                    <div class="text-gray-800 fs-4">{{ $product->name }}</div>
                                </div>
                            </div>
                            <div class="mb-5 row">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">SKU</label>
                                    <div class="text-gray-800">{{ $product->sku ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Slug</label>
                                    <div class="text-gray-800">{{ $product->slug ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="mb-5 row">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Description</label>
                                    <div class="text-gray-800">
                                        {!! $product->description ?? '<span class="text-muted">No description</span>' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Product Images --}}
                    @if ($product->images && count($product->images) > 0)
                        <div class="py-4 card card-flush">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Product Gallery</h2>
                                </div>
                            </div>
                            <div class="pt-0 card-body">
                                <div class="row g-5">
                                    @foreach ($product->images as $image)
                                        <div class="col-md-3">
                                            <img src="{{ asset('storage/' . $image) }}" alt="Product Image"
                                                class="rounded w-100">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Pricing --}}
                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Pricing</h2>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <div class="mb-5 row">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Base Price</label>
                                    <div class="text-gray-800 fs-3">
                                        AED {{ number_format($product->price ?? 0, 2) }}
                                    </div>
                                </div>
                                @if ($product->discount_type && $product->discount_type !== 'none')
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Discount</label>
                                        <div class="text-gray-800">
                                            @if ($product->discount_type === 'percentage')
                                                {{ $product->discount_percentage }}%
                                            @elseif($product->discount_type === 'fixed')
                                                AED {{ number_format($product->discounted_price ?? 0, 2) }}
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if ($product->discount_type && $product->discount_type !== 'none')
                                <div class="mb-5 row">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Final Price</label>
                                        <div class="fs-3 text-success fw-bold">
                                            @php
                                                $finalPrice = $product->price;
                                                if ($product->discount_type === 'percentage') {
                                                    $finalPrice =
                                                        $product->price -
                                                        $product->price * ($product->discount_percentage / 100);
                                                } elseif ($product->discount_type === 'fixed') {
                                                    $finalPrice = $product->discounted_price;
                                                }
                                            @endphp
                                            AED {{ number_format($finalPrice, 2) }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="mb-5 row">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tax</label>
                                    <div class="text-gray-800">{{ $product->tax ?? 'No' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Inventory --}}
                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Inventory</h2>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <div class="mb-5 row">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Stock Quantity</label>
                                    <div class="text-gray-800">{{ $product->stock ?? 0 }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Barcode</label>
                                    <div class="text-gray-800">{{ $product->barcode ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Variations --}}
                    @if ($product->variations && count($product->variations) > 0)
                        <div class="py-4 card card-flush">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Product Variations</h2>
                                </div>
                            </div>
                            <div class="pt-0 card-body">
                                <div class="table-responsive">
                                    <table class="table table-row-bordered">
                                        <thead>
                                            <tr class="text-gray-800 fw-bold fs-6">
                                                <th>Type</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->variations as $variation)
                                                <tr>
                                                    <td>{{ $variation['type'] ?? 'N/A' }}</td>
                                                    <td>{{ $variation['value'] ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Shipping --}}
                    @if ($product->weight || $product->length || $product->width || $product->height)
                        <div class="py-4 card card-flush">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Shipping</h2>
                                </div>
                            </div>
                            <div class="pt-0 card-body">
                                <div class="mb-5 row">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Weight</label>
                                        <div class="text-gray-800">{{ $product->weight ?? 'N/A' }} kg</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Length</label>
                                        <div class="text-gray-800">{{ $product->length ?? 'N/A' }} cm</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Width</label>
                                        <div class="text-gray-800">{{ $product->width ?? 'N/A' }} cm</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Height</label>
                                        <div class="text-gray-800">{{ $product->height ?? 'N/A' }} cm</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- SEO --}}
                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>SEO Information</h2>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <div class="mb-5 row">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Meta Title</label>
                                    <div class="text-gray-800">{{ $product->meta_title ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="mb-5 row">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Meta Description</label>
                                    <div class="text-gray-800">{{ $product->meta_description ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Meta Keywords</label>
                                    <div class="text-gray-800">{{ $product->meta_keywords ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Timestamps --}}
                    <div class="py-4 card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Timeline</h2>
                            </div>
                        </div>
                        <div class="pt-0 card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Created At</label>
                                    <div class="text-gray-800">{{ $product->created_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Last Updated</label>
                                    <div class="text-gray-800">{{ $product->updated_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-default-layout>
