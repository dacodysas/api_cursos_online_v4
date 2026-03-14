<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Pages\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Owenoj\LaravelGetId3\GetId3;
use Vimeo\Laravel\Facades\Vimeo;


class AboutController extends Controller
{
    public function index()
    {
        $about = About::first();
        // CAMBIO AQUÍ: Envolver en ['about' => $about]
        return response()->json([
            "about" => $about
        ]);     


        // $about = About::first();
        // return response()->json($about);
    }

    public function store(Request $request)
    {
        // 1. Convertir los strings de FormData a Arrays de PHP antes de validar
        if ($request->has('core_values')) {
            $request->merge([
                'core_values' => json_decode($request->core_values, true)
            ]);
        }

        if ($request->has('social_media')) {
            $request->merge([
                'social_media' => json_decode($request->social_media, true)
            ]);
        }

        // 2. Validación (Ahora core_values y social_media pasarán como array)
        $request->validate([
            'title'         => 'required|string|max:255',
            'history'       => 'required|string',
            'about_title'   => 'nullable|string|max:255',
            'about_text'    => 'nullable|string',
            'mission_title' => 'nullable|string|max:255',
            'mission_text'  => 'nullable|string',
            'vision_title'  => 'nullable|string|max:255',
            'vision_text'   => 'nullable|string',
            'social_media'  => 'nullable|array',
            'hero_image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'side_image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        

        $data = $request->all();

        // 3. Obtener el registro actual para no perder las imágenes si no se suben nuevas
        $about = About::find(1);

        // 4. Gestión de Imagen Hero
        if ($request->hasFile('hero_image')) {
            if ($about && $about->hero_image_url) {
                Storage::disk('public')->delete($about->hero_image_url);
            }
            $data['hero_image_url'] = $request->file('hero_image')->store('abouts', 'public');
        }

        // 5. Gestión de Imagen Lateral
        if ($request->hasFile('side_image')) {
            if ($about && $about->side_image_url) {
                Storage::disk('public')->delete($about->side_image_url);
            }
            $data['side_image_url'] = $request->file('side_image')->store('abouts', 'public');
        }

        // 6. Guardar o Actualizar
        $about = About::updateOrCreate(
            ['id' => 1],
            $data
        );

        return response()->json([
            "message" => "Información actualizada con éxito",
            "about" => $about
        ], 200);
    }
    
    public function upload_video(Request $request,$id)
    {
        $time = 0;
        
        //instantiate class with file
        $track = new GetId3($request->file('video'));

        //get playtime
        $time = $track->getPlaytimeSeconds();

        $response = Vimeo::upload($request->file('video'));


        $course = About::findOrFail($id);
        error_log(json_encode(explode("/",$response)));
        $vimeo_id = explode("/",$response)[2];

        $course->update(["vimeo_id" => $vimeo_id,"time" => date("H:i:s",$time)]);

        return response()->json([
            "link_video" => "https://player.vimeo.com/video/".$vimeo_id,
        ]);
    }

    public function show($id)
    {
         return response()->json([
            "about" => $about
        ]);
        // $about = About::findOrFail($id);
        // return response()->json($about);
    }

    
}
