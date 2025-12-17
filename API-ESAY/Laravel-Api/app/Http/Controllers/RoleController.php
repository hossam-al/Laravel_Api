<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Only Super Admin can view all roles
        if (!($user->role && $user->role->slug === 'super-admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $roles = Role::all();
        return response()->json(['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.    php artisan test
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Only Super Admin can create roles
        if (!($user->role && $user->role->slug === 'super-admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate request
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string'
        ]);

        // Generate slug from name
        $slug = Str::slug($request->name);

        // Create role
        $role = Role::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'role' => $role
        ], 201);

  
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        // Only Super Admin can view role details
        if (!($user->role && $user->role->slug === 'super-admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $role = Role::findOrFail($id);

        // Get users with this role
        $users = User::where('role_id', $role->id)->get(['id', 'name', 'email']);

        return response()->json([
            'role' => $role,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        // Only Super Admin can update roles
        if (!($user->role && $user->role->slug === 'super-admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $role = Role::findOrFail($id);

        // Prevent updating super-admin slug
        if ($role->slug === 'super-admin' && $request->has('name')) {
            return response()->json([
                'error' => 'Cannot modify the Super Admin role name'
            ], 422);
        }

        // Validate request
        $request->validate([
            'name' => 'sometimes|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string'
        ]);

        // Update role
        $role->update($request->only(['name', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        // Only Super Admin can delete roles
        if (!($user->role && $user->role->slug === 'super-admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $role = Role::findOrFail($id);

        // Prevent deleting super-admin or admin roles
        if (in_array($role->slug, ['super-admin', 'admin'])) {
            return response()->json([
                'error' => 'Cannot delete system roles'
            ], 422);
        }

        // Check if users are assigned to this role
        $usersCount = User::where('role_id', $role->id)->count();
        if ($usersCount > 0) {
            return response()->json([
                'error' => 'Cannot delete role with assigned users',
                'users_count' => $usersCount
            ], 422);
        }

        // Delete role
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request)
    {
        $user = Auth::user();

        // Only Super Admin can assign roles
        if (!($user->role && $user->role->slug === 'super-admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        $targetUser = User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);

        // Assign role
        $targetUser->role_id = $role->id;
        $targetUser->save();

        return response()->json([
            'success' => true,
            'message' => "Role {$role->name} assigned to {$targetUser->name} successfully"
        ]);
    }
}
