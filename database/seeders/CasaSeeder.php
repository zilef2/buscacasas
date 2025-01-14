<?php

namespace Database\Seeders;

use App\Models\Casa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CasaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Casa::create([
           'nombre' => 'mi casita',
           'barrio' => 'Simon bolivar',
           'precio' => 2000_000,
        ]);
        Casa::create([
           'nombre' => 'Casa Alvaro',
           'barrio' => 'Malibu',
           'precio' => 3000_000,
        ]);
    }
}
