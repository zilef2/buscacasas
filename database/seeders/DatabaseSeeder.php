<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

//use Google\Service\SemanticTile\Area;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(){
        $this->call([
            ParametroSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,

            UserSeeder::class,
            CasaSeeder::class,
        ]);
         User::factory(10)->create();

         
//        $usuarios = DB::connection('secundaria')->table('usuarios')->get();
    }
}
