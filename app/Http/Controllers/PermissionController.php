<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller {
    
    public function rolesWithPermissions()
    {
        $roles = Role::where('name', '!=', 'Owner')->get(['id', 'name']);

        $permissions = Permission::select('id', 'name')->get();

        $rolePermissions = Role::with('permissions')
            ->where('name', '!=', 'Owner')
            ->get()
            ->mapWithKeys(fn($role) => [
                $role->id => $role->permissions->pluck('name')->toArray()
            ]);

        return response()->json([
            'roles'           => $roles,
            'permissions'     => $permissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    public function updateRolePermissions(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        if ($role->name === 'Owner') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Map permission names to IDs
        $permIds = Permission::whereIn('name', $validated['permissions'] ?? [])
            ->pluck('id')
            ->toArray();

        $role->permissions()->sync($permIds);

        return response()->json(['message' => 'Permissions updated successfully.']);
    }

    public function index()
    {
        return response()->json(Permission::latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|unique:permissions',
        ]);

        $permission = Permission::create($validated);

        return response()->json(['message' => 'Permission created.', 'data' => $permission]);
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name'  => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update($validated);

        return response()->json(['message' => 'Permission updated.', 'data' => $permission]);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json(['message' => 'Permission deleted.']);
    }
}

