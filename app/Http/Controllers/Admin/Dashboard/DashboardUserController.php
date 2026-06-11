<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardUserController extends Controller
{
    public function index(Request $request)
    {
        $q     = $request->get('q');
        $users = User::query()
            ->when($q, fn ($qb) => $qb->where('name_ar', 'like', "%$q%")
                                       ->orWhere('username', 'like', "%$q%")
                                       ->orWhere('phone', 'like', "%$q%")
                                       ->orWhere('email', 'like', "%$q%"))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name_ar'   => 'required|string|max:255',
            'username'  => "required|string|max:100|unique:users,username,{$user->id}|regex:/^[a-zA-Z0-9_.]+$/",
            'phone'     => "required|string|unique:users,phone,{$user->id}|regex:/^(?:\+962|00962|0)7\d{8}$/",
            'email'     => "nullable|email|unique:users,email,{$user->id}",
            'region'    => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]) + ['is_active' => $request->boolean('is_active', true)];

        $user->update($data);

        return redirect()->route('dashboard.users.index')
                         ->with('success', __('dashboard.updated_success'));
    }

    /** Toggle the user's enabled flag without leaving the index page. */
    public function toggleStatus(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);
        return back()->with('success', __('dashboard.updated_success'));
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        // Soft delete — preserves order history (the User model uses SoftDeletes).
        $user->delete();
        return redirect()->route('dashboard.users.index')
                         ->with('success', __('dashboard.deleted_success'));
    }
}