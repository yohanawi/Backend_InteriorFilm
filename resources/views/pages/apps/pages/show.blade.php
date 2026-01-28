<x-default-layout>
    @section('title')
        {{ $page->title }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('pages.show', $page) }}
    @endsection

    {{-- Page Header --}}
    <div class="card card-flush mb-xl-3">
        <div class="pb-0 card-body pt-9">
            {{-- Back Button & Actions --}}
            <div class="flex-wrap d-flex flex-sm-nowrap">
                <a href="{{ route('pages.index') }}" class="btn btn-sm btn-icon btn-active-color-primary me-3">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </a>

                <div class="flex-grow-1">
                    <div class="flex-wrap mb-2 d-flex justify-content-between align-items-start">
                        {{-- Page Title & Status --}}
                        <div class="d-flex flex-column">
                            <div class="mb-2 d-flex align-items-center">
                                <span class="text-gray-900 fs-2 fw-bold me-3">{{ $page->title }}</span>
                                @if ($page->status == 'published')
                                    <span class="badge badge-light-success fs-7 fw-semibold">
                                        <i class="ki-duotone ki-check-circle fs-5 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Published
                                    </span>
                                @elseif($page->status == 'draft')
                                    <span class="badge badge-light-warning fs-7 fw-semibold">
                                        <i class="ki-duotone ki-pencil fs-5 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Draft
                                    </span>
                                @else
                                    <span class="badge badge-light-secondary fs-7 fw-semibold">
                                        <i class="ki-duotone ki-archive fs-5 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Archived
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Row --}}
            <div class="mb-5 overflow-auto d-flex h-55px">
                <ul class="border-transparent nav nav-stretch nav-line-tabs nav-line-tabs-2x fs-5 fw-bold flex-nowrap">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6 active" data-bs-toggle="tab"
                            href="#kt_tab_overview">Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6" data-bs-toggle="tab"
                            href="#kt_tab_content">Content</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#kt_tab_seo">SEO
                            Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#kt_tab_preview">Search
                            Preview</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-6 g-xl-9">
        {{-- Main Content Area --}}
        <div class="col-lg-8">
            <div class="tab-content" id="kt_page_tab_content">
                {{-- Overview Tab --}}
                <div class="tab-pane fade show active" id="kt_tab_overview" role="tabpanel">
                    {{-- Quick Stats --}}
                    <div class="mb-5 row g-5 g-xl-8 mb-xl-8">
                        <div class="col-xl-4">
                            <div class="card card-flush h-xl-20">
                                <div class="flex-row card-body d-flex align-items-center justify-content-between">
                                    <div class="d-flex flex-column">
                                        <span class="text-dark text-hover-primary fw-bold fs-3">
                                            {{ $page->status == 'published' ? 'Live' : ucfirst($page->status) }}
                                        </span>
                                        <span class="text-gray-500 fw-semibold fs-7">Page Status</span>
                                    </div>
                                    <div class="m-0">
                                        @if ($page->status == 'published')
                                            <i class="ki-duotone ki-check-circle fs-2x text-success">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        @elseif($page->status == 'draft')
                                            <i class="ki-duotone ki-pencil fs-2x text-warning">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        @else
                                            <i class="ki-duotone ki-archive fs-2x text-secondary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="card card-flush h-xl-20 bg-light-info">
                                <div class="flex-row card-body d-flex align-items-center justify-content-between">
                                    <div class="d-flex flex-column">
                                        <span class="text-dark text-hover-primary fw-bold fs-3">
                                            {{ $page->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="text-gray-500 fw-semibold fs-7">Created Date</span>
                                    </div>
                                    <div class="m-0">
                                        <i class="ki-duotone ki-calendar-add fs-2x text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                            <span class="path6"></span>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="card card-flush h-xl-20 bg-light-primary">
                                <div class="flex-row card-body d-flex align-items-center justify-content-between">
                                    <div class="d-flex flex-column">
                                        <span class="text-dark text-hover-primary fw-bold fs-3">
                                            {{ $page->updated_at->diffForHumans() }}
                                        </span>
                                        <span class="text-gray-500 fw-semibold fs-7">Last Updated</span>
                                    </div>
                                    <div class="m-0">
                                        <i class="ki-duotone ki-timer fs-2x text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Page Details --}}
                    <div class="mb-5 card card-flush mb-xl-8">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="text-gray-800 card-label fw-bold">Page Details</span>
                                <span class="mt-1 text-gray-500 fw-semibold fs-6">Essential information about this
                                    page</span>
                            </h3>
                        </div>
                        <div class="pt-6 card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-bordered table-row-dashed gy-4 fw-bold">
                                    <tbody class="text-gray-600 fs-6">
                                        <tr>
                                            <td class="text-gray-800 min-w-200px">Page Title</td>
                                            <td class="text-end">
                                                <span class="badge badge-light-primary fs-7">{{ $page->title }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800">URL Slug</td>
                                            <td class="text-end">
                                                <code class="fs-6">{{ $page->slug }}</code>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800">Status</td>
                                            <td class="text-end">
                                                @if ($page->status == 'published')
                                                    <span class="badge badge-success">Published</span>
                                                @elseif($page->status == 'draft')
                                                    <span class="badge badge-warning">Draft</span>
                                                @else
                                                    <span class="badge badge-secondary">Archived</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800">Created At</td>
                                            <td class="text-end">{{ $page->created_at->format('F d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-800">Last Updated</td>
                                            <td class="text-end">{{ $page->updated_at->format('F d, Y h:i A') }}</td>
                                        </tr>
                                        @if ($page->published_at)
                                            <tr>
                                                <td class="text-gray-800">Published At</td>
                                                <td class="text-end">{{ $page->published_at->format('F d, Y h:i A') }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Content Preview --}}
                    @if ($page->content)
                        <div class="card card-flush">
                            <div class="card-header pt-7">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="text-gray-800 card-label fw-bold">Content Preview</span>
                                    <span class="mt-1 text-gray-500 fw-semibold fs-6">Quick look at your page
                                        content</span>
                                </h3>
                                <div class="card-toolbar">
                                    <a href="{{ route('pages.content-edit', $page) }}"
                                        class="btn btn-sm btn-light-primary">
                                        <i class="ki-duotone ki-pencil fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Edit
                                    </a>
                                </div>
                            </div>
                            <div class="pt-6 card-body">
                                <div class="p-5 border border-gray-300 border-dashed rounded">
                                    {!! Str::limit(strip_tags($page->content), 500) !!}
                                    @if (strlen(strip_tags($page->content)) > 500)
                                        <a href="#kt_tab_content" data-bs-toggle="tab"
                                            class="text-primary fw-bold">... Read more</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card card-flush">
                            <div class="py-20 text-center card-body">
                                <i class="mb-5 text-gray-400 ki-duotone ki-file-deleted fs-5x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <h3 class="mb-3 text-gray-800 fw-bold">No Content Yet</h3>
                                <p class="text-gray-500 fw-semibold fs-6 mb-7">This page doesn't have any content.
                                    Start creating!</p>
                                <a href="{{ route('pages.content-edit', $page) }}" class="btn btn-primary">
                                    <i class="ki-duotone ki-plus fs-2"></i>
                                    Add Content
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Content Tab --}}
                <div class="tab-pane fade" id="kt_tab_content" role="tabpanel">
                    <div class="card card-flush">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="text-gray-800 card-label fw-bold">Full Page Content</span>
                                <span class="mt-1 text-gray-500 fw-semibold fs-6">Complete content of your page</span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('pages.content-edit', $page) }}" class="btn btn-sm btn-primary">
                                    <i class="ki-duotone ki-pencil fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Edit Content
                                </a>
                            </div>
                        </div>
                        <div class="pt-6 card-body">
                            @if ($page->content)
                                <div class="p-8 rounded content-preview bg-light">
                                    {!! $page->content !!}
                                </div>
                            @else
                                <div class="py-20 text-center">
                                    <i class="mb-5 text-gray-400 ki-duotone ki-file-deleted fs-5x">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <h3 class="mb-3 text-gray-800 fw-bold">No Content Available</h3>
                                    <p class="text-gray-500 fw-semibold fs-6 mb-7">Start writing your page content now
                                    </p>
                                    <a href="{{ route('pages.content-edit', $page) }}" class="btn btn-primary">
                                        <i class="ki-duotone ki-plus fs-2"></i>
                                        Add Content
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- SEO Details Tab --}}
                <div class="tab-pane fade" id="kt_tab_seo" role="tabpanel">
                    <div class="mb-5 card card-flush mb-xl-8">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="text-gray-800 card-label fw-bold">SEO Meta Information</span>
                                <span class="mt-1 text-gray-500 fw-semibold fs-6">Search engine optimization
                                    details</span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('pages.seo-edit', $page) }}" class="btn btn-sm btn-light-success">
                                    <i class="ki-duotone ki-pencil fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Edit SEO
                                </a>
                            </div>
                        </div>
                        <div class="pt-6 card-body">
                            {{-- Basic Meta --}}
                            <div class="mb-10">
                                <h4 class="mb-5 text-gray-800 fw-bold">
                                    <i class="ki-duotone ki-tag fs-2 text-primary me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Basic Meta Tags
                                </h4>
                                <div class="table-responsive">
                                    <table class="table table-row-bordered gy-5">
                                        <tbody>
                                            <tr>
                                                <td class="text-gray-800 fw-bold min-w-200px">Meta Title</td>
                                                <td>
                                                    @if ($page->meta_title)
                                                        <span class="text-gray-700">{{ $page->meta_title }}</span>
                                                        <span
                                                            class="badge badge-light-success ms-2">{{ strlen($page->meta_title) }}
                                                            chars</span>
                                                    @else
                                                        <span class="text-gray-500 fst-italic">Not set (using page
                                                            title)</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800 fw-bold">Meta Description</td>
                                                <td>
                                                    @if ($page->meta_description)
                                                        <span
                                                            class="text-gray-700">{{ $page->meta_description }}</span>
                                                        <span
                                                            class="badge badge-light-success ms-2">{{ strlen($page->meta_description) }}
                                                            chars</span>
                                                    @else
                                                        <span class="text-gray-500 fst-italic">Not set</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800 fw-bold">Meta Keywords</td>
                                                <td>
                                                    @if ($page->meta_keywords)
                                                        @foreach (explode(',', $page->meta_keywords) as $keyword)
                                                            <span
                                                                class="badge badge-light me-1">{{ trim($keyword) }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-gray-500 fst-italic">Not set</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800 fw-bold">Canonical URL</td>
                                                <td>
                                                    @if ($page->canonical_url)
                                                        <code class="text-primary">{{ $page->canonical_url }}</code>
                                                    @else
                                                        <span class="text-gray-500 fst-italic">Not set</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Social Media --}}
                            <div class="mb-10">
                                <h4 class="mb-5 text-gray-800 fw-bold">
                                    <i class="ki-duotone ki-share fs-2 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Social Media Tags
                                </h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="p-5 mb-5 border border-gray-300 border-dashed rounded">
                                            <h5 class="mb-4 text-gray-800 fw-bold">
                                                <i class="bi bi-facebook text-primary fs-2 me-2"></i>
                                                Open Graph
                                            </h5>
                                            <div class="mb-3 d-flex justify-content-between">
                                                <span class="text-gray-600">OG Title:</span>
                                                <span
                                                    class="text-gray-800 fw-semibold">{{ $page->og_title ?: 'Not set' }}</span>
                                            </div>
                                            <div class="mb-3 d-flex justify-content-between">
                                                <span class="text-gray-600">OG Type:</span>
                                                <span
                                                    class="badge badge-light-primary">{{ $page->og_type ?: 'website' }}</span>
                                            </div>
                                            <div class="mb-3 d-flex justify-content-between">
                                                <span class="text-gray-600">OG Image:</span>
                                                @if ($page->og_image)
                                                    <span class="badge badge-light-success">
                                                        <i class="ki-duotone ki-check fs-5"></i>
                                                        Set
                                                    </span>
                                                @else
                                                    <span class="badge badge-light-danger">Not set</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-5 mb-5 border border-gray-300 border-dashed rounded">
                                            <h5 class="mb-4 text-gray-800 fw-bold">
                                                <i class="bi bi-twitter-x text-dark fs-2 me-2"></i>
                                                Twitter Card
                                            </h5>
                                            <div class="mb-3 d-flex justify-content-between">
                                                <span class="text-gray-600">Twitter Title:</span>
                                                <span
                                                    class="text-gray-800 fw-semibold">{{ $page->twitter_title ?: 'Not set' }}</span>
                                            </div>
                                            <div class="mb-3 d-flex justify-content-between">
                                                <span class="text-gray-600">Card Type:</span>
                                                <span
                                                    class="badge badge-light-info">{{ $page->twitter_card ?: 'summary' }}</span>
                                            </div>
                                            <div class="mb-3 d-flex justify-content-between">
                                                <span class="text-gray-600">Twitter Image:</span>
                                                @if ($page->twitter_image)
                                                    <span class="badge badge-light-success">
                                                        <i class="ki-duotone ki-check fs-5"></i>
                                                        Set
                                                    </span>
                                                @else
                                                    <span class="badge badge-light-danger">Not set</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Structured Data --}}
                            <div class="mb-0">
                                <h4 class="mb-5 text-gray-800 fw-bold">
                                    <i class="ki-duotone ki-code fs-2 text-warning me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    Structured Data
                                </h4>
                                @if ($page->structured_data)
                                    <div class="p-5 rounded bg-light-dark">
                                        <pre class="mb-0"><code class="language-json">{{ is_array($page->structured_data) ? json_encode($page->structured_data, JSON_PRETTY_PRINT) : $page->structured_data }}</code></pre>
                                    </div>
                                @else
                                    <div class="p-10 text-center border border-gray-300 border-dashed rounded">
                                        <i class="mb-3 text-gray-400 ki-duotone ki-code fs-5x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        <p class="text-gray-500 fw-semibold">No structured data configured</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Search Preview Tab --}}
                <div class="tab-pane fade" id="kt_tab_preview" role="tabpanel">
                    <div class="card card-flush">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="text-gray-800 card-label fw-bold">Search Engine Preview</span>
                                <span class="mt-1 text-gray-500 fw-semibold fs-6">How your page appears in search
                                    results</span>
                            </h3>
                        </div>
                        <div class="pt-6 card-body">
                            <div class="p-10 border border-gray-300 rounded bg-light">
                                {{-- Google Preview --}}
                                <div class="mb-10">
                                    <h4 class="mb-5 text-gray-800 fw-bold">
                                        <i class="bi bi-google fs-2 text-primary me-2"></i>
                                        Google Search Result
                                    </h4>
                                    <div class="p-8 bg-white rounded shadow-sm">
                                        <div class="mb-2">
                                            <span class="text-success fs-7">
                                                {{ $page->canonical_url ?: url('/') . '/' . $page->slug }}
                                            </span>
                                        </div>
                                        <h3 class="mb-2 text-primary fs-4 fw-bold text-hover-underline"
                                            style="cursor: pointer;">
                                            {{ $page->meta_title ?: $page->title }}
                                        </h3>
                                        <p class="mb-0 text-gray-700 fs-6">
                                            {{ $page->meta_description ?: Str::limit(strip_tags($page->content), 160) }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Facebook Preview --}}
                                @if ($page->og_title || $page->og_image)
                                    <div class="mb-10">
                                        <h4 class="mb-5 text-gray-800 fw-bold">
                                            <i class="bi bi-facebook fs-2 text-primary me-2"></i>
                                            Facebook Share Preview
                                        </h4>
                                        <div class="bg-white rounded shadow-sm" style="max-width: 500px;">
                                            @if ($page->og_image)
                                                <img src="{{ $page->og_image }}" class="w-100 rounded-top"
                                                    style="max-height: 260px; object-fit: cover;" alt="OG Image">
                                            @else
                                                <div class="bg-gray-200 rounded-top d-flex align-items-center justify-content-center"
                                                    style="height: 260px;">
                                                    <i class="text-gray-500 ki-duotone ki-picture fs-5x">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </div>
                                            @endif
                                            <div class="p-5 border-start border-end border-bottom rounded-bottom">
                                                <div class="mb-1 text-gray-500 text-uppercase fs-8">
                                                    {{ parse_url($page->canonical_url ?: url('/'), PHP_URL_HOST) }}
                                                </div>
                                                <h5 class="mb-1 text-gray-900 fw-bold">
                                                    {{ $page->og_title ?: $page->meta_title ?: $page->title }}
                                                </h5>
                                                <p class="mb-0 text-gray-600 fs-7">
                                                    {{ Str::limit($page->og_description ?: $page->meta_description, 100) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Twitter Preview --}}
                                @if ($page->twitter_title || $page->twitter_image)
                                    <div>
                                        <h4 class="mb-5 text-gray-800 fw-bold">
                                            <i class="bi bi-twitter-x fs-2 text-dark me-2"></i>
                                            Twitter Card Preview
                                        </h4>
                                        <div class="bg-white border rounded shadow-sm" style="max-width: 500px;">
                                            @if ($page->twitter_image)
                                                <img src="{{ $page->twitter_image }}" class="w-100 rounded-top"
                                                    style="max-height: 250px; object-fit: cover;" alt="Twitter Image">
                                            @else
                                                <div class="bg-gray-200 rounded-top d-flex align-items-center justify-content-center"
                                                    style="height: 250px;">
                                                    <i class="text-gray-500 ki-duotone ki-picture fs-5x">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </div>
                                            @endif
                                            <div class="p-5 border-top">
                                                <h5 class="mb-2 text-gray-900 fw-bold">
                                                    {{ $page->twitter_title ?: $page->og_title ?: $page->meta_title ?: $page->title }}
                                                </h5>
                                                <p class="mb-2 text-gray-600 fs-7">
                                                    {{ Str::limit($page->twitter_description ?: $page->og_description ?: $page->meta_description, 120) }}
                                                </p>
                                                <div class="text-gray-500 fs-8">
                                                    <i class="ki-duotone ki-geolocation fs-6 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    {{ parse_url($page->canonical_url ?: url('/'), PHP_URL_HOST) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Quick Actions --}}
            <div class="mb-6 card card-flush">
                <div class="card-header pt-7">
                    <h3 class="card-title">
                        <span class="text-gray-800 card-label fw-bold">Quick Actions</span>
                    </h3>
                </div>
                <div class="pt-5 card-body">
                    <a href="{{ route('pages.content-edit', $page) }}" class="mb-3 btn btn-light-primary w-100">
                        <i class="ki-duotone ki-pencil fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Edit Content
                    </a>
                    <a href="{{ route('pages.seo-edit', $page) }}" class="mb-3 btn btn-light-success w-100">
                        <i class="ki-duotone ki-chart-simple fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        Configure SEO
                    </a>
                    <div class="my-5 separator"></div>
                    <button type="button" class="btn btn-light-danger w-100" onclick="deletePage()">
                        <i class="ki-duotone ki-trash fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                        Delete Page
                    </button>
                </div>
            </div>

            {{-- SEO Score --}}
            <div class="mb-6 card card-flush">
                <div class="card-header pt-7">
                    <h3 class="card-title">
                        <span class="text-gray-800 card-label fw-bold">SEO Score</span>
                    </h3>
                </div>
                <div class="pt-5 card-body">
                    <div class="mb-5 text-center">
                        <div
                            class="fs-2hx fw-bold 
                            @if ($page->meta_title && $page->meta_description && $page->og_image) text-success
                            @elseif($page->meta_title || $page->meta_description)
                                text-warning
                            @else
                                text-danger @endif
                        ">
                            @php
                                $score = 0;
                                if ($page->meta_title) {
                                    $score += 25;
                                }
                                if ($page->meta_description) {
                                    $score += 25;
                                }
                                if ($page->og_image) {
                                    $score += 25;
                                }
                                if ($page->structured_data) {
                                    $score += 25;
                                }
                            @endphp
                            {{ $score }}%
                        </div>
                        <span class="text-gray-500 fw-semibold fs-6">Overall SEO Health</span>
                    </div>

                    <div class="mb-5 separator separator-dashed"></div>

                    <div class="d-flex flex-column">
                        <div class="mb-5 d-flex align-items-center">
                            <div class="bullet {{ $page->meta_title ? 'bg-success' : 'bg-danger' }} me-3"
                                style="width: 8px; height: 8px; border-radius: 50%;"></div>
                            <span class="text-gray-800 fw-semibold fs-7 flex-grow-1">Meta Title</span>
                            @if ($page->meta_title)
                                <i class="ki-duotone ki-check-circle fs-2 text-success"><span
                                        class="path1"></span><span class="path2"></span></i>
                            @else
                                <i class="ki-duotone ki-cross-circle fs-2 text-danger"><span
                                        class="path1"></span><span class="path2"></span></i>
                            @endif
                        </div>

                        <div class="mb-5 d-flex align-items-center">
                            <div class="bullet {{ $page->meta_description ? 'bg-success' : 'bg-danger' }} me-3"
                                style="width: 8px; height: 8px; border-radius: 50%;"></div>
                            <span class="text-gray-800 fw-semibold fs-7 flex-grow-1">Meta Description</span>
                            @if ($page->meta_description)
                                <i class="ki-duotone ki-check-circle fs-2 text-success"><span
                                        class="path1"></span><span class="path2"></span></i>
                            @else
                                <i class="ki-duotone ki-cross-circle fs-2 text-danger"><span
                                        class="path1"></span><span class="path2"></span></i>
                            @endif
                        </div>

                        <div class="mb-5 d-flex align-items-center">
                            <div class="bullet {{ $page->og_image ? 'bg-success' : 'bg-warning' }} me-3"
                                style="width: 8px; height: 8px; border-radius: 50%;"></div>
                            <span class="text-gray-800 fw-semibold fs-7 flex-grow-1">Social Images</span>
                            @if ($page->og_image)
                                <i class="ki-duotone ki-check-circle fs-2 text-success"><span
                                        class="path1"></span><span class="path2"></span></i>
                            @else
                                <i class="ki-duotone ki-information-5 fs-2 text-warning"><span
                                        class="path1"></span><span class="path2"></span><span
                                        class="path3"></span></i>
                            @endif
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="bullet {{ $page->structured_data ? 'bg-success' : 'bg-warning' }} me-3"
                                style="width: 8px; height: 8px; border-radius: 50%;"></div>
                            <span class="text-gray-800 fw-semibold fs-7 flex-grow-1">Structured Data</span>
                            @if ($page->structured_data)
                                <i class="ki-duotone ki-check-circle fs-2 text-success"><span
                                        class="path1"></span><span class="path2"></span></i>
                            @else
                                <i class="ki-duotone ki-information-5 fs-2 text-warning"><span
                                        class="path1"></span><span class="path2"></span><span
                                        class="path3"></span></i>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Publishing Info --}}
            <div class="card card-flush bg-light-info">
                <div class="card-body">
                    <div class="mb-3 d-flex align-items-center">
                        <i class="ki-duotone ki-information-2 fs-2hx text-info me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <h3 class="m-0 text-gray-800 fw-bold">Page URL</h3>
                    </div>
                    <div class="mb-3 text-gray-700 fs-7 fw-semibold">
                        Your page will be accessible at:
                    </div>
                    <code class="p-3 bg-white rounded d-block fs-7">
                        {{ url('/') }}/{{ $page->slug }}
                    </code>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function deletePage() {
                Swal.fire({
                    title: 'Delete Page?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f1416c',
                    cancelButtonColor: '#7e8299',
                    confirmButtonText: 'Yes, Delete It',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-light'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('pages.destroy', $page) }}',
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message || 'Page has been deleted successfully.',
                                    icon: 'success',
                                    confirmButtonColor: '#50cd89',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                }).then(() => {
                                    window.location.href = '{{ route('pages.index') }}';
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Failed to delete the page. Please try again.',
                                    icon: 'error',
                                    confirmButtonColor: '#f1416c',
                                    customClass: {
                                        confirmButton: 'btn btn-danger'
                                    }
                                });
                            }
                        });
                    }
                });
            }

            // Initialize tooltips
            document.addEventListener('DOMContentLoaded', function() {
                const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltips.forEach(tooltip => {
                    new bootstrap.Tooltip(tooltip);
                });
            });
        </script>
    @endpush

</x-default-layout>
