<?php

namespace Database\Seeders;

use App\helpers\CargosModelos;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //<editor-fold desc="//-dont touch bro">
        $superadmin = Role::create(['name' => 'superadmin']);
        $admin = Role::create(['name' => 'admin']);

        $constantes = CargosModelos::CargosANDModelos();

        $crudCompleto = $constantes['crudCompleto'];
        $crudSemiCompleto = $constantes['crudSemiCompleto'];

        foreach ($constantes['Models'] as $model) {
            foreach ($crudCompleto as $crud) {
                $superadmin->givePermissionTo([$crud . ' ' . $model]);
                $admin->givePermissionTo([$crud . ' ' . $model]);
            }
        }
        $isSomeThing = array_merge(["isSuper", "isAdmin"], $constantes['isSome']);
        $superadmin->givePermissionTo($isSomeThing); //al superadmin, se le da rol de todos
        unset($isSomeThing[0]);
        $admin->givePermissionTo($isSomeThing);
        //</editor-fold>

        foreach ($constantes['nombresDeCargos'] as $value) {
            $$value = Role::create(['name' => $value]);
            $$value->givePermissionTo(['is' . $value]);
        }

        $core = $constantes['core'];
        $nombresDeCargos = $constantes['nombresDeCargos'];
        $primerCargo = (reset($nombresDeCargos));

        //! se asigna a "persona" todos los permisos
        foreach ($crudSemiCompleto as $index => $item) { //ejemplo buscacasas: item = update
            ${$primerCargo}->givePermissionTo([ //ejemplo buscacasas: persona
                $item . ' ' . $core //evaluate: $persona->givepermissionto = update casa
            ]);
        }
//        $elcargo = current(next($nombresDeCargos));
//        ${$elcargo}->givePermissionTo([
//            'read '.$core,  'create '.$core, 'update '.$core, 'download '.$core,
//            'read area',
//            'read user',
//        ]);

//        foreach ($constantes['fullCrud'] as $rol => $constante) {
//            foreach ($constante as $model) {
//                foreach ($crudSemiCompleto as $index => $item) {
//                    $$rol->givePermissionTo([$item.' '.$model]); //ejemplo: read user
//                }
//            }
//        }

        /**
         * /////-just for memory
         * @Comandos utiles: no  olvidar
         * // $role->revokePermissionTo($permission);
         * // $permission->removeRole($role);
         */
    }
}
