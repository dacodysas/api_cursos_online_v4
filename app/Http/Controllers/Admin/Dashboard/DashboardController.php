<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course\Course;
use App\Models\Sale\Cart;
use App\Models\Course\Categorie;

class DashboardController extends Controller
{
    public function admin_config()
    {
        // 1. Usuarios: Conteo total
        $total_users = User::count();

        // 2. Categorías: Conteo total
        $total_categories = Categorie::count();

        // 3. Cursos: Conteo total (puedes filtrar por state si lo deseas)
        $total_courses = Course::count();

        // 4. Compras (Ventas): Suma de la columna 'total' de la tabla carts
        $total_sales = Cart::sum('total');

        // 5. Tabla de Usuarios Recientes (8 registros)
        $recent_users = User::orderBy('id', 'desc')->limit(8)->get();

        return response()->json([
            "total_users" => $total_users,
            "total_categories" => $total_categories,
            "total_courses" => $total_courses,
            "total_sales" => $total_sales,
            "recent_users" => [
                "data" => $recent_users
            ]
        ]);
    }
}