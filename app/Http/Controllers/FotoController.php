<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\Foto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FotoController extends Controller
{
    public $thisAtributos,$FromController = 'Foto';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create Foto', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read Foto', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update Foto', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete Foto', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new Foto())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$Fotos = Foto::with('no_nada');
        $Fotos = Foto::Where('id','>',0);
        return $Fotos;

    }
    public function Filtros(&$Fotos,$request){
        if ($request->has('search')) {
            $Fotos = $Fotos->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $Fotos = $Fotos->orderBy($request->field, $request->order);
        }else
            $Fotos = $Fotos->orderBy('updated_at', 'DESC');
    }
    public function losSelect()
    {
        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Fotos '));
        $Fotos = $this->Mapear();
        $this->Filtros($Fotos,$request);
//        $losSelect = $this->losSelect();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $Fotos->paginate($perPage),
            'total'                 => $Fotos->count(),

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
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:Fotos');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Foto = Foto::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:Fotos EXITOSO', 'Foto id:' . $Foto->id . ' | ' . $Foto->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $Foto->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:Fotos');
        DB::beginTransaction();
        $Foto = Foto::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Foto->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:Fotos EXITOSO', 'Foto id:' . $Foto->id . ' | ' . $Foto->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Foto->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($Fotoid){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:Fotos');
        $Foto = Foto::find($Fotoid);
        $elnombre = $Foto->nombre;
        $Foto->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:Fotos', 'Foto id:' . $Foto->id . ' | ' . $Foto->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $Foto = Foto::whereIn('id', $request->id);
        $Foto->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.Foto')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
