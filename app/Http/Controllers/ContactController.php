<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the contact messages.
     */
    public function index(Request $request)
    {
        $contacts = ContactMessage::query()
            ->latest()
            // Filter by status if provided and not 'all'
            ->when($request->filled('status') && $request->status !== 'all', function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            // Search by multiple columns
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString(); // This keeps the search/filter params in pagination links

        return view('pages.apps.contacts.index', compact('contacts'));
    }

    /**
     * Display the specified contact message.
     */
    public function show(ContactMessage $contact)
    {
        return view('pages.apps.contacts.show', compact('contact'));
    }

    /**
     * Update the status of the specified contact message.
     */
    public function updateStatus(Request $request, ContactMessage $contact)
    {
        $request->validate([
            'status' => 'required|in:new,replied,closed',
        ]);

        $contact->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Contact status updated successfully.');
    }

    /**
     * Remove the specified contact message from storage.
     */
    public function destroy(ContactMessage $contact)
    {
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact message deleted successfully.');
    }
}
