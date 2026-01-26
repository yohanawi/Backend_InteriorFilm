<x-default-layout>

    @section('title')
        Contact Message Details
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('apps.contacts.show', $contact) }}
    @endsection

    <div id="kt_app_content_container">
        @if (session('success'))
            <div class="mb-5 alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <div class="card-title fs-3 fw-bold">Contact Message Details</div>
                <div class="card-toolbar">
                    <span
                        class="badge badge-lg badge-light-{{ $contact->status_badge_color }}">{{ ucfirst($contact->status) }}</span>
                </div>
            </div>

            <div class="card-body p-9">
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Name</label>
                    <div class="col-lg-8">
                        <span class="text-gray-800 fw-bold fs-6">{{ $contact->name }}</span>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Email</label>
                    <div class="col-lg-8 fv-row">
                        <a href="mailto:{{ $contact->email }}"
                            class="text-gray-800 fw-semibold text-hover-primary">{{ $contact->email }}</a>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Phone Number</label>
                    <div class="col-lg-8 d-flex align-items-center">
                        <a href="tel:{{ $contact->phone }}"
                            class="text-gray-800 fw-semibold fs-6 text-hover-primary">{{ $contact->phone }}</a>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">IP Address</label>
                    <div class="col-lg-8">
                        <span class="text-gray-800 fw-semibold fs-6">{{ $contact->ip_address }}</span>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Submitted Date</label>
                    <div class="col-lg-8">
                        <span
                            class="text-gray-800 fw-semibold fs-6">{{ $contact->created_at->format('F d, Y - h:i A') }}</span>
                    </div>
                </div>

                <div class="mb-10 row">
                    <label class="col-lg-4 fw-semibold text-muted">Message</label>
                    <div class="col-lg-8">
                        <div class="p-5 rounded bg-light-primary">
                            <p class="mb-0 text-gray-800 fw-semibold fs-6">
                                {{ $contact->message }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="gap-3 d-flex justify-content-end">
                    @if ($contact->status !== 'replied')
                        <form action="{{ route('contacts.update-status', $contact->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="replied">
                            <button type="submit" class="btn btn-success">
                                <i class="ki-outline ki-check-circle fs-3"></i>
                                Mark as Replied
                            </button>
                        </form>
                    @endif

                    @if ($contact->status !== 'closed')
                        <form action="{{ route('contacts.update-status', $contact->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="closed">
                            <button type="submit" class="btn btn-secondary">
                                <i class="ki-outline ki-lock fs-3"></i>
                                Mark as Closed
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this contact message? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="ki-outline ki-trash fs-3"></i>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-5 card">
            <div class="card-header">
                <h3 class="card-title">Quick Reply</h3>
            </div>
            <div class="card-body">
                <p class="mb-3 text-muted">Reply directly to this contact via email:</p>
                <a href="mailto:{{ $contact->email }}?subject=Re: Your inquiry&body=Dear {{ $contact->name }},%0D%0A%0D%0AThank you for contacting Interior Film.%0D%0A%0D%0A"
                    class="btn btn-primary">
                    <i class="ki-outline ki-send fs-3"></i>
                    Open Email Client
                </a>
            </div>
        </div>
    </div>
</x-default-layout>
