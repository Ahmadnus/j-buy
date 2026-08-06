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

        // Rules are declared as arrays, not pipe-delimited strings: the phone
        // pattern contains '|' characters, which Laravel would otherwise treat
        // as rule separators and split the regex mid-expression.
        $data = $request->validate([
            'name_ar'   => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'max:100',
                            "unique:users,username,{$user->id}",
                            'regex:/^[a-zA-Z0-9_.]+$/'],
            'phone'     => ['required', 'string',
                            "unique:users,phone,{$user->id}",
                            'regex:/^(?:\+962|00962|0)7\d{8}$/'],
            'email'     => ['nullable', 'email', "unique:users,email,{$user->id}"],
            'region'    => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // 'is_active' is absent from User::$fillable, so update() would discard
        // it silently. Assign it directly instead.
        unset($data['is_active']);
        $user->update($data);
        $user->is_active = $request->boolean('is_active', true);
        $user->save();

        return redirect()->route('dashboard.users.index')
                         ->with('success', __('dashboard.updated_success'));
    }

    /** Toggle the user's enabled flag without leaving the index page. */
    public function toggleStatus(int $id)
    {
        $user = User::findOrFail($id);
        // Direct assignment: 'is_active' is not in User::$fillable, so update()
        // would drop it and the toggle would appear to do nothing.
        $user->is_active = ! $user->is_active;
        $user->save();
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