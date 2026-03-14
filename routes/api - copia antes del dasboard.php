<?php

use App\Http\Controllers\Admin\Coupon\CouponController;
use App\Http\Controllers\Admin\Course\CategorieController;
use App\Http\Controllers\Admin\Course\ClaseGController;
use App\Http\Controllers\Admin\Course\CourseGController;
use App\Http\Controllers\Admin\Course\SeccionGController;
use App\Http\Controllers\Admin\Discount\DiscountController;
use App\Http\Controllers\Admin\Role\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pages\AboutController;
use App\Http\Controllers\Tienda\CartController;
use App\Http\Controllers\Tienda\HomeController;
use Illuminate\Support\Facades\Route;

// 1. AUTENTICACIÓN
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login_tienda', [AuthController::class, 'login_tienda']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/me', [AuthController::class, 'me']);
});

// 2. RUTAS PROTEGIDAS
Route::group([
    'middleware' => ['auth:api'], 
], function ($router) {

    // --- GESTIÓN DE ROLES ---
    Route::group(['middleware' => ['role:Administrador,api']], function() {
        
        /* 🔴 MEJORA: Estas rutas deben ir antes de resource('roles') */
        Route::get('/roles/users/{id}', [RoleController::class, 'getUsersByRole']);
        Route::get('/roles/config', [RoleController::class, 'config']);
        Route::delete('/roles/{roleId}/users/{userId}', [RoleController::class, 'removeUserFromRole']);

        Route::resource('/roles', RoleController::class);
    });

    // --- USUARIOS ---
    Route::group(['middleware' => ['permission:view_users,api']], function() {
        /* 🔴 MEJORA: El POST de update debe ir antes para que Laravel no lo confunda con el SHOW */
        Route::post('/users/{id}', [UserController::class, "update"]);
        Route::resource('/users', UserController::class);
    });

    // --- CATEGORÍAS ---
    Route::group(['middleware' => ['permission:view_categories,api']], function() {
        /* 🚀 NUEVA RUTA PARA PERMISOS */
        Route::post('/users/{id}/permissions', [UserController::class, "updatePermissions"]);
        /* 🔴 MEJORA: El POST de update debe ir antes */
        Route::post('/categorie/{id}', [CategorieController::class, "update"]);
        Route::resource('/categorie', CategorieController::class);
    });

    // --- CURSOS, SECCIONES Y CLASES ---
    Route::group(['middleware' => ['permission:view_courses,api']], function() {
        
        // Cursos
        Route::get('/course/config', [CourseGController::class, "config"]);
        Route::post('/course/upload_video/{id}', [CourseGController::class, "upload_video"]);
        Route::post('/course/{id}', [CourseGController::class, "update"]);
        Route::resource('/course', CourseGController::class);

        // Secciones
        Route::resource('/course-section', SeccionGController::class);

        // Clases
        /* 🔴 MEJORA: Las rutas de archivos y videos de clases deben ir ANTES del resource */
        Route::post('/course-clases-file', [ClaseGController::class, "addFiles"]);
        Route::delete('/course-clases-file/{id}', [ClaseGController::class, "removeFiles"]);
        Route::post('/course-clases/upload_video/{id}', [ClaseGController::class, "upload_video"]);
        Route::resource('/course-clases', ClaseGController::class);
    });

    // --- CUPONES Y DESCUENTOS ---
    Route::group(['middleware' => ['permission:manage_coupons,api']], function() {
        Route::get('/coupon/config', [CouponController::class, "config"]);
        Route::resource('/coupon', CouponController::class);
        Route::resource('/discount', DiscountController::class);
    });

    // --- ABOUT ---
    Route::group(['prefix' => 'pages', 'middleware' => ['permission:manage_about,api']], function() {
        /* 🔴 MEJORA: El upload_video antes del apiResource */
        Route::post('/about/upload_video/{id}', [AboutController::class, 'upload_video']);
        Route::apiResource('/about', AboutController::class);  
    });
});

// 3. ECOMMERCE
Route::group(["prefix" => "ecommerce"], function($router){
    Route::get("/home", [HomeController::class, "home"]);
    Route::get("/course-detail/{slug}", [HomeController::class, "course_detail"]);
    
    Route::resource('/cart', CartController::class)->middleware('auth:api');
    Route::post('/apply_coupon', [CartController::class, "apply_coupon"])->middleware('auth:api');
});