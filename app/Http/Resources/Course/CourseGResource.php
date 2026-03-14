<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseGResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->resource->id,
            "title" => $this->resource->title,
            "subtitle" => $this->resource->subtitle,
            "slug" => $this->resource->slug,
            "imagen" => env("APP_URL")."storage/".$this->resource->imagen,
            
            // --- CAMBIO 1: Validación de Categoría ---
            // Protegemos por si algún curso se quedó sin categoría asignada
            "categorie_id" => $this->resource->categorie_id,
            "categorie" => $this->resource->categorie ? [
                "id" => $this->resource->categorie->id,
                "name" => $this->resource->categorie->name
            ] : null,

            // --- CAMBIO 2: Validación de Subcategoría ---
            // Ya lo tenías bien, lo mantenemos protegido
            "sub_categorie_id" => $this->resource->sub_categorie_id,
            "sub_categorie" => $this->resource->sub_categorie ? [
                "id" => $this->resource->sub_categorie->id,
                "name" => $this->resource->sub_categorie->name,
            ] : null, 
            
            "precio_usd" => $this->resource->precio_usd,
            "precio_pen" => $this->resource->precio_pen,
            "user_id" => $this->resource->user_id,

            // --- CAMBIO 3: Validación de Instructor (EL ERROR ESTABA AQUÍ) ---
            // Si el instructor es null, todo el bloque de 'user' será null.
            // Esto evita que PHP intente leer ->name o ->email en algo que no existe.
            "user" => $this->resource->instructor ? [
                "id" => $this->resource->instructor->id,
                "full_name" => $this->resource->instructor->name.' '.$this->resource->instructor->surname,
                "email" => $this->resource->instructor->email,
            ] : null,

            "level" => $this->resource->level,
            "idioma" => $this->resource->idioma,
            "vimeo_id" => $this->resource->vimeo_id,
            "time" => $this->resource->time,
            "description" => $this->resource->description,
            "requirements" => json_decode($this->resource->requirements),
            "who_is_it_for" => json_decode($this->resource->who_is_it_for),
            "state" => $this->resource->state,
        ];
    }
}