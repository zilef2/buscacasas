<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\generic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request as LaravelRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class geenericController extends Controller
{
    public $thisAtributos;
    public string $FromController = 'generic';
    public string $atributoBusqueda = 'adjetivo';

    //<editor-fold desc="Construc | mapea | filtro and dependencia">
    private int $segundos = 1;

    public function __construct(){
        $this->thisAtributos = (new generic())->getFillable();
    }

    public function Filtros($request)
    {
        $modelClass = "App\\Models\\" . $this->FromController;
        $generico = resolve($modelClass)->query();
        $generico = $generico->select([
            'id',
            'fecha_ini',
            'categoria',
        ]);

        if ($request->has('search')) {
            $generico->where(function ($query) use ($request) {
                $query->orWhere('fecha_realizacion', 'LIKE', "%" . $request->search . "%")
                    ->orWhere($this->atributoBusqueda, 'LIKE', "%" . $request->search . "%");
            });
        }

        return $generico;
    }

    public function MapearClaseP_P($request, $generic)
    {
        $generic->orderBy('updated_at', 'desc');

        if ($request->has(['field', 'order'])) {
            $field = $request->input('field');
            $order = $request->input('order');
            if ($order === 'asc') {
                $generic = $generic->orderBy($field);
            } else {
                $generic = $generic->orderByDesc($field);
            }
        }
        $myhelp = new Myhelp();

        $generic = $generic->get()->map(function ($registrosmedio) use ($myhelp) {
            $registrosmedio->aspecto = '';

            return $registrosmedio;
        })->filter();

        return $generic;
    }

    //</editor-fold>

    public function cacheRemember(LaravelRequest $request, $fromController): mixed {
        $cacheKey = 'mapear_clase_pp_' . md5(json_encode($request->all()));
        $fromController = Cache::remember($cacheKey, $this->segundos, function () use ($request, $fromController) {
            return $this->MapearClaseP_P($request, $fromController);
        });
        $this->segundos = 60 * 1;
        return $fromController; //segundos
    }
    
    public function index(LaravelRequest $request): Response
    {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' generic '));
        $fromController = $this->Filtros($request);
        $fromController = $this->cacheRemember($request, $fromController);
        
        $perPage = $request->has('perPage') ? $request->perPage : 8;
        $total = $fromController->count();
        $page = request('page', 1);
        $fromController = new LengthAwarePaginator(
            $fromController->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $fromController,
            'total' => $total,

            'breadcrumbs' => [['label' => __('app.label.' . $this->FromController), 'href' => route($this->FromController . '.index')]],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all(['search', 'field', 'order']),
            'perPage' => (int)$perPage,
            'numberPermissions' => $numberPermissions,
        ]);
    }


    public function create(){}
    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(LaravelRequest $request)
    {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:generics');
        DB::beginTransaction();
        $generic = generic::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:generics EXITOSO', 'generic id:' . $generic->id , false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $generic->nombre]));
    }

    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(LaravelRequest $request, $id)
    {
        Myhelp::EscribirEnLog($this, ' Begin UPDATE:generics');
        DB::beginTransaction();
        $generic = generic::findOrFail($id);
        $request->merge(['dependex_id' => $request->dependex['id']]);
        $generic->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:generics EXITOSO', 'generic id:' . $generic->id , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $generic->{$this->thisAtributos[1]}]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($genericid)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:generics');
        $generic = generic::find($genericid);
        $elnombre = $generic->nombre;
        $generic->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:generics', 'generic id:' . $generic->id  . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(LaravelRequest $request)
    {
        $generic = generic::whereIn('id', $request->id);
        $generic->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.generic')]));
    }
    //FIN : STORE - UPDATE - DELETE


}
