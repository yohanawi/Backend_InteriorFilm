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
                <div class="gap-2 d-flex justify-content-end" data-kt-customer-table-toolbar="base">                    
                    <select class="form-select form-select-solid w-150px" id="status-filter">
                        <option value="all">All Status</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied
                        </option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed
                        </option>
                    </select>                    
                </div>                
            </div>            
        </div>        
        
        <div class="pt-0 card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
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
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                        <i class="ki-outline ki-down fs-5 ms-1"></i></a>                                    
                                    <div class="py-4 menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px"
                                        data-kt-menu="true">                                        
                                        <div class="px-3 menu-item">
                                            <a href="{{ route('contacts.show', $contact->id) }}"
                                                class="px-3 menu-link">View Details</a>
                                        </div>                                        

                                        @if ($contact->status !== 'replied')                                            
                                            <div class="px-3 menu-item">
                                                <form action="{{ route('contacts.update-status', $contact->id) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="replied">
                                                    <button type="submit"
                                                        class="p-0 px-3 menu-link btn btn-link text-start w-100">Mark
                                                        as Replied</button>
                                                </form>
                                            </div>                                            
                                        @endif

                                        @if ($contact->status !== 'closed')                                            
                                            <div class="px-3 menu-item">
                                                <form action="{{ route('contacts.update-status', $contact->id) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="closed">
                                                    <button type="submit"
                                                        class="p-0 px-3 menu-link btn btn-link text-start w-100">Mark
                                                        as Closed</button>
                                                </form>
                                            </div>                                            
                                        @endif
                                        
                                        <div class="px-3 menu-item">
                                            <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this contact message?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-0 px-3 menu-link btn btn-link text-danger text-start w-100">Delete</button>
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
                                        <span class="text-gray-600 fs-5">No contact
                                            messages
                                            found</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>            
            
            @if ($contacts->hasPages())
                <div class="flex-wrap pt-5 d-flex justify-content-between align-items-center">
                    <div class="text-gray-700 fs-6 fw-semibold">
                        Showing {{ $contacts->firstItem() }} to
                        {{ $contacts->lastItem() }}
                        of {{ $contacts->total() }} entries
                    </div>
                    {{ $contacts->links() }}
                </div>
            @endif            
        </div>        
    </div>

</x-default-layout>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Search functionality
            $('#search-input').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    filterContacts();
                }
            });

            // Status filter
            $('#status-filter').on('change', function() {
                filterContacts();
            });

            function filterContacts() {
                const search = $('#search-input').val();
                const status = $('#status-filter').val();

                const url = new URL(window.location.href);
                url.searchParams.set('search', search);
                url.searchParams.set('status', status);

                window.location.href = url.toString();
            }
        });
    </script>
@endpush
