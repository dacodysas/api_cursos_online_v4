<?php

namespace App\Http\Controllers\Admin\Course;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\Course\Categorie;
use Owenoj\LaravelGetId3\GetId3;
use Vimeo\Laravel\Facades\Vimeo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Course\CourseGResource;
use App\Http\Resources\Course\CourseGCollection;

class CourseGController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $state = $request->state;
        $user = auth()->user(); 

        $query = Course::filterAdvance($search, $state);

        // 🚨 CAMBIO ROJO: Filtro para que el profesor solo vea lo suyo
        if ($user->hasRole('Profesor')) {
            $query->where('user_id', $user->id);
        }

        $courses = $query->orderBy("id", "desc")->get();

        return response()->json([
            "courses" => CourseGCollection::make($courses),
        ]);
    }

    public function config()
    {
        // 🚨 CAMBIO ROJO: Aseguramos que las categorías carguen para todos
        $categories = Categorie::where("categorie_id", NULL)->orderBy("id", "desc")->get();
        $subcategories = Categorie::where("categorie_id", "<>", NULL)->orderBy("id", "desc")->get();

        $user_auth = auth()->user();
        if ($user_auth->hasRole('Profesor')) {
            $instructores = User::where("id", $user_auth->id)->get();
        } else {
            $instructores = User::where("is_instructor", 1)->orderBy("id", "desc")->get();
        }

        return response()->json([
            "categories" => $categories,
            "subcategories" => $subcategories,
            "instructores" => $instructores->map(function ($user) {
                return [
                    "id" => $user->id,
                    "full_name" => $user->name . ' ' . $user->surname,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $is_exits = Course::where("title", $request->title)->first();
        if ($is_exits) {
            return response()->json(["message" => 403, "message_text" => "YA EXISTE UN CURSO CON ESTE TITULO"]);
        }
        if ($request->hasFile("portada")) {
            $path = Storage::putFile("courses", $request->file("portada"));
            $request->request->add(["imagen" => $path]);
        }

        $request->request->add(["slug" => Str::slug($request->title)]);
        $request->request->add(["requirements" => json_encode(explode(",", $request->requirements))]);
        $request->request->add(["who_is_it_for" => json_encode(explode(",", $request->who_is_it_for))]);
        
        // 🚨 CAMBIO ROJO: Forzar el ID del profesor al crear
        if(auth()->user()->hasRole('Profesor')){
            $request->request->add(["user_id" => auth()->user()->id]);
        }

        $course = Course::create($request->all());
        return response()->json(["message" => 200]);
    }

    public function upload_video(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        // 🚨 CAMBIO ROJO: Validar propiedad antes de subir a Vimeo
        if ($user->hasRole('Profesor') && $course->user_id !== $user->id) {
            return response()->json(["message" => 403, "message_text" => "NO TIENES PERMISO"], 403);
        }

        $track = new GetId3($request->file('video'));
        $time = $track->getPlaytimeSeconds();

        $response = Vimeo::upload($request->file('video'));
        $vimeo_id = explode("/", $response)[2];

        $course->update(["vimeo_id" => $vimeo_id, "time" => date("H:i:s", $time)]);

        return response()->json([
            "link_video" => "https://player.vimeo.com/video/" . $vimeo_id,
        ]);
    }

    public function show($id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        if ($user->hasRole('Profesor') && $course->user_id !== $user->id) {
            return response()->json(["message" => 403], 403);
        }

        return response()->json([
            "course" => CourseGResource::make($course),
        ]);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        if ($user->hasRole('Profesor') && $course->user_id !== $user->id) {
            return response()->json(["message" => 403], 403);
        }

        $is_exits = Course::where("id", "<>", $id)->where("title", $request->title)->first();
        if ($is_exits) {
            return response()->json(["message" => 403, "message_text" => "YA EXISTE UN CURSO CON ESTE TITULO"]);
        }

        if ($request->hasFile("portada")) {
            if ($course->imagen) {
                Storage::delete($course->imagen);
            }
            $path = Storage::putFile("courses", $request->file("portada"));
            $request->request->add(["imagen" => $path]);
        }

        $request->request->add(["slug" => Str::slug($request->title)]);
        $request->request->add(["requirements" => json_encode(explode(",", $request->requirements))]);
        $request->request->add(["who_is_it_for" => json_encode(explode(",", $request->who_is_it_for))]);
        
        $course->update($request->all());

        return response()->json(["course" => CourseGResource::make($course)]);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        if ($user->hasRole('Profesor') && $course->user_id !== $user->id) {
            return response()->json(["message" => 403], 403);
        }

        $course->delete();
        return response()->json(["message" => 200]);
    }
}