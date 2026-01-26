<a href="{{ route('user-management.permissions.edit', $permission) }}"
    class="btn btn-icon btn-active-light-primary w-30px h-30px me-3">
    {!! getIcon('pencil', 'fs-3') !!}
</a>
<button class="btn btn-icon btn-active-light-primary w-30px h-30px" data-permission-id="{{ $permission->id }}"
    data-kt-action="delete_row">
    {!! getIcon('trash', 'fs-3') !!}
</button>
