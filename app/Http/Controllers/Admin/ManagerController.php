<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    public function index()
    {
        $managers = User::whereIn('role', ['technical', 'financial', 'sales_manager'])->get();
        return view('admin.managers.index', compact('managers'));
    }

    public function create()
    {
        return view('admin.managers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:technical,financial,sales_manager',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'active';

        User::create($validated);

        return redirect()->route('admin.managers.index')
            ->with('success', 'Manager created successfully');
    }

    public function edit(User $manager)
    {
        return view('admin.managers.edit', compact('manager'));
    }

    public function update(Request $request, User $manager)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $manager->id,
            'role' => 'required|in:technical_manager,financial_manager,procurement_manager',
            'phone' => 'required|string',
            'address' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $manager->update($validated);

        return redirect()->route('admin.managers.index')
            ->with('success', 'Manager updated successfully');
    }
}
