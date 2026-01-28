<x-default-layout>
    @section('title')
        Pages Management
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('pages.index') }}
    @endsection

    <div class="card">
        <div class="pt-6 border-0 card-header">
            <div class="card-title">
                <div class="my-1 d-flex align-items-center position-relative">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" class="form-control form-control-solid w-250px ps-13"
                        placeholder="Search Pages..." id="searchInput" />
                </div>
            </div>

            <div class="card-toolbar">
                <div class="gap-2 d-flex justify-content-end">
                    <select class="form-select form-select-solid" id="statusFilter" data-control="select2"
                        data-hide-search="true">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                    <button type="button" class="btn btn-primary w-300px" data-bs-toggle="modal"
                        data-bs-target="#createPageModal">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add New Page
                    </button>
                </div>
            </div>
        </div>

        <div class="py-4 card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="pagesTable">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-50px">#</th>
                            <th class="min-w-200px">Title</th>
                            <th class="min-w-125px">Slug</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-125px">Created At</th>
                            <th class="text-end min-w-150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        <!-- Data loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
            <div id="pagination" class="mt-5"></div>
        </div>
    </div>

    <!-- Create/Edit Page Modal -->
    <div class="modal fade" id="createPageModal" tabindex="-1" aria-labelledby="createPageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPageModalLabel">Create New Page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="pageForm">
                    @csrf
                    <input type="hidden" id="pageId" name="page_id">
                    <input type="hidden" id="formMethod" value="POST">

                    <div class="modal-body">
                        <div class="mb-5">
                            <label class="required form-label">Page Title</label>
                            <input type="text" class="form-control" id="pageTitle" name="title" required
                                placeholder="Enter page title" />
                            <div class="invalid-feedback" id="titleError"></div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" id="pageSlug" name="slug"
                                placeholder="auto-generated-from-title" />
                            <div class="form-text">Leave empty to auto-generate from title</div>
                            <div class="invalid-feedback" id="slugError"></div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="pageStatus" name="status" data-control="select2"
                                data-hide-search="true">
                                <option value="draft" selected>Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                            <div class="form-text">You can edit content after creating the page</div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="indicator-label">Save Page</span>
                            <span class="indicator-progress" style="display: none;">
                                Please wait...
                                <span class="align-middle spinner-border spinner-border-sm ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let currentPage = 1;
                let searchTimeout;

                // Escape HTML to prevent XSS
                function escapeHtml(text) {
                    if (!text) return '';
                    const map = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    };
                    return text.replace(/[&<>"']/g, m => map[m]);
                }

                // Load pages data
                function loadPages(page = 1) {
                    const search = $('#searchInput').val();
                    const status = $('#statusFilter').val();

                    $.ajax({
                        url: '{{ route('pages.index') }}',
                        method: 'GET',
                        data: {
                            page: page,
                            search: search,
                            status: status,
                            per_page: 10
                        },
                        success: function(response) {
                            renderTable(response.data);
                            renderPagination(response);
                        },
                        error: function(xhr) {
                            console.error('Error loading pages:', xhr);
                            toastr.error('Failed to load pages');
                        }
                    });
                }

                // Render table rows
                function renderTable(pages) {
                    const tbody = $('#pagesTable tbody');
                    tbody.empty();

                    if (!pages || pages.length === 0) {
                        tbody.append(`
                        <tr>
                            <td colspan="6" class="py-10 text-center">
                                <div class="d-flex flex-column align-items-center"> 
                                    <i class="ki-solid ki-magnifier fs-2x"></i>
                                    <div class="text-gray-500 fs-5">No pages found</div>
                                    <div class="mt-1 text-gray-400 fs-7">Try adjusting your search or filters</div>
                                </div>
                            </td>
                        </tr>
                    `);
                        return;
                    }

                    pages.forEach((page, index) => {
                        const statusBadge = getStatusBadge(page.status);
                        const createdAt = new Date(page.created_at).toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric'
                        });

                        tbody.append(`
                        <tr>
                            <td>${(currentPage - 1) * 10 + index + 1}</td>
                            <td>
                                <div class="text-gray-800 fw-bold">${escapeHtml(page.title)}</div>
                            </td>
                            <td>
                                <code class="text-gray-600">${escapeHtml(page.slug)}</code>
                            </td>
                            <td>${statusBadge}</td>
                            <td>${createdAt}</td>
                            <td class="text-end">
                                <button class="btn btn-icon btn-sm btn-light-primary me-1" 
                                    onclick="viewPage(${page.id})" title="View">
                                    {!! getIcon('eye', 'fs-4 text-black') !!}
                                </button>
                                <a href="{{ route('pages.index') }}/${page.id}/seo" 
                                    class="btn btn-icon btn-sm btn-light-success me-1" title="SEO Edit">
                                    {!! getIcon('chart-simple', 'fs-4 text-black') !!}
                                </a>
                                <a href="{{ route('pages.index') }}/${page.id}/content" 
                                    class="btn btn-icon btn-sm btn-light-info me-1" title="Content Edit">
                                    {!! getIcon('notepad', 'fs-4 text-black') !!}
                                </a>
                                <button class="btn btn-icon btn-sm btn-light-warning me-1" 
                                    onclick="editPage(${page.id})" title="Quick Edit">
                                    {!! getIcon('pencil', 'fs-4 text-black') !!}
                                </button>
                                <button class="btn btn-icon btn-sm btn-light-danger" 
                                    onclick="deletePage(${page.id})" title="Delete">
                                    {!! getIcon('trash', 'fs-4 text-black') !!}
                                </button>
                            </td>
                        </tr>
                    `);
                    });
                }

                // Get status badge HTML
                function getStatusBadge(status) {
                    const badges = {
                        published: '<span class="badge badge-light-success">Published</span>',
                        draft: '<span class="badge badge-light-warning">Draft</span>',
                        archived: '<span class="badge badge-light-secondary">Archived</span>'
                    };
                    return badges[status] || badges.draft;
                }

                // Render pagination
                function renderPagination(response) {
                    const pagination = $('#pagination');
                    pagination.empty();

                    if (response.last_page <= 1) return;

                    let html = '<ul class="pagination">';

                    // Previous button
                    if (response.current_page > 1) {
                        html +=
                            `<li class="page-item previous"><a href="#" class="page-link" data-page="${response.current_page - 1}"><i class="previous"></i></a></li>`;
                    } else {
                        html +=
                            '<li class="page-item previous disabled"><a href="#" class="page-link"><i class="previous"></i></a></li>';
                    }

                    // Page numbers
                    for (let i = 1; i <= response.last_page; i++) {
                        if (i === response.current_page) {
                            html += `<li class="page-item active"><a href="#" class="page-link">${i}</a></li>`;
                        } else {
                            html +=
                                `<li class="page-item"><a href="#" class="page-link" data-page="${i}">${i}</a></li>`;
                        }
                    }

                    // Next button
                    if (response.current_page < response.last_page) {
                        html +=
                            `<li class="page-item next"><a href="#" class="page-link" data-page="${response.current_page + 1}"><i class="next"></i></a></li>`;
                    } else {
                        html +=
                            '<li class="page-item next disabled"><a href="#" class="page-link"><i class="next"></i></a></li>';
                    }

                    html += '</ul>';
                    pagination.html(html);

                    // Pagination click handler
                    pagination.find('a').on('click', function(e) {
                        e.preventDefault();
                        const page = $(this).data('page');
                        if (page) {
                            currentPage = page;
                            loadPages(page);
                        }
                    });
                }

                // Search functionality
                $('#searchInput').on('keyup', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        currentPage = 1;
                        loadPages();
                    }, 500);
                });

                // Status filter
                $('#statusFilter').on('change', function() {
                    currentPage = 1;
                    loadPages();
                });

                // Auto-generate slug from title
                $('#pageTitle').on('keyup', function() {
                    const title = $(this).val();
                    const slug = title.toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .trim();
                    $('#pageSlug').attr('placeholder', slug || 'auto-generated-from-title');
                });

                // Form submission
                $('#pageForm').on('submit', function(e) {
                    e.preventDefault();

                    const submitBtn = $('#submitBtn');
                    const indicator = submitBtn.find('.indicator-label');
                    const progress = submitBtn.find('.indicator-progress');

                    submitBtn.prop('disabled', true);
                    indicator.hide();
                    progress.show();

                    const pageId = $('#pageId').val();
                    const method = $('#formMethod').val();
                    const url = pageId ? `{{ route('pages.index') }}/${pageId}` : '{{ route('pages.store') }}';

                    const formData = {
                        title: $('#pageTitle').val(),
                        slug: $('#pageSlug').val(),
                        status: $('#pageStatus').val(),
                        _token: '{{ csrf_token() }}'
                    };

                    if (method === 'PUT') {
                        formData._method = 'PUT';
                    }

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            toastr.success(response.message || 'Page saved successfully!');
                            $('#createPageModal').modal('hide');
                            resetForm();
                            loadPages(currentPage);
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                if (errors.title) {
                                    $('#titleError').text(errors.title[0]).show();
                                    $('#pageTitle').addClass('is-invalid');
                                }
                                if (errors.slug) {
                                    $('#slugError').text(errors.slug[0]).show();
                                    $('#pageSlug').addClass('is-invalid');
                                }
                            } else {
                                toastr.error('Failed to save page');
                            }
                        },
                        complete: function() {
                            submitBtn.prop('disabled', false);
                            indicator.show();
                            progress.hide();
                        }
                    });
                });

                // Reset form
                function resetForm() {
                    $('#pageForm')[0].reset();
                    $('#pageId').val('');
                    $('#formMethod').val('POST');
                    $('#createPageModalLabel').text('Create New Page');
                    $('#submitBtn .indicator-label').text('Save Page');
                    $('#pageStatus').val('draft');
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').hide();
                }

                // Reset form when modal is hidden
                $('#createPageModal').on('hidden.bs.modal', function() {
                    resetForm();
                });

                // Edit page function (Quick Edit)
                window.editPage = function(id) {
                    $.ajax({
                        url: `{{ route('pages.index') }}/${id}`,
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        },
                        success: function(page) {
                            $('#pageId').val(page.id);
                            $('#formMethod').val('PUT');
                            $('#pageTitle').val(page.title);
                            $('#pageSlug').val(page.slug);
                            $('#pageStatus').val(page.status);
                            $('#createPageModalLabel').text('Quick Edit Page');
                            $('#submitBtn .indicator-label').text('Update Page');
                            $('#createPageModal').modal('show');
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            toastr.error('Failed to load page data');
                        }
                    });
                };

                // View page function
                window.viewPage = function(id) {
                    window.location.href = `{{ route('pages.index') }}/${id}`;
                };

                // Delete page function
                window.deletePage = function(id) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this! This will permanently delete the page.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `{{ route('pages.index') }}/${id}`,
                                method: 'POST',
                                data: {
                                    _method: 'DELETE',
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message || 'Page deleted successfully!',
                                        'success'
                                    );
                                    loadPages(currentPage);
                                },
                                error: function(xhr) {
                                    console.error('Delete error:', xhr);
                                    Swal.fire(
                                        'Error!',
                                        'Failed to delete page',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                };

                // Initial load
                loadPages();
            });
        </script>
    @endpush

</x-default-layout>
