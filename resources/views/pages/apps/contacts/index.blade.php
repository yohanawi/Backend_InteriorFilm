<x-default-layout>

    @section('title')
        Contact Messages Management
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('apps.contacts.index') }}
    @endsection

    <div class="card">
        <div class="pt-6 border-0 card-header">
            <div class="card-title">
                <div class="my-1 d-flex align-items-center position-relative">
                    <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                    <input type="text" id="search-input" class="form-control form-control-solid w-250px ps-13"
                        placeholder="Search contacts..." value="{{ request('search') }}" />
                </div>
            </div>

            <div class="card-toolbar">
                <div class="gap-2 d-flex justify-content-end">
                    @if (request()->has('search') || request()->has('status'))
                        <a href="{{ route('contacts.index') }}" class="btn btn-light-danger btn-sm">Clear Filters</a>
                    @endif

                    <select class="form-select form-select-solid w-150px" id="status-filter" data-control="select2"
                        data-hide-search="true">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="pt-0 card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_contacts_table">
                    <thead>
                        <tr class="text-gray-400 text-start fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Name</th>
                            <th class="min-w-125px">Email</th>
                            <th class="min-w-100px">Phone</th>
                            <th class="min-w-200px">Message</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-125px">Date</th>
                            <th class="text-end min-w-70px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($contacts as $contact)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="overflow-hidden symbol symbol-circle symbol-50px me-3">
                                            <div class="symbol-label bg-light-primary">
                                                <span
                                                    class="text-primary fw-bold">{{ strtoupper(substr($contact->name, 0, 1)) }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold">{{ $contact->name }}</span>
                                            <span class="text-muted fs-7">{{ $contact->ip_address }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $contact->email }}"
                                        class="text-gray-600 text-hover-primary">{{ $contact->email }}</a>
                                </td>
                                <td>
                                    <a href="tel:{{ $contact->phone }}"
                                        class="text-gray-600 text-hover-primary">{{ $contact->phone }}</a>
                                </td>
                                <td>
                                    <div class="text-gray-600"
                                        style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                        title="{{ $contact->message }}">
                                        {{ Str::limit($contact->message, 50) }}
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge text-gray-500 badge-light-{{ $contact->status_badge_color }}">{{ ucfirst($contact->status) }}</span>
                                </td>
                                <td>{{ $contact->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="#"
                                        class="btn btn-sm btn-light btn-active-light-primary btn-flex btn-center"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Actions
                                        <i class="ki-outline ki-down fs-5 ms-1"></i>
                                    </a>

                                    <div class="py-4 menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-700 menu-state-bg-light-primary fw-semibold fs-7 w-225px"
                                        data-kt-menu="true">

                                        <!-- View Details -->
                                        <div class="px-3 menu-item">
                                            <a href="{{ route('contacts.show', $contact->id) }}"
                                                class="px-3 menu-link">
                                                <i class="ki-outline ki-eye fs-5 me-2"></i>
                                                View Details
                                            </a>
                                        </div>

                                        @if ($contact->status !== 'replied')
                                            <div class="px-3 menu-item">
                                                <form action="{{ route('contacts.update-status', $contact->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="replied">
                                                    <button type="submit"
                                                        class="px-3 bg-transparent border-0 menu-link w-100 text-start">
                                                        <i class="ki-outline ki-check fs-5 me-2"></i>
                                                        Mark as Replied
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if ($contact->status !== 'closed')
                                            <div class="px-3 menu-item">
                                                <form action="{{ route('contacts.update-status', $contact->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="closed">
                                                    <button type="submit"
                                                        class="px-3 bg-transparent border-0 menu-link w-100 text-start">
                                                        <i class="ki-outline ki-lock fs-5 me-2"></i>
                                                        Mark as Closed
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        <div class="px-3 menu-item">
                                            <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST"
                                                class="delete-contact-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 bg-transparent border-0 menu-link w-100 text-start">
                                                    <i class="ki-outline ki-trash fs-5 me-2"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="mb-3 text-gray-400 ki-outline ki-message-text-2 fs-3x"></i>
                                        <span class="text-gray-600 fs-5">
                                            No contact messages found
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($contacts->hasPages())
                <div class="flex-wrap mt-4 pagination-card d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                        Showing
                        <span>{{ $contacts->firstItem() }}</span> –
                        <span>{{ $contacts->lastItem() }}</span>
                        of
                        <span>{{ $contacts->total() }}</span> entries
                    </div>

                    <div class="pagination-links">
                        {{ $contacts->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
            <style>
                .pagination-card {
                    background: #ffffff;
                    border-radius: 12px;
                    padding: 16px 20px;
                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
                }

                .pagination-info {
                    font-size: 14px;
                    color: #6b7280;
                }

                .pagination-info span {
                    font-weight: 600;
                    color: #111827;
                }

                .pagination-links .page-link {
                    border-radius: 8px !important;
                    margin: 0 4px;
                    border: none;
                    color: #374151;
                }

                .pagination-links .page-item.active .page-link {
                    background-color: #6366f1;
                    color: #fff;
                }

                .pagination-links .page-link:hover {
                    background-color: #eef2ff;
                }
            </style>
        </div>
    </div>

</x-default-layout>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- Success Toast ---
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: @json(session('success')),
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif

            // --- Delete Confirmation ---
            document.querySelectorAll('.delete-contact-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This action cannot be undone!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // --- Status Update Confirmation ---
            document.querySelectorAll('form[action*="update-status"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const status = form.querySelector('input[name="status"]').value;
                    let message = '';
                    let icon = 'question';

                    if (status === 'replied') message = 'Mark this message as Replied?';
                    if (status === 'closed') message = 'Mark this message as Closed?';

                    Swal.fire({
                        title: 'Confirm Action',
                        text: message,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: '#6366f1',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });


            // --- Search & Filter ---
            const searchInput = document.getElementById('search-input');
            const statusFilter = document.getElementById('status-filter');

            function performSearch() {
                const url = new URL(window.location.href);
                const searchValue = searchInput.value.trim();
                const statusValue = statusFilter.value;

                // Always set search param (even if empty, to clear it)
                if (searchValue) {
                    url.searchParams.set('search', searchValue);
                } else {
                    url.searchParams.delete('search');
                }

                // Always set status param (even if 'all', to clear it)
                if (statusValue && statusValue !== 'all') {
                    url.searchParams.set('status', statusValue);
                } else {
                    url.searchParams.delete('status');
                }

                // Always reset to first page
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }

            // Ensure Select2 is initialized if present
            if (window.jQuery && typeof $(statusFilter).select2 === 'function') {
                $(statusFilter).select2({
                    minimumResultsForSearch: Infinity
                });
            }

            // Listen for Enter key on search
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') performSearch();
            });

            // Listen for change on dropdown (works for both native and Select2)
            statusFilter.addEventListener('change', performSearch);

            // Clear Filters button resets both fields visually
            const clearBtn = document.querySelector('.btn-light-danger.btn-sm');
            if (clearBtn) {
                clearBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    searchInput.value = '';
                    statusFilter.value = 'all';
                    if (window.jQuery && typeof $(statusFilter).select2 === 'function') {
                        $(statusFilter).val('all').trigger('change.select2');
                    }
                    window.location.href = clearBtn.getAttribute('href');
                });
            }

        });
    </script>
@endpush
