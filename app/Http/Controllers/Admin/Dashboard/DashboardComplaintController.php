<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Staff side of support conversations: inbox, thread view, replies.
 */
class DashboardComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $q      = $request->query('q');
        $status = $request->query('status');
        $unread = $request->boolean('unread');

        $complaints = Complaint::with(['user:id,name_ar,phone', 'latestMessage'])
            ->when($q, fn ($query) => $query->where(function ($sub) use ($q) {
                $sub->where('subject', 'like', "%$q%")
                    ->orWhere('message', 'like', "%$q%")
                    ->orWhere('contact_name', 'like', "%$q%")
                    ->orWhere('contact_phone', 'like', "%$q%")
                    ->orWhereHas('user', fn ($u) => $u->where('name_ar', 'like', "%$q%")
                        ->orWhere('phone', 'like', "%$q%"));
            }))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($unread, fn ($query) => $query->where('admin_unread', true))
            // Conversations waiting for us first, then most recent activity.
            ->orderByDesc('admin_unread')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.complaints.index', compact('complaints', 'q', 'status', 'unread'));
    }

    public function show(Complaint $complaint): View
    {
        // Opening the conversation marks it as read for staff.
        if ($complaint->admin_unread) {
            $complaint->update(['admin_unread' => false]);
        }

        $complaint->load(['user:id,name_ar,phone', 'messages.admin:id,name_ar']);

        return view('admin.complaints.show', compact('complaint'));
    }

    /** Staff reply — visible to the customer in the app. */
    public function reply(Request $request, Complaint $complaint): RedirectResponse
    {
        $data = $request->validate([
            'body'   => 'required|string|max:5000',
            'status' => 'nullable|in:new,in_review,resolved,closed',
        ]);

        $complaint->addMessage('admin', $data['body'], Auth::id());

        // Optional "reply and mark as resolved/closed" in one step.
        if (! empty($data['status'])) {
            $complaint->update(['status' => $data['status']]);
        }

        return redirect()->route('dashboard.complaints.show', $complaint)
            ->with('success', __('dashboard.complaint_reply_sent'));
    }

    /** Status + internal notes (never shown to the customer). */
    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $data = $request->validate([
            'status'      => 'required|in:new,in_review,resolved,closed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);
        $complaint->update($data);

        return redirect()->route('dashboard.complaints.show', $complaint)
            ->with('success', __('dashboard.complaint_updated'));
    }
}
