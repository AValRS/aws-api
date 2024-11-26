<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'alumnos';

    protected $fillable = [
        'nombres',
        'apellidos',
        'matricula',
        'promedio',
        'password',
        'fotoPerfilUrl',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'password'
    ];
}
