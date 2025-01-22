<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;


/*
 * php artisan make:command MakeModelExperimental
 */

class CopyUserPages extends Command
{

    protected function generateAttributes($modelName): array
    {
        // Regla básica: nombres genéricos
        return [
            'nombre' => 'string',
            'descripcion' => 'text',
            'precio' => 'decimal',
            'estado' => 'boolean',
            'fecha_disponible' => 'date',
        ];
    }

    protected $signature = 'copy:u';
    protected $description = 'Copia de la entidad generica';

    const MSJ_GENERANDO = 'La genericacion del componente: ';
    const MSJ_EXITO = ' fue realizada con exito ';
    const MSJ_FALLO = ' Fallo';

    public function handle(): void
    {
        $modelName = $this->ask('¿Cuál es el nombre del modelo?');
        if (!$modelName || $modelName == '') {
            $this->info('Sin modelo');
            return;
        }

        $depende = $this->ask('¿depende de otro modelo?');
        $plantillaActual = 'generic';


        $this->warn("Empezando make:model");
        Artisan::call('make:model', ['name' => $modelName, '--all' => true]);
        $this->warn("Empezando copies");
        Artisan::call('copy:f');
        $this->warn("Ahora Lang");
        Artisan::call('lang:u ' . $modelName);


        $RealizoVueConExito = $this->MakeVuePages($plantillaActual, $modelName);
        $mensaje = $RealizoVueConExito ? self::MSJ_GENERANDO . ' Vuejs' . self::MSJ_EXITO
            : self::MSJ_GENERANDO . ' Vuejs' . self::MSJ_FALLO;
        $this->info($mensaje);


        $RealizoControllerConExito = $this->MakeControllerPages($plantillaActual, $modelName);
        $mensaje = $RealizoControllerConExito ? self::MSJ_GENERANDO . 'el controlador' . self::MSJ_EXITO
            : self::MSJ_GENERANDO . ' controlador ' . self::MSJ_FALLO;
        $this->info($mensaje);


        if ($RealizoControllerConExito || $RealizoVueConExito)
            $this->replaceWordInFiles($plantillaActual,
                [
                    'vue' => $RealizoVueConExito,
                    'controller' => $RealizoControllerConExito
                ]
                , $modelName, $depende);


        $this->DoWebphp($modelName);
        $this->DoAppLenguaje($modelName);
        $this->DoSideBar($modelName);
        $this->DoFillable($modelName);
        $this->updateMigration($modelName);

        $this->info("Fin de la operacion. Se limpiará cache\n\n");
        $this->info('Corriendo el comando optimize: ');
        $this->info(Artisan::call('optimize'));
        $this->info('Corriendo el comando optimize:clear ');
        $this->info(Artisan::call('optimize:clear'));
    }


    private function MakeControllerPages($plantillaActual, $modelName): bool
    {
        $folderMayus = ucfirst($modelName);
        $sourcePath = base_path('app/Http/Controllers/' . $plantillaActual . 'sController.php');
        $destinationPath = base_path("app/Http/Controllers/" . $folderMayus . "sController.php");

        if (File::exists($destinationPath)) {
            $this->warn("La carpeta de destino '{$destinationPath}' ya existe.");
            return false;
        }
        File::copyDirectory($sourcePath, $destinationPath);
        $this->info("- " . $sourcePath);
        $this->info("- " . $destinationPath);

        return true;
    }

    private function MakeVuePages($plantillaActual, $modelName): bool
    {
        $sourcePath = base_path('resources/js/Pages/' . $plantillaActual);
        $destinationPath = base_path("resources/js/Pages/{$modelName}");

        if (File::exists($destinationPath)) {
            $this->warn("La carpeta de destino '{$modelName}' ya existe.");
            return false;
        }
        File::copyDirectory($sourcePath, $destinationPath);
        return true;
    }

    private function replaceWordInFiles($oldWord, $permiteRemplazo, $modelName, $depende): void
    {
        $folderMayus = ucfirst($modelName);
        $files = File::allFiles(base_path("resources/js/Pages/{$modelName}"));
        $controller = base_path("app/Http/Controllers/{$folderMayus}" . 'Controller.php');

        $depende = $depende == '' || $depende == null ? 'no_nada' : $depende;

        if ($permiteRemplazo['vue']) {
            foreach ($files as $key => $file) {

                $content = file_get_contents($file);
                $content = str_replace(array($oldWord, ucfirst($oldWord), 'geeneric'),//ojo aqui, es estatico
                    [$modelName, $folderMayus, $folderMayus],
                    $content
                );
                file_put_contents($file, $content);
            }
        }

        //reemplazo de controlador
        if ($permiteRemplazo['controller']) {
            $sourcePath = base_path('app/Http/Controllers/' . ucfirst($oldWord) . 'Controller.php');
            $content = file_get_contents($sourcePath);
            $content = str_replace(array($oldWord, 'dependex', 'deependex', 'geeneric'),//ojo aqui, es estatico
                array($modelName, $depende, ucfirst($depende), ucfirst($modelName)),
                $content
            );
            file_put_contents($controller, $content);
        }
    }

