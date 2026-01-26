<x-default-layout>

    @section('title')
        Blogs
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('apps.blogs.index') }}
    @endsection

    <div class="card">
        <div class="pt-6 border-0 card-header">
            <div class="card-title">
                <div class="my-1 d-flex align-items-center position-relative">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" class="form-control form-control-solid w-250px ps-13"
                        placeholder="Search Blogs..." id="searchInput" />
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('blogs.create') }}" class="btn btn-primary">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add Blog
                    </a>
                </div>
            </div>
        </div>

        <div class="py-4 card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="blogsTable">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-50px">Image</th>
                            <th class="min-w-125px">Title</th>
                            <th class="min-w-100px">Author</th>
                            <th class="min-w-100px">Publish Date</th>
                            <th class="min-w-100px">Status</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($blogs as $blog)
                            <tr>
                                <td>
                                    @if ($blog->featured_image)
                                        <img src="{{ asset('storage/' . $blog->featured_image) }}"
                                            alt="{{ $blog->title }}" class="rounded w-50px h-50px object-fit-cover">
                                    @else
                                        <div class="symbol symbol-50px">
                                            <span class="symbol-label bg-light-primary text-primary fs-6 fw-bold">
                                                {{ substr($blog->title, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('blogs.edit', $blog) }}"
                                        class="mb-1 text-gray-800 text-hover-primary">
                                        {{ Str::limit($blog->title, 40) }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($blog->author_profile_image)
                                            <img src="{{ asset('storage/' . $blog->author_profile_image) }}"
                                                alt="{{ $blog->author_name }}"
                                                class="rounded-circle w-30px h-30px me-2">
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $blog->author_name }}</div>
                                            @if ($blog->author_position)
                                                <div class="text-muted fs-7">{{ $blog->author_position }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $blog->publish_date->format('M d, Y') }}
                                </td>
                                <td>
                                    @if ($blog->status == 'published')
                                        <span class="badge badge-light-success">Published</span>
                                    @elseif($blog->status == 'draft')
                                        <span class="badge badge-light-warning">Draft</span>
                                    @else
                                        <span class="badge badge-light-secondary">Archived</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-light btn-active-light-primary btn-sm"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Actions
                                        {!! getIcon('down', 'fs-5 m-0') !!}
                                    </a>
                                    <div class="py-3 menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px"
                                        data-kt-menu="true">
                                        <div class="px-3 menu-item">
                                            <a href="{{ route('blogs.edit', $blog) }}" class="px-3 menu-link">
                                                Edit
                                            </a>
                                        </div>
                                        <div class="px-3 menu-item">
                                            <a href="javascript:void(0);" onclick="deleteBlog({{ $blog->id }})"
                                                class="px-3 menu-link text-danger">
                                                Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="py-10">
                                        {!! getIcon('file', 'fs-3x text-gray-400') !!}
                                        <div class="mt-5 text-muted fs-5">No blogs available yet. Start by adding your
                                            first
                                            blog!</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $blogs->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Search functionality
            document.getElementById('searchInput').addEventListener('keyup', function() {
                let searchValue = this.value.toLowerCase();
                let tableRows = document.querySelectorAll('#blogsTable tbody tr');

                tableRows.forEach(row => {
                    let text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchValue) ? '' : 'none';
                });
            });

            // Delete function
            function deleteBlog(id) {
                Swal.fire({
                    title: 'Move to Trash?',
                    text: "This blog will be moved to trash. You can restore it later!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, move to trash!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/blogs/${id}`, {
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
