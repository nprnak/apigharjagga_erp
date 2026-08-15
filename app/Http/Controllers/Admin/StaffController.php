<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StaffController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Staff/Index', [
            'staff' => Staff::with('role')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Staff/Form', [
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_id'     => ['required', 'exists:roles,role_id'],
            'full_name'   => ['required', 'string', 'max:150'],
            'designation' => ['nullable', 'string', 'max:100'],
            'mobile_no'   => ['nullable', 'string', 'max:20'],
            'email'       => ['nullable', 'email', 'max:150'],
        ]);

        Staff::create($validated);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member added.');
    }

    public function edit(Staff $staff): Response
    {
        return Inertia::render('Admin/Staff/Form', [
            'staff' => $staff,
            'roles' => Role::all(),
        ]);
    }

    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'role_id'     => ['required', 'exists:roles,role_id'],
            'full_name'   => ['required', 'string', 'max:150'],
            'designation' => ['nullable', 'string', 'max:100'],
            'mobile_no'   => ['nullable', 'string', 'max:20'],
            'email'       => ['nullable', 'email', 'max:150'],
            'is_active'   => ['boolean'],
        ]);

        $staff->update($validated);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated.');
    }
}
