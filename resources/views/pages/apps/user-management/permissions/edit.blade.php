<x-default-layout>

    @section('title')
        Edit Permission
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('user-management.permissions.edit', $permission) }}
    @endsection

    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h3>Edit Permission: {{ $permission->name }}</h3>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body">
            <form action="{{ route('user-management.permissions.update', $permission) }}" method="POST">
                @csrf
                @method('PUT')

                <!--begin::Input group-->
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">
                        Permission Name
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-8">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            placeholder="e.g., user-create, role-edit" value="{{ old('name', $permission->name) }}"
                            required />
                        <div class="form-text">Use format: module-action (e.g., user-create, role-edit)</div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end::Input group-->

                <!--begin::Actions-->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('user-management.permissions.index') }}" class="btn btn-light me-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <span class="indicator-label">Update Permission</span>
                    </button>
                </div>
                <!--end::Actions-->
            </form>
        </div>
        <!--end::Card body-->
    </div>

</x-default-layout>
