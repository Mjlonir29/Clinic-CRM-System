<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\StaffDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubaccountController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'documents'])->latest()->get();
        $roles = Role::all();

        return view('subaccounts.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('subaccounts.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|string',
            'qualifications' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'experience_years' => 'nullable|string|max:50',
            'consultation_fee' => 'nullable|numeric|min:0',
            'cabin_number' => 'nullable|string|max:100',
            'working_hours' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'document_title' => 'nullable|string|max:255',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $role = Role::findOrFail($request->role_id);
        $permissions = $request->input('permissions', $role->permissions ?? []);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
            'role_slug' => $role->slug,
            'status' => $validated['status'],
            'qualifications' => $validated['qualifications'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'registration_number' => $validated['registration_number'] ?? null,
            'experience_years' => $validated['experience_years'] ?? null,
            'consultation_fee' => $validated['consultation_fee'] ?? 500,
            'cabin_number' => $validated['cabin_number'] ?? null,
            'working_hours' => $validated['working_hours'] ?? null,
            'permissions' => $permissions,
        ]);

        // Upload initial document if provided
        if ($request->hasFile('document_file') && $request->filled('document_title')) {
            $file = $request->file('document_file');
            $filename = time() . '_' . Str::slug($request->document_title) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('staff_documents/' . $user->id, $filename, 'public');

            StaffDocument::create([
                'user_id' => $user->id,
                'title' => $request->document_title,
                'file_path' => '/storage/' . $path,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
            ]);
        }

        return redirect()->route('subaccounts.index')
            ->with('success', "Staff account for {$user->name} created successfully with assigned role & login credentials.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|string',
            'password' => 'nullable|string|min:6',
            'qualifications' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'cabin_number' => 'nullable|string|max:100',
            'working_hours' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::findOrFail($request->role_id);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role_id' => $role->id,
            'role_slug' => $role->slug,
            'status' => $validated['status'],
            'qualifications' => $validated['qualifications'] ?? $user->qualifications,
            'specialization' => $validated['specialization'] ?? $user->specialization,
            'registration_number' => $validated['registration_number'] ?? $user->registration_number,
            'cabin_number' => $validated['cabin_number'] ?? $user->cabin_number,
            'working_hours' => $validated['working_hours'] ?? $user->working_hours,
            'permissions' => $request->input('permissions', []),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return back()->with('success', "Staff account & access permissions for {$user->name} saved successfully!");
    }

    public function uploadDocument(Request $request, User $user)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('staff_documents/' . $user->id, $filename, 'public');

            StaffDocument::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'file_path' => '/storage/' . $path,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'notes' => $request->notes,
            ]);

            return back()->with('success', 'Staff document uploaded successfully.');
        }

        return back()->with('error', 'Failed to upload document.');
    }

    public function deleteDocument(User $user, StaffDocument $document)
    {
        if ($document->user_id !== $user->id) {
            return back()->with('error', 'Unauthorized action.');
        }

        $document->delete();
        return back()->with('success', 'Document removed successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account while logged in.');
        }

        $user->delete();
        return back()->with('success', 'Staff account deleted successfully.');
    }
}
