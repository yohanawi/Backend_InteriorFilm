<?php

use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Home
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});

// Home > Dashboard > User Management
Breadcrumbs::for('user-management.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('User Management', route('user-management.users.index'));
});

// Home > Dashboard > User Management > Users
Breadcrumbs::for('user-management.users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push('Users', route('user-management.users.index'));
});

// Home > Dashboard > User Management > Users > Create
Breadcrumbs::for('user-management.users.create', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.users.index');
    $trail->push('Create User', route('user-management.users.create'));
});

// Home > Dashboard > User Management > Users > [User]
Breadcrumbs::for('user-management.users.show', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('user-management.users.index');
    $trail->push(ucwords($user->name), route('user-management.users.show', $user));
});

// Home > Dashboard > User Management > Users > Edit [User]
Breadcrumbs::for('user-management.users.edit', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('user-management.users.index');
    $trail->push('Edit ' . ucwords($user->name), route('user-management.users.edit', $user));
});

// Home > Dashboard > User Management > Roles
Breadcrumbs::for('user-management.roles.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push('Roles', route('user-management.roles.index'));
});

// Home > Dashboard > User Management > Roles > Create
Breadcrumbs::for('user-management.roles.create', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.roles.index');
    $trail->push('Create Role', route('user-management.roles.create'));
});

// Home > Dashboard > User Management > Roles > [Role]
Breadcrumbs::for('user-management.roles.show', function (BreadcrumbTrail $trail, Role $role) {
    $trail->parent('user-management.roles.index');
    $trail->push(ucwords($role->name), route('user-management.roles.show', $role));
});

// Home > Dashboard > User Management > Roles > Edit [Role]
Breadcrumbs::for('user-management.roles.edit', function (BreadcrumbTrail $trail, Role $role) {
    $trail->parent('user-management.roles.index');
    $trail->push('Edit ' . ucwords($role->name), route('user-management.roles.edit', $role));
});

// Home > Dashboard > User Management > Permissions
Breadcrumbs::for('user-management.permissions.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push('Permissions', route('user-management.permissions.index'));
});

// Home > Dashboard > User Management > Permissions > Create
Breadcrumbs::for('user-management.permissions.create', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.permissions.index');
    $trail->push('Create Permission', route('user-management.permissions.create'));
});

// Home > Dashboard > User Management > Permissions > Edit [Permission]
Breadcrumbs::for('user-management.permissions.edit', function (BreadcrumbTrail $trail, Permission $permission) {
    $trail->parent('user-management.permissions.index');
    $trail->push('Edit ' . ucwords($permission->name), route('user-management.permissions.edit', $permission));
});


// Home > Dashboard > Catalogs
Breadcrumbs::for('catalog.catalogs.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Categories', route('catalog.catalogs.index'));
});

// Home > Dashboard > Catalogs > Create
Breadcrumbs::for('catalog.catalogs.create', function (BreadcrumbTrail $trail) {
    $trail->parent('catalog.catalogs.index');
    $trail->push('Create Categories', route('catalog.catalogs.create'));
});

// Home > Dashboard > Catalogs > [Catalog]
Breadcrumbs::for('catalog.catalogs.show', function (BreadcrumbTrail $trail, $catalog) {
    $trail->parent('catalog.catalogs.index');
    $trail->push($catalog->name, route('catalog.catalogs.show', $catalog));
});

// Home > Dashboard > Catalogs > Edit [Catalog]
Breadcrumbs::for('catalog.catalogs.edit', function (BreadcrumbTrail $trail, $catalog) {
    $trail->parent('catalog.catalogs.index');
    $trail->push('Edit ' . $catalog->name, route('catalog.catalogs.edit', $catalog));
});

// Home > Dashboard > Sub Categories
Breadcrumbs::for('catalog.categories.index', function (BreadcrumbTrail $trail) {
    $trail->parent('catalog.catalogs.index');
    $trail->push('Sub Categories', route('catalog.categories.index'));
});

// Home > Dashboard > Sub Categories > Create
Breadcrumbs::for('catalog.categories.create', function (BreadcrumbTrail $trail) {
    $trail->parent('catalog.categories.index');
    $trail->push('Create Sub Category', route('catalog.categories.create'));
});

// Home > Dashboard > Sub Categories > [Sub Category]
Breadcrumbs::for('catalog.categories.show', function (BreadcrumbTrail $trail, $category) {
    $trail->parent('catalog.categories.index');
    $trail->push($category->name, route('catalog.categories.show', $category));
});

