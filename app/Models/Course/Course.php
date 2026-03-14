<?php

namespace App\Models\Course;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Discount\DiscountCourse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "title",
        "subtitle",
        "slug",
        "imagen",
        "precio_usd",
        "precio_pen",
        "categorie_id",
        "sub_categorie_id",
        "user_id",
        "level",
        "idioma",
        "vimeo_id",
        "time",
        "description",
        "requirements",
        "who_is_it_for",
        "state",
    ];

    public function setCreatedAtAttribute($value)
    {
        date_default_timezone_set("America/Bogota");
        $this->attributes["created_at"] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value)
    {
        date_default_timezone_set("America/Bogota");
        $this->attributes["updated_at"] = Carbon::now();
    }

    public function instructor()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    //se hicieron cambios para poder recibir registros sin subcategirias
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id')->withDefault([
            'id' => 0,
            'name' => 'Categoría General'
        ]);
    }
    //se hicieron cambios para poder recibir registros sin subcategirias
    public function sub_categorie()
    {
        return $this->belongsTo(Categorie::class, 'sub_categorie_id')->withDefault([
            'id' => 0,
            'name' => 'Sin subcategoría'
        ]);
    }

    // public function sub_categorie()
    // {
    //     return $this->belongsTo(Categorie::class);
    // }

    public function sections()
    {
        return $this->hasMany(CourseSection::class);
    }

    public function discount_courses()
    {
        return $this->hasMany(DiscountCourse::class);
    }

    public function getDiscountCAttribute()
    {
        date_default_timezone_set("America/Bogota");
        $discount = null;
        foreach ($this->discount_courses as $key => $discount_course) {
           if($discount_course->discount->type_campaing == 1 &&  $discount_course->discount->state == 1){
            if(Carbon::now()->between($discount_course->discount->start_date,Carbon::parse($discount_course->discount->end_date)->addDays(1))){
                // EXISTE UNA CAMPAÑA DE DESCUENTO CON EL CURSO
                $discount = $discount_course->discount;
                break;
            }
           }
        }
        return $discount;
    }

    public function getDiscountCTAttribute()
    {
        date_default_timezone_set("America/Bogota");
        $discount = null;
        foreach ($this->categorie->discount_categories as $key => $discount_categorie) {
           if($discount_categorie->discount->type_campaing == 1 && $discount_categorie->discount->state == 1){
            if(Carbon::now()->between($discount_categorie->discount->start_date,Carbon::parse($discount_categorie->discount->end_date)->addDays(1))){
                // EXISTE UNA CAMPAÑA DE DESCUENTO CON EL CURSO
                $discount = $discount_categorie->discount;
                break;
            }
           }
        }
        return $discount;
    }

    public function getFilesCountAttribute()
    {
        $files_count = 0;

        foreach ($this->sections as $keyS => $section) {
            foreach ($section->clases as $keyC => $clase) {
                $files_count += $clase->files->count();
            }
        }

        return $files_count;
    }

    function AddTimes($horas)
{
    $total = 0;

    foreach($horas as $h) {
        // Si $h es null o no es string, saltamos para evitar errores   
        if (empty($h)) continue;

        $parts = explode(":", $h);

        // Asignamos a variables usando el operador ?? para evitar el "Undefined array key"
        $segundos = $parts[2] ?? 0; 
        $minutos  = $parts[1] ?? 0;
        $horas_p  = $parts[0] ?? 0;

        // USA LAS VARIABLES, NO EL ARRAY $PARTS DIRECTAMENTE
        $total += (int)$segundos + ((int)$minutos * 60) + ((int)$horas_p * 3600);
    }

    $hours = floor($total / 3600);
    $minutes = floor(($total / 60) % 60);

    return $hours . " hrs " . $minutes . " mins";
}


    public function getTimeCourseAttribute()
    {
       $times = [];
       foreach ($this->sections as $keyS => $section) {
        foreach ($section->clases as $keyC => $clase) {
            array_push($times,$clase->time);
        }
       }
       return $this->AddTimes($times);
    }

    function scopeFilterAdvance($query,$search,$state)
    {
        if($search){
            $query->where("title","like","%".$search."%");
        }
        if($state){
            $query->where("state",$state);
        }
        
        return $query;
    }
}
