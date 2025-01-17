<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class Casa extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'nombre',
        'precio',
        
        'tipo_inmueble',
        'ventaOarriendo',
        'barrio',
        
        'ciudad',
        'pais',
        
        'usado',
        'inmoviliaria',
        'tamano_m2',
        'contacto_celular',
        'contacto_celular2',
        'estrato',
        'estado',
        'antiguedad',
        'parqueaderos',
        'banos',
        'habitaciones',
        'acepta_mascotas',
        'administracion',
        'contrato_minimo',
        'cambio_precio1',
        'cambio_precio2',
        'pisos_interiores',
        'terraza',
        'comodidades',
        'fecha_contacto1',
        'conclusion_contacto1',
        'registro_fecha_contacto1',

        'fecha_contacto2',
        'conclusion_contacto2',
        'registro_fecha_contacto2',

        'fecha_primera_aceptacion_cliente',
        'fecha_conversacion_arrendatario',
    ];

}
