<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::query()->with(['orders', 'addresses']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Email verification filter
        if ($request->filled('verified')) {
            if ($request->verified === 'yes') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $customers = $query->paginate(15)->withQueryString();

        return view('pages.apps.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('pages.apps.customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:customers,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        try {
            $customer = Customer::create($validated);

            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create customer. Please try again.');
        }
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        $customer->load(['orders.items', 'addresses', 'wishlistProducts']);
        $recentOrders = $customer->orders()->latest()->take(5)->get();
        $totalOrders = $customer->orders()->count();
        $totalSpent = $customer->orders()->where('payment_status', 'paid')->sum('total_amount');

        return view('pages.apps.customers.show', compact('customer', 'recentOrders', 'totalOrders', 'totalSpent'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        return view('pages.apps.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('customers')->ignore($customer->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        try {
            $customer->update($validated);

            return redirect()->route('customers.index')
                ->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update customer. Please try again.');
        }
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();

            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete customer. Please try again.');
        }
    }

    /**
     * Restore the specified customer.
     */
    public function restore($id)
    {
        try {
            $customer = Customer::withTrashed()->findOrFail($id);
            $customer->restore();

            return redirect()->route('customers.index')
                ->with('success', 'Customer restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore customer.');
        }
    }

    /**
     * Permanently delete the specified customer.
     */
    public function forceDelete($id)
    {
        try {
            $customer = Customer::withTrashed()->findOrFail($id);
            $customer->forceDelete();

            return redirect()->route('customers.index')
                ->with('success', 'Customer permanently deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to permanently delete customer.');
        }
    }

    /**
     * Verify customer email.
     */
    public function verifyEmail(Customer $customer)
    {
        try {
            $customer->update(['email_verified_at' => now()]);

            return back()->with('success', 'Customer email verified successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to verify email.');
        }
    }

    /**
     * Export customers to CSV.
     */
    public function export(Request $request)
    {
        $query = Customer::query();

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $customers = $query->get();

        $filename = 'customers_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($customers) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'City',
                'State',
                'Country',
                'Postal Code',
                'Date of Birth',
                'Status',
                'Email Verified',
                'Created At'
            ]);

            // Add data rows
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->id,
                    $customer->first_name,
                    $customer->last_name,
                    $customer->email,
                    $customer->phone ?? '',
                    $customer->city ?? '',
                    $customer->state ?? '',
                    $customer->country ?? '',
                    $customer->postal_code ?? '',
                    $customer->date_of_birth?->format('Y-m-d') ?? '',
                    $customer->status,
                    $customer->email_verified_at ? 'Yes' : 'No',
                    $customer->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk update customer status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'customer_ids' => ['required', 'array'],
            'customer_ids.*' => ['exists:customers,id'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        try {
            Customer::whereIn('id', $validated['customer_ids'])
                ->update(['status' => $validated['status']]);

            return back()->with('success', 'Customers updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update customers.');
        }
    }

    /**
     * Bulk delete customers.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'customer_ids' => ['required', 'array'],
            'customer_ids.*' => ['exists:customers,id'],
        ]);

        try {
            Customer::whereIn('id', $validated['customer_ids'])->delete();

            return back()->with('success', 'Customers deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete customers.');
        }
    }
}
