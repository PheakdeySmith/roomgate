<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        $users = collect();

        if ($currentUser->hasRole('admin')) {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'landlord');
            })->latest()->get();

        } elseif ($currentUser->hasRole('landlord')) {
            $users = User::where('landlord_id', $currentUser->id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'tenant');
                })->latest()->get();

        } else {
            return redirect()->route('unauthorized');
        }

        return view('backends.dashboard.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive',
        ]);

        $imageDbPath = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::slug($originalName) . '.' . $extension;
            $destinationPath = public_path('uploads/profile-photos');
            $relativeDbPath = 'uploads/profile-photos/' . $filename;
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }
            try {
                $file->move($destinationPath, $filename);
                $imageDbPath = $relativeDbPath;
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to upload image: ' . $e->getMessage())->withInput();
            }
        } else {
            $randomNumber = rand(1, 10);
            $imageDbPath = "assets/images/avatar-{$randomNumber}.jpg";
        }

        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'image' => $imageDbPath,
            'phone' => $validatedData['phone'],
            'status' => $validatedData['status'],
        ];

        $assignedRole = null;

        if ($currentUser->hasRole('admin')) {
            $userData['landlord_id'] = null;
            $assignedRole = 'landlord';
        } elseif ($currentUser->hasRole('landlord')) {
            $userData['landlord_id'] = $currentUser->id;
            $assignedRole = 'tenant';
        } else {
            return back()->with('error', 'You are not authorized to create this type of user.')->withInput();
        }

        if (!$assignedRole) {
            return back()->with('error', 'Role assignment failed due to invalid permissions or role type.')->withInput();
        }

        $user = User::create($userData);
        $user->assignRole($assignedRole);

        return back()->with('success', ucfirst($assignedRole) . ' created successfully.');
    }

    public function show(Request $request, User $user)
    {
        $currentUser = Auth::user();
        $basePath = $request->segment(1);

        if ($basePath === 'admin' && $currentUser->hasRole('admin') && $user->hasRole('landlord')) {
        } elseif ($basePath === 'landlord' && $currentUser->hasRole('landlord') && $user->landlord_id === $currentUser->id && $user->hasRole('tenant')) {
        } elseif ($currentUser->id === $user->id) {
        } else {
            return redirect()->route('unauthorized');
        }
        return view('backends.dashboard.users.show', compact('user', 'basePath'));
    }

    public function update(Request $request, User $user)
    {
        $loggedInUser = Auth::user();

        $validatedRequestData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive',
        ]);

        $targetUserCurrentRole = $user->getRoleNames()->first();

        $canUpdate = false;
        $roleSpecificDataForUpdate = [];

        if ($loggedInUser->hasRole('admin')) {
            if ($targetUserCurrentRole === 'landlord') {
                $canUpdate = true;
                $roleSpecificDataForUpdate['landlord_id'] = null;
            }
        } elseif ($loggedInUser->hasRole('landlord')) {
            if ($targetUserCurrentRole === 'tenant' && $user->landlord_id === $loggedInUser->id) {
                $canUpdate = true;
                $roleSpecificDataForUpdate['landlord_id'] = $loggedInUser->id;
            }
        }

        if (!$canUpdate) {
            return redirect()->route('unauthorized');
        }

        $updatePayload = [
            'name' => $validatedRequestData['name'],
            'email' => $validatedRequestData['email'],
            'phone' => $validatedRequestData['phone'],
            'status' => $validatedRequestData['status'],
        ];
        $updatePayload = array_merge($updatePayload, $roleSpecificDataForUpdate);

        if (!empty($validatedRequestData['password'])) {
            $updatePayload['password'] = Hash::make($validatedRequestData['password']);
        }

        if ($request->hasFile('image')) {
            if ($user->image && File::exists(public_path($user->image))) {
                File::delete(public_path($user->image));
            }
            $file = $request->file('image');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::slug($originalName) . '.' . $extension;
            $destinationPath = public_path('uploads/profile-photos');
            $relativeDbPath = 'uploads/profile-photos/' . $filename;
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }
            try {
                $file->move($destinationPath, $filename);
                $updatePayload['image'] = $relativeDbPath;
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to upload new image: ' . $e->getMessage())->withInput();
            }
        } elseif ($request->filled('remove_image') && $request->remove_image == '1') {
            if ($user->image && File::exists(public_path($user->image))) {
                File::delete(public_path($user->image));
            }
            $updatePayload['image'] = null;
        }

        $user->update($updatePayload);

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        $currentUser = Auth::user();
        $roleOfUserToDelete = $user->getRoleNames()->first();

        $canDelete = false;
        if ($currentUser->hasRole(roles: 'admin')) {
            if ($roleOfUserToDelete === 'landlord') {
                $canDelete = true;
            }
        } elseif ($currentUser->hasRole('landlord')) {
            if ($roleOfUserToDelete === 'tenant' && $user->landlord_id === $currentUser->id) {
                $canDelete = true;
            }
        }

        if (!$canDelete) {
            return back()->with('error', 'Unauthorized action to delete this user.');
        }

        if ($user->contracts()->exists()) {
            return back()->with('error', 'This user is associated with one or more contracts.');
        }

        if ($user->image && File::exists(public_path($user->image))) {
            File::delete(public_path($user->image));
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
