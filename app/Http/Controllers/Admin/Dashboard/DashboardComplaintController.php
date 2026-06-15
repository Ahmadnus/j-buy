<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->query('q');
        $status = $request->query('status');

        $complaints = Complaint::with('user:id,name_ar,phone')
            ->when($q, fn($query) => $query->where(function ($sub) use ($q) {
                $sub->where('subject', 'like', "%$q%")
                    ->orWhere('message', 'like', "%$q%");
            }))
            ->when($status, fn($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.complaints.index', compact('complaints', 'q', 'status'));
    }

    public function show(Complaint $complaint): View
    {
        $complaint->load('user:id,name_ar,phone');
        return view('admin.complaints.show', compact('complaint'));
    }

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