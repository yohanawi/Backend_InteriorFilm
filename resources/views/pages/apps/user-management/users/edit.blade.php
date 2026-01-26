<x-default-layout>

    @section('title')
        Edit User
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('user-management.users.edit', $user) }}
    @endsection

    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h3>Edit User: {{ $user->name }}</h3>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body">
            <form action="{{ route('user-management.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
 
                <!--begin::Input group-->
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Full Name<span class="text-danger">*</span></label>
                    <div class="col-lg-8">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter full name" value="{{ old('name', $user->name) }}" required />
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end::Input group-->

                <!--begin::Input group-->
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Email<span class="text-danger">*</span></label>
                    <div class="col-lg-8">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="Enter email address" value="{{ old('email', $user->email) }}" required />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end::Input group-->

                <!--begin::Input group-->
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Password<small class="text-muted ms-2">(Leave blank
                            to keep current)</small></label>
                    <div class="col-lg-8">
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Enter new password" />
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end::Input group-->

                <!--begin::Input group-->
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Confirm Password</label>
                    <div class="col-lg-8">
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Confirm new password" />
                    </div>
                </div>
                <!--end::Input group-->

                <!--begin::Input group-->
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Role<span class="text-danger">*</span></label>
                    <div class="col-lg-8">
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Select a role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ old('role', $userRole?->name) == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end::Input group-->

                <!--begin::Actions-->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('user-management.users.index') }}" class="btn btn-light me-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <span class="indicator-label">Update User</span>
                    </button>
                </div>
                <!--end::Actions-->
            </form>
        </div>
        <!--end::Card body-->
    </div>

</x-default-layout>
