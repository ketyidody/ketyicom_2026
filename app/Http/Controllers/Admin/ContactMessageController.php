<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of contact messages.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        // Filter by read status
        if ($request->has('status') && $request->status !== 'all') {
            $isRead = $request->status === 'read';
            $query->where('is_read', $isRead);
        }

        // Search by name, email, or subject
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        }

        $messages = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/ContactMessages/Index', [
            'messages' => $messages,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Display a single contact message.
     */
    public function show(ContactMessage $contactMessage)
    {
        // Mark as read when viewed
        if (!$contactMessage->is_read) {
            $contactMessage->update(['is_read' => true]);
        }

        return Inertia::render('Admin/ContactMessages/Show', [
            'message' => $contactMessage,
        ]);
    }

    /**
     * Toggle read status.
     */
    public function toggleRead(ContactMessage $contactMessage)
    {
        $contactMessage->update(['is_read' => !$contactMessage->is_read]);

        return back()->with('success', 'Message status updated.');
    }

    /**
     * Delete a contact message.
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Contact message deleted successfully.');
    }
}
