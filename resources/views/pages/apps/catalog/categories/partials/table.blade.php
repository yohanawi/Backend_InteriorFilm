<div class="table-responsive">
    <table class="table align-middle table-row-dashed fs-6 gy-5">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-50px">Image</th>
                <th class="min-w-125px">Name</th>
                <th class="min-w-125px">Category</th>
                <th class="min-w-100px">Products</th>
                <th class="min-w-100px">Status</th>
                <th class="min-w-100px">Sort Order</th>
                <th class="text-end min-w-100px">Actions</th>
            </tr>
        </thead>

        <tbody class="text-gray-600 fw-semibold">
            @forelse($categories as $category)
                <tr>
                    <td>
                        @if ($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" class="rounded w-50px h-50px">
                        @else
                            <span class="symbol-label bg-light-info text-info fw-bold">
                                {{ substr($category->name, 0, 1) }}
                            </span>
                        @endif
                    </td>

                    <td>{{ $category->name }}</td>
                    <td>{{ $category->catalog?->name }}</td>

                    <td>
                        <span class="badge badge-light-success">
                            {{ $category->products_count }}
                        </span>
                    </td>

                    <td>
                        @if ($category->is_active)
                            <span class="badge badge-light-success">Active</span>
                        @else
                            <span class="badge badge-light-danger">Inactive</span>
                        @endif
                    </td>

                    <td>{{ $category->sort_order }}</td>

                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-light btn-active-light-primary btn-sm dropdown-toggle" type="button"
                                id="actionsDropdown{{ $category->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="actionsDropdown{{ $category->id }}">
                                <li>
                                    <a class="dropdown-item" href="{{ route('catalog.categories.show', $category) }}">
                                        View
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('catalog.categories.edit', $category) }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                        onclick="deleteCategory({{ $category->id }}); return false;">
                                        Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-10 text-center text-gray-500">
                        No categories found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5 d-flex justify-content-between align-items-center">
    <div class="text-muted fs-7">
        Showing {{ $categories->firstItem() ?? 0 }}
        to {{ $categories->lastItem() ?? 0 }}
        of {{ $categories->total() }} entries
    </div>

    {!! $categories->links('pagination::bootstrap-5') !!}
</div>
