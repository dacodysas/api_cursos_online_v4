<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('about', function (Blueprint $table) {
            $table->id();
            
            // Textos principales
            $table->string('title');
            $table->string('subtitle')->nullable();
            
            // Imágenes
            $table->string('hero_image_url')->nullable();
            $table->string('side_image_url')->nullable();
            
            // Historia y Sección About
            $table->text('history')->nullable();
            $table->string('about_title')->nullable();
            $table->text('about_text')->nullable();
            
            // Misión y Visión (Separados en Título y Texto)
            $table->string('mission_title')->nullable();
            $table->text('mission_text')->nullable();
            $table->string('vision_title')->nullable();
            $table->text('vision_text')->nullable();
            
            // Video y Metadata
            $table->string('vimeo_id')->nullable();
            $table->string('time')->nullable();
            
            // Redes Sociales (Para usar con el cast 'array')
            $table->json('social_media')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
