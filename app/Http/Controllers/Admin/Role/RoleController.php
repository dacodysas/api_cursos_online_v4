<?php

namespace App\Http\Controllers\Admin\Role;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::with('permissions')->orderBy('id', 'desc')->get();
        return response()->json([
            "roles" => $roles
        ]);
    }

    public function config()
    {
        $permissions = Permission::all();
        return response()->json([
            "permissions" => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => 'api']);
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        return response()->json([
            "role" => $role
        ]);
    }

    public function show($id)
{
    // 🚩 CARGA CORRECTA: Traemos el rol con sus permisos y los permisos de sus usuarios
    $role = Role::with(['permissions', 'users.permissions'])->findOrFail($id);

    return response()->json([
        "role" => $role,
        "permissions" => $role->permissions->pluck('name'),
        "users" => $role->users->map(function($user) {
            return [
                "id" => $user->id,
                "name" => $user->name,
                "surname" => $user->surname,
                "email" => $user->email,
                "avatar" => $user->avatar ? env("APP_URL")."storage/".$user->avatar : NULL,
                "created_at" => $user->created_at ? $user->created_at->format("Y-m-d") : NULL,
                // 🚩 ESTA LÍNEA ES VITAL: Pasa los permisos al Angular
                "permissions" => $user->permissions->pluck('name'), 
            ];
        })
    ]);
}

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        return response()->json([
            "role" => $role
        ]);
    }

    public function getUsersByRole(Request $request, $id)
{
    try {
        $role = Role::findOrFail($id);
        
        // Cargamos los usuarios con sus roles para la columna 'Role' de la tabla
        $users = $role->users()->with(['roles'])->get(); 

        return response()->json([
            "users" => $users->map(function($user) {
                return [
                    "id"         => $user->id,
                    "name"       => $user->name,
                    "surname"    => $user->surname,
                    "email"      => $user->email,
                    "avatar"     => $user->avatar ? env("APP_URL")."storage/".$user->avatar : NULL,
                    "roles"      => $user->roles, // Metronic usa esto para la etiqueta del rol
                    "created_at" => $user->created_at ? $user->created_at->format("Y-m-d H:i:s") : NULL,
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json(["error" => $e->getMessage()], 500);
    }
}

    public function removeUserFromRole($roleId, $userId)
    {
        $role = Role::findOrFail($roleId);
        $user = User::findOrFail($userId);

        $user->removeRole($role->name);

        return response()->json(["message" => 200]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json(["message" => 200]);
    }
}