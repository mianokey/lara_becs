<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    /* ==========================
     *  ROLE MANAGEMENT
     * ========================== */

    public function rolesIndex()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $users = User::all();

        return view('admin.roles.index', compact('roles', 'permissions', 'users'));
    }

    public function createRole()
    {
        return view('admin.roles.create');
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function editRole($id)
    {
        $permissions = Permission::all();
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

public function updateRole(Request $request, $id)
{
    $role = Role::findOrFail($id);

    $request->validate([
        'name' => 'required|unique:roles,name,' . $role->id,
    ]);

    $role->update(['name' => $request->name]);

    // Convert IDs to names
    $permissions = Permission::whereIn('id', $request->permissions ?? [])->pluck('name')->toArray();

    // Sync by permission names
    $role->syncPermissions($permissions);

    return redirect()->route('admin.roles.index')->with('success', 'Role and permissions updated successfully.');
}



    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    public function assignPermissionToRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($request->role_id);
        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $role->syncPermissions($permissions);

        return redirect()->back()->with('success', 'Permissions updated successfully.');
    }

    public function assignRoleToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);

        $user->syncRoles($role);

        return redirect()->back()->with('success', 'Role assigned successfully.');
    }

    /* ==========================
     *  PERMISSION MANAGEMENT
     * ========================== */

    public function permissionsIndex()
    {
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function createPermission()
    {
        $permissions = Permission::all();
        return view('admin.permissions.create', compact('permissions'));
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function editPermission($id)
    {
        $permission = Permission::findOrFail($id);
        return view('admin.permissions.edit', compact('permission'));
    }

    public function updatePermission(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function deletePermission($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
