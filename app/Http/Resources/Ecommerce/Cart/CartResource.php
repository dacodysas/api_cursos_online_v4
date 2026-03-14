<?php

namespace App\Http\Resources\Ecommerce\Cart;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            "user_id" => $this->resource->user_id,
            "course_id" => $this->resource->course_id,
            "course" => [
                "title" => $this->resource->course->title,
                "imagen" => env("APP_URL")."storage/".$this->resource->course->imagen,
                "subtitle" => $this->resource->course->subtitle,
                "slug" => $this->resource->course->slug,
            ],
            "type_discount" => $this->resource->type_discount,
            "discount" => $this->resource->discount,
            "type_campaing" => $this->resource->type_campaing,
            "code_cupon" => $this->resource->code_cupon,
            "code_discount" => $this->resource->code_discount,
            "precio_unitario" => $this->resource->precio_unitario,
            "total" => $this->resource->total,
        ];
    }
}
