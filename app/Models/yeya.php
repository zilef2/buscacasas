<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

    class yeya extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'descripcion', 'precio', 'estado', 'fecha_disponible'];

}
