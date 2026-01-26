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
        $query = ContactMessage::query()->latest();

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $contacts = $query->paginate(10);

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
