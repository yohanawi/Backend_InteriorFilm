<x-default-layout>

    @section('title')
        Contact Message Details
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('apps.contacts.show', $contact) }}
    @endsection

    <div id="kt_app_content_container">
        <div class="row g-5">
            <!-- Left Column - Contact Info Card -->
            <div class="col-xl-4">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="text-gray-800 card-label fw-bold">Contact Information</span>
                            <span class="mt-1 text-gray-500 fw-semibold fs-6">Submitted
                                {{ $contact->created_at->diffForHumans() }}</span>
                        </h3>
                        <div class="card-toolbar">
                            <span class="badge badge-lg badge-light-{{ $contact->status_badge_color }} fs-7 fw-bold">
                                {{ ucfirst($contact->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-5 card-body">
                        <!-- Name -->
                        <div class="d-flex align-items-center mb-7">
                            <div class="symbol symbol-50px me-5">
                                <div class="symbol-label fs-2 fw-semibold bg-light-primary text-primary">
                                    {{ strtoupper(substr($contact->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-gray-500 fw-semibold d-block fs-7">Full Name</span>
                                <span class="text-gray-800 fw-bold fs-5">{{ $contact->name }}</span>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="d-flex align-items-center mb-7">
                            <div class="symbol symbol-50px me-5">
                                <div class="symbol-label">
                                    <i class="ki-outline ki-sms fs-2x text-info"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-gray-500 fw-semibold d-block fs-7">Email Address</span>
                                <a href="mailto:{{ $contact->email }}"
                                    class="text-gray-800 text-hover-primary fw-bold fs-6">
                                    {{ $contact->email }}
                                </a>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="d-flex align-items-center mb-7">
                            <div class="symbol symbol-50px me-5">
                                <div class="symbol-label">
                                    <i class="ki-outline ki-phone fs-2x text-success"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-gray-500 fw-semibold d-block fs-7">Phone Number</span>
                                <a href="tel:{{ $contact->phone }}"
                                    class="text-gray-800 text-hover-primary fw-bold fs-6">
                                    {{ $contact->phone }}
                                </a>
                            </div>
                        </div>

                        <div class="separator separator-dashed mb-7"></div>

                        <!-- IP Address -->
                        <div class="mb-5 d-flex align-items-center">
                            <div class="symbol symbol-40px me-4">
                                <div class="symbol-label bg-light">
                                    <i class="text-gray-600 ki-outline ki-geolocation fs-2"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-gray-500 fw-semibold d-block fs-8">IP Address</span>
                                <span class="text-gray-700 fw-semibold fs-7">{{ $contact->ip_address }}</span>
                            </div>
                        </div>

                        <!-- Submitted Date -->
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-4">
                                <div class="symbol-label bg-light">
                                    <i class="text-gray-600 ki-outline ki-calendar fs-2"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-gray-500 fw-semibold d-block fs-8">Submitted On</span>
                                <span
                                    class="text-gray-700 fw-semibold fs-7">{{ $contact->created_at->format('M d, Y - h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Message & Actions -->
            <div class="col-xl-8">
                <!-- Message Card -->
                <div class="mb-5 card card-flush">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="text-gray-800 card-label fw-bold">Message Content</span>
                            <span class="mt-1 text-gray-500 fw-semibold fs-7">What they want to tell you</span>
                        </h3>
                    </div>
                    <div class="pt-5 card-body">
                        <div class="p-8 rounded bg-light-primary position-relative">
                            <p class="mb-0 text-gray-800 fw-semibold fs-5 position-relative" style="line-height: 1.8;">
                                {{ $contact->message }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Reply Card -->
                <div class="mb-5 card card-flush">
                    <div class="pb-5 card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="text-gray-800 card-label fw-bold">Quick Reply</span>
                        </h3>
                    </div>
                    <div class="pt-0 card-body">
                        <div class="p-6 border border-dashed rounded notice d-flex bg-light-info border-info">
                            <i class="ki-outline ki-information-5 fs-2tx text-info me-4"></i>
                            <div class="flex-wrap d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <div class="text-gray-700 fs-6">
                                        Click the button below to open your email client and reply directly to
                                        <span class="text-gray-900 fw-bold">{{ $contact->name }}</span>
                                    </div>
                                </div>
                                <a href="mailto:{{ $contact->email }}?subject=Re: Your inquiry&body=Dear {{ $contact->name }},%0D%0A%0D%0AThank you for contacting Interior Film.%0D%0A%0D%0A"
                                    class="btn btn-info btn-sm ms-3">
                                    <i class="ki-outline ki-send fs-4 me-2"></i>
                                    Send Email
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="card card-flush">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="text-gray-800 card-label fw-bold">Manage Status</span>
                            <span class="mt-1 text-gray-500 fw-semibold fs-7">Update or delete this message</span>
                        </h3>
                    </div>
                    <div class="pt-5 card-body">
                        <div class="flex-wrap gap-3 d-flex">
                            @if ($contact->status !== 'replied')
                                <form action="{{ route('contacts.update-status', $contact->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="replied">
                                    <button type="submit" class="btn btn-success">
                                        <i class="ki-outline ki-check-circle fs-3 me-1"></i>
                                        Mark as Replied
                                    </button>
                                </form>
                            @endif

                            @if ($contact->status !== 'closed')
                                <form action="{{ route('contacts.update-status', $contact->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="closed">
                                    <button type="submit" class="btn btn-light-primary btn-active-primary">
                                        <i class="ki-outline ki-lock fs-3 me-1"></i>
                                        Mark as Closed
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST"
                                class="ms-auto delete-contact-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light-danger btn-active-danger">
                                    <i class="ki-outline ki-trash fs-3 me-1"></i>
                                    Delete Message
                                </button>
                            </form>


                            @push('scripts')
                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        document.querySelectorAll('.delete-contact-form').forEach(function(form) {
                                            form.addEventListener('submit', function(e) {
                                                e.preventDefault();
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: 'Are you sure you want to delete this contact message? This action cannot be undone.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#d33',
                                                    cancelButtonColor: '#3085d6',
                                                    confirmButtonText: 'Yes, delete it!'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        form.submit();
                                                    }
                                                });
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-default-layout>
