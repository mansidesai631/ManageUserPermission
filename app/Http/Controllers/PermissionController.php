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
}

