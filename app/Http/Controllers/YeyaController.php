<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\yeya;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request as LaravelRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class YeyaController extends Controller
{
    public $thisAtributos;
    public string $FromController = 'yeya';
    public string $atributoBusqueda = 'adjetivo';

    //<editor-fold desc="Construc | mapea | filtro and dependencia">
    private int $segundos = 1;

    public function __construct(){
        $this->thisAtributos = (new yeya())->getFillable();
    }

    public function Filtros($request)
    {
        $modelClass = "App\\Models\\" . $this->FromController;
        $yeyao = resolve($modelClass)->query();
        $yeyao = $yeyao->select([
            'id',
            'fecha_ini',
            'categoria',
        ]);

        if ($request->has('search')) {
            $yeyao->where(function ($query) use ($request) {
                $query->orWhere('fecha_realizacion', 'LIKE', "%" . $request->search . "%")
                    ->orWhere($this->atributoBusqueda, 'LIKE', "%" . $request->search . "%");
            });
        }

        return $yeyao;
    }

    public function MapearClaseP_P($request, $yeya)
    {
        $yeya->orderBy('updated_at', 'desc');

        if ($request->has(['field', 'order'])) {
            $field = $request->input('field');
            $order = $request->input('order');
            if ($order === 'asc') {
                $yeya = $yeya->orderBy($field);
            } else {
                $yeya = $yeya->orderByDesc($field);
            }
        }
        $myhelp = new Myhelp();

        $yeya = $yeya->get()->map(function ($registrosmedio) use ($myhelp) {
            $registrosmedio->aspecto = '';

            return $registrosmedio;
        })->filter();

        return $yeya;
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
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' yeya '));
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
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:yeyas');
        DB::beginTransaction();
        $yeya = yeya::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:yeyas EXITOSO', 'yeya id:' . $yeya->id , false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $yeya->nombre]));
    }

    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(LaravelRequest $request, $id)
    {
        Myhelp::EscribirEnLog($this, ' Begin UPDATE:yeyas');
        DB::beginTransaction();
        $yeya = yeya::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $yeya->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:yeyas EXITOSO', 'yeya id:' . $yeya->id , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $yeya->{$this->thisAtributos[1]}]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($yeyaid)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:yeyas');
        $yeya = yeya::find($yeyaid);
        $elnombre = $yeya->nombre;
        $yeya->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:yeyas', 'yeya id:' . $yeya->id  . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(LaravelRequest $request)
    {
        $yeya = yeya::whereIn('id', $request->id);
        $yeya->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.yeya')]));
    }
    //FIN : STORE - UPDATE - DELETE


}
