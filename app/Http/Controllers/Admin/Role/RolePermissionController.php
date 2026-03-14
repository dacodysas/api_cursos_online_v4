<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index()
    {
        // 🔴 Esto es lo que enviamos a Angular para llenar los selects y checkboxes
        $permissions = Permission::all()->pluck('name');
        $roles = Role::all()->pluck('name');

        return response()->json([
            "permissions" => $permissions,
            "roles" => $roles
        ]);
    }
}