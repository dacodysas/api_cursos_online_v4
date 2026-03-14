<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;
    protected $guard_name = 'api';

    const ADMIN = 1;
    const CLIENTE = 2;
    const PROFESOR = 3;

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'avatar',
        'role_id',
        'state',        
        'type_user',    
        'is_instructor',
        'profesion',
        'description',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * OBLIGATORIO PARA JWT: Retorna el identificador del usuario (ID)
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * OBLIGATORIO PARA JWT: Retorna datos personalizados para el Token
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->type_user,
            'role_name' => $this->getRoleNames()->first(), 
        ];
    }
    public function scopeFilterAdvance($query, $search, $state)
    {
        if($search){
            $query->where(function($q) use($search) {
                $q->where("name", "like", "%".$search."%")
                  ->orWhere("surname", "like", "%".$search."%")
                  ->orWhere("email", "like", "%".$search."%");
            });
        }
        if($state){
            $query->where("state", $state);
        }
        return $query;
    }
}