    protected function DoFillable($modelName): void
    {
        $attributes = $this->generateAttributes($modelName);

        // Generar el fillable
        $fillable = array_keys($attributes);
        $fillableString = implode("', '", $fillable);

        // Ruta del modelo
        $modelPath = app_path("Models/{$modelName}.php");

        // Verificar si el modelo existe
        if (!File::exists($modelPath)) {
            $this->error("El modelo {$modelName} no existe.");
            return;
        }

        // Leer el contenido del modelo
        $modelContent = File::get($modelPath);

        // Añadir el fillable y SoftDeletes
        $modelContent = preg_replace('/protected \$fillable = \[.*?\];/s', "protected \$fillable = ['{$fillableString}'];", $modelContent);
        if (!str_contains($modelContent, 'use SoftDeletes;')) {
            $modelContent = preg_replace('/class ' . $modelName . ' extends/', "use Illuminate\Database\Eloquent\SoftDeletes;\n\n    class {$modelName} extends", $modelContent);
        }

        // Guardar el contenido modificado
        File::put($modelPath, $modelContent);

        $this->info("El fillable y SoftDeletes han sido añadidos al modelo {$modelName}.");
    }

    private function DoAppLenguaje($resource): void
    {
        $directory = 'lang/es/app.php';
        $files = glob($directory);

        $insertable = "'$resource' => '$resource',\n\t\t//aquipues";
        $pattern = '/\/\/aquipues/';

        foreach ($files as $file) {
            $content = file_get_contents($file);
            if (strpos($content, $pattern) === false) {
                $content2 = preg_replace($pattern, $insertable, $content);
//                $content2 = preg_replace($pattern, "$0$insertable", $content);
                file_put_contents($file, $content2);
                if ($content == $content2)
                    $this->info("Language Actualizado: $file\n");
                else
                    $this->info("Language sin cambios: $file\n");
            } else {
                $this->error("No existe aquipues en: $file\n");
            }
        }

    }


    private function DoWebphp($resource)
    {
        $directory = 'routes';
        $files = glob($directory . '/*.php');

        $insertable = "Route::resource(\"/$resource\", \\App\\Http\\Controllers\\" . ucfirst($resource) . "Controller::class);\n\t//aquipues";

        $pattern = '/\/\/aquipues/';

        foreach ($files as $file) {
            $content = file_get_contents($file);

            if (strpos($content, $pattern) === false) {
                $content2 = preg_replace($pattern, $insertable, $content);
//                $content2 = preg_replace($pattern, "$0$insertable", $content);
                file_put_contents($file, $content2);
                if ($content == $content2)
                    $this->info("Routes Actualizado: $file\n");
                else
                    $this->info("Routes sin cambios: $file\n");
            } else {
                $this->error("No existe aquipues en: $file\n");
            }
        }

        return true;
    }

    private function DoSideBar($resource)
    {
        $directory = 'resources/js/Components/SideBarMenu.vue';
        $files = glob($directory);

        $insertable = "'" . $resource . "',\n\t//aquipuesSide";
        $pattern = '/\/\/aquipuesSide/';

        foreach ($files as $file) {
            $content = file_get_contents($file);

            if (strpos($content, $pattern) === false) {
                $content2 = preg_replace($pattern, $insertable, $content);
                //$content2 = preg_replace($pattern, "$0$insertable", $content);
                file_put_contents($file, $content2);
                if ($content != $content2)
                    $this->info("SideBarMenu.vue Actualizado: $file\n");
                else
                    $this->info("SideBarMenu.vue sin cambios: $file\n"); //todo: revisar si ya existe
            } else {
                $this->error("No existe aquipues en: $file\n");
            }
        }

        return true;
    }

    protected function updateMigration($modelName): void
    {
        $atributos = $this->generateAttributes($modelName);
        $migrationFile = collect(glob(database_path('migrations/*.php')))
            ->first(fn($file) => str_contains($file, 'create_' . Str::snake(Str::plural($modelName)) . '_table'));

        if (!$migrationFile) {
            $this->error("No se encontró la migración para $modelName");
            return;
        }

        $columns = collect($atributos)->map(function ($type, $name) {
            return "\$table->$type('$name');";
        })->implode("\n            ");

        $content = file_get_contents($migrationFile);
        $content = preg_replace('/Schema::create\(.*?\{/', "$0\n            $columns", $content);
        file_put_contents($migrationFile, $content);

        $this->info("Migración actualizada para $modelName");
    }

}
