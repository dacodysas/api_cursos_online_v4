<?php

namespace App\Models\Pages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     * @var string
     */
    protected $table = 'about';

    /**
     * Atributos que se pueden asignar masivamente.
     * @var array
     */
    protected $fillable = [
        
        'title',                   // Título principal de la página (H1)
        'subtitle',                // Lema o descripción corta bajo el título
        'hero_image_url',          
        'side_image_url',         
        'history',
        'about_title',             
        'about_text',
        'mission_title',                 
        'mission_text',
        'vision_title',
        'vision_text',                 
        "vimeo_id",
        "time",  
        
        'social_media',             // Objeto JSON con enlaces (LinkedIn, Instagram, etc.)                 
        
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * Esto permite que Angular reciba objetos JSON reales en lugar de strings.
     * @var array
     */
    protected $casts = [        
        'social_media' => 'array',        
    ];
}
