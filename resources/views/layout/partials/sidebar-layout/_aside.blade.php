<!--begin::aside-->
<div id="kt_app_aside" class="app-aside flex-column" data-kt-drawer="true" data-kt-drawer-name="app-aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="auto"
    data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_aside_mobile_toggle">
    <!--begin::Wrapper-->
    <div id="kt_app_aside_wrapper"
        class="gap-4 py-5 d-flex flex-column align-items-center hover-scroll-y mt-lg-n3 py-lg-0" data-kt-scroll="true"
        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_header" data-kt-scroll-wrappers="#kt_app_aside_wrapper"
        data-kt-scroll-offset="5px">
        {{-- <a href="#" class="flex-shrink-0 btn btn-icon btn-color-primary bg-hover-body h-45px w-45px"
            data-bs-toggle="tooltip" title="Orders" data-bs-custom-class="tooltip-inverse">
            <i class="ki-outline ki-package fs-2x"></i>
        </a> --}}
        <a href="{{ route('catalog.products.index') }}"
            class="flex-shrink-0 btn btn-icon btn-color-warning bg-hover-body h-45px w-45px" data-bs-toggle="tooltip"
            title="Products" data-bs-custom-class="tooltip-inverse">
            <i class="ki-outline ki-cube-2 fs-2x"></i>
        </a>
        <a href="{{ route('customers.index') }}"
            class="flex-shrink-0 btn btn-icon btn-color-success bg-hover-body h-45px w-45px" data-bs-toggle="tooltip"
            title="Customers" data-bs-custom-class="tooltip-inverse">
            <i class="ki-outline ki-people fs-2x"></i>
        </a>
        {{-- <a href="#" class="flex-shrink-0 btn btn-icon btn-color-dark bg-hover-body h-45px w-45px"
            data-bs-toggle="tooltip" title="Settings" data-bs-custom-class="tooltip-inverse">
            <i class="ki-outline ki-setting-2 fs-2x"></i>
        </a> --}}
    </div>
    <!--end::Wrapper-->
</div>
<!--end::aside-->
