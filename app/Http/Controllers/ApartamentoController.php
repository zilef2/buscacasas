<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\Apartamento;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ApartamentoController extends Controller
{
    public $thisAtributos,$FromController = 'Apartamento';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create Apartamento', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read Apartamento', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update Apartamento', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete Apartamento', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new Apartamento())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$Apartamentos = Apartamento::with('no_nada');
        $Apartamentos = Apartamento::Where('id','>',0);
        return $Apartamentos;

    }
    public function Filtros(&$Apartamentos,$request){
        if ($request->has('search')) {
            $Apartamentos = $Apartamentos->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $Apartamentos = $Apartamentos->orderBy($request->field, $request->order);
        }else
            $Apartamentos = $Apartamentos->orderBy('updated_at', 'DESC');
    }
    public function losSelect()
    {
        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Apartamentos '));
        $Apartamentos = $this->Mapear();
        $this->Filtros($Apartamentos,$request);
//        $losSelect = $this->losSelect();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $Apartamentos->paginate($perPage),
            'total'                 => $Apartamentos->count(),

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
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:Apartamentos');
        DB::beginTransaction();
        $Apartamento = Apartamento::create($request->all());
        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:Apartamentos EXITOSO', 'Apartamento id:' . $Apartamento->id . ' | ' . $Apartamento->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $Apartamento->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:Apartamentos');
        DB::beginTransaction();
        $Apartamento = Apartamento::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Apartamento->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:Apartamentos EXITOSO', 'Apartamento id:' . $Apartamento->id . ' | ' . $Apartamento->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Apartamento->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($Apartamentoid){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:Apartamentos');
        $Apartamento = Apartamento::find($Apartamentoid);
        $elnombre = $Apartamento->nombre;
        $Apartamento->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:Apartamentos', 'Apartamento id:' . $Apartamento->id . ' | ' . $Apartamento->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $Apartamento = Apartamento::whereIn('id', $request->id);
        $Apartamento->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.Apartamento')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
