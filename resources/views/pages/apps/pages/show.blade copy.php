<x-default-layout>
    @section('title')
        View Page - {{ $page->title }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('pages.show', $page) }}
    @endsection

    <div class="card">
        <div class="pt-6 border-0 card-header">
            <div class="card-title">
                <h2 class="fw-bold">{{ $page->title }}</h2>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('pages.index') }}" class="btn btn-light me-2">
                    <i class="fas fa-arrow-left"></i>
                    Back to Pages
                </a>
                <a href="{{ route('pages.content-edit', $page) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit"></i>
                    Edit Content
                </a>
                <a href="{{ route('pages.seo-edit', $page) }}" class="btn btn-success">
                    <i class="fas fa-chart-simple"></i>
                    Edit SEO
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Page Status Banner -->
            <div
                class="alert alert-{{ $page->status == 'published' ? 'success' : ($page->status == 'draft' ? 'warning' : 'secondary') }} d-flex align-items-center mb-8">
                {!! getIcon('information-5', 'fs-2x me-4') !!}
                <div>
                    <h4 class="gap-2 mb-1 d-flex align-items-center">
                        Page Status:
                        @if ($page->status == 'published')
                            <span class="badge badge-success">Published</span>
                        @elseif($page->status == 'draft')
                            <span class="badge badge-warning">Draft</span>
                        @else
                            <span class="badge badge-secondary">Archived</span>
                        @endif
                    </h4>
                    <div class="fw-semibold">
                        @if ($page->published_at)
                            Published on: {{ $page->published_at->format('F d, Y h:i A') }}
                        @else
                            Not yet published
                        @endif
                    </div>
                </div>
            </div>

            <!-- Page Information -->
            <div class="mb-10 row">
                <div class="col-md-6">
                    <div class="card bg-light-primary">
                        <div class="card-body">
                            <h5 class="mb-4 card-title d-flex align-items-center">
                                {!! getIcon('information', 'fs-3 me-2') !!}
                                Basic Information
                            </h5>
                            <table class="table mb-0 table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 40%">Title:</td>
                                    <td>{{ $page->title }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Slug:</td>
                                    <td><code>{{ $page->slug }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
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
                                    <td class="fw-bold">Created:</td>
                                    <td>{{ $page->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Updated:</td>
                                    <td>{{ $page->updated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card bg-light-success">
                        <div class="card-body">
                            <h5 class="mb-4 card-title">
                                {!! getIcon('chart-simple', 'fs-3 me-2') !!}
                                SEO Information
                            </h5>
                            <table class="table mb-0 table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 40%">Meta Title:</td>
                                    <td>{{ $page->meta_title ?: 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Meta Description:</td>
                                    <td>{{ Str::limit($page->meta_description, 50) ?: 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">OG Image:</td>
                                    <td>{{ $page->og_image ? 'Set' : 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Canonical URL:</td>
                                    <td>{{ $page->canonical_url ?: 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Structured Data:</td>
                                    <td>{{ $page->structured_data ? 'Set' : 'Not set' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        {!! getIcon('document', 'fs-3 me-2') !!}
                        Page Content
                    </h3>
                </div>
                <div class="card-body">
                    @if ($page->content)
                        <div class="p-5 border rounded content-preview">
                            {!! $page->content !!}
                        </div>
                    @else
                        <div class="py-10 text-center text-muted">
                            <i class="mb-3 bi bi-file-earmark-text fs-3x"></i>
                            <p>No content available for this page.</p>
                            <a href="{{ route('pages.content-edit', $page) }}" class="btn btn-primary">
                                Add Content
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SEO Preview -->
            @if ($page->meta_title || $page->meta_description)
                <div class="mt-8 card">
                    <div class="card-header">
                        <h3 class="card-title">
                            {!! getIcon('google', 'fs-3 me-2') !!}
                            Search Engine Preview
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="p-5 border rounded bg-light">
                            <div class="mb-2 text-success fs-7">
                                {{ $page->canonical_url ?: url('/') . '/' . $page->slug }}
                            </div>
                            <div class="mb-1 text-gray-700 fs-5">
                                {{ $page->meta_title ?: $page->title }}
                            </div>
                            <div class="text-gray-700">
                                {{ $page->meta_description ?: Str::limit(strip_tags($page->content), 160) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="py-6 card-footer d-flex justify-content-between">
            <button type="button" class="btn btn-light-danger" onclick="deletePage()">
                 <i class="bi bi-trash"></i>
                Delete Page
            </button>
            <div>
                <a href="{{ route('pages.content-edit', $page) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    Edit Content
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function deletePage() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('pages.destroy', $page) }}',
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message || 'Page has been deleted.',
                                    'success'
                                ).then(() => {
                                    window.location.href = '{{ route('pages.index') }}';
                                });
                            },
                            error: function() {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete page.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            }
        </script>
    @endpush

</x-default-layout>
