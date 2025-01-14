<?php

namespace App\helpers;

class CargosModelos {

    //JUST THIS PROJECT
    public static function CargosANDModelos(): array
    {
        $crudSemiCompleto = ['update', 'read', 'create','update2']; //-tochange plantilla
        $crudCompleto = array_merge(['delete'], $crudSemiCompleto);
        
        //otros cargos NO_ADMIN
        $nombresDeCargos = [ //-tochange plantilla
            'arrendatario',//1
            'persona',//2
        ];//recuerda userseeder, RoleSeeder
        $isSome = [];
        foreach ($nombresDeCargos as $key => $value) {
            $isSome[] = 'is' . $value;
        }
        $elcore = 'casa'; //tochange plantilla
        $Models = [
            'role',
            'permission',
            'user',
            'parametro',
            $elcore,
        ];
        
        return [
            'nombresDeCargos' => $nombresDeCargos,
            'Models' => $Models,
            'isSome' => $isSome,
            'core' => $elcore,
            'crudCompleto' => $crudCompleto,
            'crudSemiCompleto' => $crudSemiCompleto,
        ];
    }
}
?>
