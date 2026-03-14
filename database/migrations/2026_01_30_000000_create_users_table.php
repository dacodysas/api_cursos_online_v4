<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname', 250)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Campos de Instructor
            $table->tinyInteger('is_instructor')->default(0)->comment('0: no instructor, 1: instructor');
            $table->string('profesion', 250)->nullable();
            $table->text('description')->nullable();
            $table->string('avatar', 250)->nullable();

            /**
             * IMPORTANTE: He quitado el ->constrained('roles') 
             * ¿Por qué? Porque la tabla de Spatie se crea después. 
             * Mantendremos la columna para tu lógica manual sin forzar la llave foránea física aquí.
             */
            $table->unsignedBigInteger('role_id')->nullable();

            $table->tinyInteger('state')->default(1)->comment('1: Activo, 2: Desactivo');
            $table->tinyInteger('type_user')->default(2)->comment('1: Admin, 2: Cliente, 3: Profesor');

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}