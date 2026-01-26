<x-default-layout>
    @section('title')
        Edit Customer
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('customers.edit', $customer) }}
    @endsection

    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h2>Edit Customer: {{ $customer->full_name }}</h2>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Form-->
        <form action="{{ route('customers.update', $customer) }}" method="POST" class="form">
            @csrf
            @method('PUT')
            <!--begin::Card body-->
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="fw-bold mb-2">Please fix the following errors:</div>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!--begin::Row-->
                <div class="mb-6 row">
                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="required form-label">First Name</label>
                        <input type="text" name="first_name"
                            class="form-control @error('first_name') is-invalid @enderror"
                            value="{{ old('first_name', $customer->first_name) }}" required />
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="required form-label">Last Name</label>
                        <input type="text" name="last_name"
                            class="form-control @error('last_name') is-invalid @enderror"
                            value="{{ old('last_name', $customer->last_name) }}" required />
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <!--begin::Row-->
                <div class="mb-6 row">
                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="required form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $customer->email) }}" required />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $customer->phone) }}" />
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <!--begin::Row-->
                <div class="mb-6 row">
                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" />
                        <div class="form-text">Leave blank to keep current password</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" />
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <!--begin::Input group-->
                <div class="mb-6">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $customer->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!--end::Input group-->

                <!--begin::Row-->
                <div class="mb-6 row">
                    <!--begin::Col-->
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                            value="{{ old('city', $customer->city) }}" />
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control @error('state') is-invalid @enderror"
                            value="{{ old('state', $customer->state) }}" />
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-md-4">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control @error('country') is-invalid @enderror"
                            value="{{ old('country', $customer->country) }}" />
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <!--begin::Row-->
                <div class="mb-6 row">
                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="form-label">Postal Code</label>
                        <input type="text" name="postal_code"
                            class="form-control @error('postal_code') is-invalid @enderror"
                            value="{{ old('postal_code', $customer->postal_code) }}" />
                        @error('postal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth"
                            class="form-control @error('date_of_birth') is-invalid @enderror"
                            value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}" />
                        @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <!--begin::Input group-->
                <div class="mb-6">
                    <label class="required form-label">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $customer->status) === 'active' ? 'selected' : '' }}>
                            Active</option>
                        <option value="inactive"
                            {{ old('status', $customer->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended"
                            {{ old('status', $customer->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Card body-->

            <!--begin::Card footer-->
            <div class="py-6 card-footer d-flex justify-content-end px-9">
                <a href="{{ route('customers.index') }}" class="btn btn-light btn-active-light-primary me-2">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    Update Customer
                </button>
            </div>
            <!--end::Card footer-->
        </form>
        <!--end::Form-->
    </div>
</x-default-layout>
