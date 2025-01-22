<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\Casa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CasaController extends Controller
{
    public $thisAtributos,$FromController = 'Casa';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create Casa', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read Casa', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update Casa', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete Casa', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new Casa())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$Casas = Casa::with('no_nada');
        $Casas = Casa::Where('id','>',0);
        return $Casas;

    }
    public function Filtros(&$Casas,$request){
        if ($request->has('search')) {
            $Casas = $Casas->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $Casas = $Casas->orderBy($request->field, $request->order);
        }else
            $Casas = $Casas->orderBy('updated_at', 'DESC');
    }
    public function losSelect()
    {
        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Casas '));
        $Casas = $this->Mapear();
        $this->Filtros($Casas,$request);
//        $losSelect = $this->losSelect();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $Casas->paginate($perPage),
            'total'                 => $Casas->count(),

            'breadcrumbs'           => [['label' => __('app.label.'.$this->FromController), 'href' => route($this->FromController.'.index')]],
            'title'                 => __('app.label.'.$this->FromController),
            'filters'               => $request->all(['search', 'field', 'order']),
            'perPage'               => (int) $perPage,
            'numberPermissions'     => $numberPermissions,
//            'losSelect'             => $losSelect,
        ]);
    }

    public function create(){}

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(Request $request){
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:Casas');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Casa = Casa::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:Casas EXITOSO', 'Casa id:' . $Casa->id . ' | ' . $Casa->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $Casa->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:Casas');
        DB::beginTransaction();
        $Casa = Casa::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Casa->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:Casas EXITOSO', 'Casa id:' . $Casa->id . ' | ' . $Casa->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Casa->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($Casaid){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:Casas');
        $Casa = Casa::find($Casaid);
        $elnombre = $Casa->nombre;
        $Casa->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:Casas', 'Casa id:' . $Casa->id . ' | ' . $Casa->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $Casa = Casa::whereIn('id', $request->id);
        $Casa->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.Casa')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