// Home > Dashboard > Sub Categories > Edit [Sub Category]
Breadcrumbs::for('catalog.categories.edit', function (BreadcrumbTrail $trail, $category) {
    $trail->parent('catalog.categories.index');
    $trail->push('Edit ' . $category->name, route('catalog.categories.edit', $category));
});

// Home > Dashboard > Products
Breadcrumbs::for('catalog.products.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Products', route('catalog.products.index'));
});

// Home > Dashboard > Products > Create
Breadcrumbs::for('catalog.products.create', function (BreadcrumbTrail $trail) {
    $trail->parent('catalog.products.index');
    $trail->push('Create Product', route('catalog.products.create'));
});

// Home > Dashboard > Products > [Product]
Breadcrumbs::for('catalog.products.show', function (BreadcrumbTrail $trail, $product) {
    $trail->parent('catalog.products.index');
    $trail->push($product->name, route('catalog.products.show', $product));
});

// Home > Dashboard > Products > Edit [Product]
Breadcrumbs::for('catalog.products.edit', function (BreadcrumbTrail $trail, $product) {
    $trail->parent('catalog.products.index');
    $trail->push('Edit ' . $product->name, route('catalog.products.edit', $product));
});

// Home > Dashboard > Customers
Breadcrumbs::for('customers.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Customers', route('customers.index'));
});

// Home > Dashboard > Customers > Create
Breadcrumbs::for('customers.create', function (BreadcrumbTrail $trail) {
    $trail->parent('customers.index');
    $trail->push('Create Customer', route('customers.create'));
});

// Home > Dashboard > Customers > [Customer]
Breadcrumbs::for('customers.show', function (BreadcrumbTrail $trail, $customer) {
    $trail->parent('customers.index');
    $trail->push($customer->full_name, route('customers.show', $customer));
});

// Home > Dashboard > Customers > Edit [Customer]
Breadcrumbs::for('customers.edit', function (BreadcrumbTrail $trail, $customer) {
    $trail->parent('customers.index');
    $trail->push('Edit ' . $customer->full_name, route('customers.edit', $customer));
});
// Home > Dashboard > Orders
Breadcrumbs::for('orders.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Orders', route('orders.index'));
});

// Home > Dashboard > Orders > [Order]
Breadcrumbs::for('orders.show', function (BreadcrumbTrail $trail, $order) {
    $trail->parent('orders.index');
    $trail->push('Order #' . $order->order_number, route('orders.show', $order));
});

// Home > Dashboard > Blogs
Breadcrumbs::for('apps.blogs.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Blogs', route('blogs.index'));
});

// Home > Dashboard > Blogs > Create
Breadcrumbs::for('apps.blogs.create', function (BreadcrumbTrail $trail) {
    $trail->parent('apps.blogs.index');
    $trail->push('Create Blog', route('blogs.create'));
});

// Home > Dashboard > Blogs > Update
Breadcrumbs::for('apps.blogs.update', function (BreadcrumbTrail $trail) {
    $trail->parent('apps.blogs.index');
    $trail->push('Update Blog', route('blogs.create'));
});

// Home > Contact Messages
Breadcrumbs::for('apps.contacts.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Contact Messages', route('contacts.index'));
});

// Home > Contact Messages > [Contact Message]
Breadcrumbs::for('apps.contacts.show', function (BreadcrumbTrail $trail, $contact) {
    $trail->parent('apps.contacts.index');
    $trail->push('Message from ' . $contact->name, route('contacts.show', $contact));
});

// Home > Dashboard > Wrapping Areas
Breadcrumbs::for('wrapping-areas.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Wrapping Areas', route('wrapping-areas.index'));
});

// Home > Dashboard > Wrapping Areas > Create
Breadcrumbs::for('wrapping-areas.create', function (BreadcrumbTrail $trail) {
    $trail->parent('wrapping-areas.index');
    $trail->push('Create Wrapping Area', route('wrapping-areas.create'));
});

// Home > Dashboard > Wrapping Areas > [Wrapping Area]
Breadcrumbs::for('wrapping-areas.show', function (BreadcrumbTrail $trail, $wrappingArea) {
    $trail->parent('wrapping-areas.index');
    $trail->push($wrappingArea->title, route('wrapping-areas.show', $wrappingArea));
});

// Home > Dashboard > Wrapping Areas > Edit [Wrapping Area]
Breadcrumbs::for('wrapping-areas.edit', function (BreadcrumbTrail $trail, $wrappingArea) {
    $trail->parent('wrapping-areas.index');
    $trail->push('Edit ' . $wrappingArea->title, route('wrapping-areas.edit', $wrappingArea));
});
