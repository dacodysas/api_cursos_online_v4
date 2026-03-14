<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        // 1. Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $user = auth()->user();

        // 2. Usar el método hasPermission que creamos en el modelo User
        // Esto verifica si el rol del usuario tiene asignado el slug (ej: 'view_users')
        if (!$user->hasPermission($permission)) {
            return response()->json([
                'message' => "Acceso denegado. No tienes el permiso: [$permission]"
            ], 403);
        }

        return $next($request);
    }
}
