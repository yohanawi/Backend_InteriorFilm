<!--begin::Wrapper-->
<div id="kt_app_sidebar_wrapper" class="app-sidebar-wrapper">
    <div class="mx-4 my-5 hover-scroll-y my-lg-2" data-kt-scroll="true"
        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_header" data-kt-scroll-wrappers="#kt_app_sidebar_wrapper"
        data-kt-scroll-offset="5px">

        <div id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false"
            class="px-3 mb-5 app-sidebar-menu-primary menu menu-column menu-rounded menu-sub-indention menu-state-bullet-primary">
            <div class="menu-item">
                <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <span class="menu-icon">
                        <i class="ki-outline ki-home-2 fs-2"></i>
                    </span>
                    <span class="menu-title">Dashboards</span>
                </a>
            </div>
            <div class="pt-2 menu-item">
                <div class="menu-content">
                    <span class="menu-heading fw-bold text-uppercase fs-7">Catalog</span>
                </div>
            </div>

            <div class="menu-item">
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('catalog.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-abstract-26 fs-2"></i>
                        </span>
                        <span class="menu-title">Categories</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('catalog.catalogs.index') ? 'active' : '' }}"
                                href="{{ route('catalog.catalogs.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Category List</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('catalog.catalogs.create') ? 'active' : '' }}"
                                href="{{ route('catalog.catalogs.create') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">New Category</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <div data-kt-menu-trigger="click"
                                class="menu-item menu-accordion {{ request()->routeIs('catalog.categories.*') ? 'here show' : '' }}">
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="ki-outline ki-abstract-14 fs-2"></i>
                                    </span>
                                    <span class="menu-title">Sub Categories</span>
                                    <span class="menu-arrow"></span>
                                </span>

                                <div class="menu-sub menu-sub-accordion">
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('catalog.categories.index') ? 'active' : '' }}"
                                            href="{{ route('catalog.categories.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Sub Category List</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('catalog.categories.create') ? 'active' : '' }}"
                                            href="{{ route('catalog.categories.create') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">New Sub Category</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="menu-item">
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('catalog.products.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-parcel fs-2"></i>
                        </span>
                        <span class="menu-title">Products</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('catalog.products.index') ? 'active' : '' }}"
                                href="{{ route('catalog.products.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Product List</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('catalog.products.create') ? 'active' : '' }}"
                                href="{{ route('catalog.products.create') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">New Product</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="menu-item">
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('orders.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-purchase fs-2"></i>
                        </span>
                        <span class="menu-title">Orders</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('orders.index') ? 'active' : '' }}"
                                href="{{ route('orders.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Order List</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('orders.index', ['payment_status' => 'pending']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Pending Payments</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('orders.index', ['status' => 'refunded']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Refunds</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-2 menu-item">
                <div class="menu-content">
                    <span class="menu-heading fw-bold text-uppercase fs-7">Customers & Users</span>
                </div>
            </div>

            <div class="menu-item">
                <a class="menu-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                    href="{{ route('customers.index') }}">
                    <span class="menu-icon">
                        <i class="ki-outline ki-user fs-2"></i>
                    </span>
                    <span class="menu-title">Customers</span>
                </a>
            </div>

            <div class="menu-item">
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('user-management.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-people fs-2"></i>
                        </span>
                        <span class="menu-title">User Management</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div data-kt-menu-trigger="click" class="mb-1 menu-item menu-accordion">
                            <a class="menu-link {{ request()->routeIs('user-management.users.*') ? 'active' : '' }}"
                                href="{{ route('user-management.users.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Users</span>
                            </a>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                            <a class="menu-link {{ request()->routeIs('user-management.roles.*') ? 'active' : '' }}"
                                href="{{ route('user-management.roles.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Roles</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('user-management.permissions.*') ? 'active' : '' }}"
                                href="{{ route('user-management.permissions.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Permissions</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-2 menu-item">
                <div class="menu-content">
                    <span class="menu-heading fw-bold text-uppercase fs-7">CMS</span>
                </div>
            </div>

            <div class="menu-item">
                <a href="{{ route('pages.index') }}" class="menu-link {{ request()->routeIs('pages.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <i class="ki-outline ki-element-8 fs-2"></i>
                    </span>
                    <span class="menu-title">Pages</span>
                </a>
            </div>

            <div class="menu-item">
                <a class="menu-link {{ request()->routeIs('blogs.*') ? 'active' : '' }}"
                    href="{{ route('blogs.index') }}">
                    <span class="menu-icon">
                        <i class="ki-outline ki-document fs-2"></i>
                    </span>
                    <span class="menu-title">Blogs</span>
                </a>
            </div>

            <div class="menu-item">
                <a class="menu-link {{ request()->routeIs('wrapping-areas.*') ? 'active' : '' }}"
                    href="{{ route('wrapping-areas.index') }}">
                    <span class="menu-icon">
                        <i class="ki-outline ki-question fs-2"></i>
                    </span>
                    <span class="menu-title">Wrapping Areas</span>
                </a>
            </div>

            <div class="menu-item">
                <a class="menu-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}"
                    href="{{ route('contacts.index') }}">
                    <span class="menu-icon">
                        <i class="ki-outline ki-message-text-2 fs-2"></i>
                    </span>
                    <span class="menu-title">Contact Messages</span>
                </a>
            </div>
        </div>
    </div>
</div>
