<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserGResource extends JsonResource
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
        "name" => $this->resource->name,
        "surname" => $this->resource->surname,
        "email" => $this->resource->email,
        
        // ✅ Extraemos el nombre del rol desde la relación de Spatie
        "role" => $this->resource->roles->first() ? [
            "id" => $this->resource->roles->first()->id,
            "name" => $this->resource->roles->first()->name
        ] : null,
        
        // Esto es lo que permitirá que Angular sepa qué permisos "extra" tiene cada profesor
        "permissions" => $this->resource->permissions->pluck('name'),

        "state" => $this->resource->state,
        'is_instructor' => $this->resource->is_instructor,
        'profesion' => $this->resource->profesion,
        'description' => $this->resource->description,
        
        // ✅ Formateamos la fecha para que Angular la pinte sin problemas
        "created_at" => $this->resource->created_at ? $this->resource->created_at->format("Y-m-d H:i:s") : null,
        
        "avatar" => $this->resource->avatar ? env("APP_URL")."storage/".$this->resource->avatar : null,
    ];
}
}
