<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    protected $table = 'profesores';

    protected $fillable = [
        'nombres',
        'apellidos',
        'numeroEmpleado',
        'horasClase',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
