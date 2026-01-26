<x-default-layout>
    @section('title')
        Add Customer
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('customers.create') }}
    @endsection

    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h2>Add New Customer</h2>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Form-->
        <form action="{{ route('customers.store') }}" method="POST" class="form">
            @csrf
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
                <div class="row mb-6">
                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="required form-label">First Name</label>
                        <input type="text" name="first_name"
                            class="form-control @error('first_name') is-invalid @enderror"
                            value="{{ old('first_name') }}" required />
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="required form-label">Last Name</label>
                        <input type="text" name="last_name"
                            class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}"
                            required />
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <!--begin::Row-->
                <div class="row mb-6">
                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="required form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}" />
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <!--begin::Row-->
                <div class="row mb-6">
                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="required form-label">Password</label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" required />
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="required form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required />
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <!--begin::Input group-->
                <div class="mb-6">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!--end::Input group-->

                <!--begin::Row-->
                <div class="row mb-6">
                    <!--begin::Col-->
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                            value="{{ old('city') }}" />
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control @error('state') is-invalid @enderror"
                            value="{{ old('state') }}" />
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-md-4">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control @error('country') is-invalid @enderror"
                            value="{{ old('country') }}" />
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <!--begin::Row-->
                <div class="row mb-6">
                    <!--begin::Col-->
                    <div class="col-md-6">
                        <label class="form-label">Postal Code</label>
                        <input type="text" name="postal_code"
                            class="form-control @error('postal_code') is-invalid @enderror"
                            value="{{ old('postal_code') }}" />
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
                            value="{{ old('date_of_birth') }}" />
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
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive
                        </option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Card body-->

            <!--begin::Card footer-->
            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('customers.index') }}" class="btn btn-light btn-active-light-primary me-2">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    Create Customer
                </button>
            </div>
            <!--end::Card footer-->
        </form>
        <!--end::Form-->
    </div>
</x-default-layout>
