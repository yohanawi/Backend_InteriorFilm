<x-default-layout>

    @section('title')
        Edit Role
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('user-management.roles.edit', $role) }}
    @endsection

    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h3>Edit Role: {{ $role->name }}</h3>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body">
            <form action="{{ route('user-management.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                <!--begin::Input group-->
                <div class="mb-10">
                    <label class="form-label required">Role Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        placeholder="Enter role name" value="{{ old('name', $role->name) }}" required />
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!--end::Input group-->

                <!--begin::Permissions-->
                <div class="mb-10">
                    <label class="form-label">Permissions</label>
                    <div class="row">
                        @foreach ($permissions as $module => $modulePermissions)
                            <div class="col-md-6 mb-5">
                                <div class="card card-flush h-100">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h4 class="text-gray-800">{{ ucfirst($module) }}</h4>
                                        </div>
                                    </div>
                                    <div class="card-body pt-1">
                                        @foreach ($modulePermissions as $permission)
                                            <div class="form-check form-check-custom form-check-solid mb-3">
                                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                                    value="{{ $permission->name }}"
                                                    id="permission_{{ $permission->id }}"
                                                    {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }} />
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    {{ ucwords(str_replace('-', ' ', $permission->name)) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!--end::Permissions-->

                <!--begin::Actions-->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('user-management.roles.index') }}" class="btn btn-light me-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <span class="indicator-label">Update Role</span>
                    </button>
                </div>
                <!--end::Actions-->
            </form>
        </div>
        <!--end::Card body-->
    </div>

</x-default-layout>
