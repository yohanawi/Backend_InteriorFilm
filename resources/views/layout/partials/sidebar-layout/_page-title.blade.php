<div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
    <!--begin::Page title-->
    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
        <!--begin::Title-->
        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
            @hasSection('title')
                @yield('title')
            @else
                Dashboard
            @endif
        </h1>
        <!--end::Title-->
        <!--begin::Breadcrumb-->
        @hasSection('breadcrumbs')
            @yield('breadcrumbs')
        @else
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
            </ul>
        @endif
        <!--end::Breadcrumb-->
    </div>
    <!--end::Page title-->
    <!--begin::Actions-->
    @if (request()->routeIs('dashboard'))
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="#"
                class="btn btn-flex btn-outline btn-color-gray-700 btn-active-color-primary bg-body h-40px fs-7 fw-bold"
                data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">Add
                Member</a>
            <a href="#" class="btn btn-flex btn-primary h-40px fs-7 fw-bold" data-bs-toggle="modal"
                data-bs-target="#kt_modal_create_campaign">New Campaign</a>
        </div>
    @endif
    <!--end::Actions-->
</div>
