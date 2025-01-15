<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class roleController extends Controller
{
    function __construct() {
        $this->middleware('permission:ver-role|crear-role|mostrar-role|eliminar-role',['only' => ['index']]);
        $this->middleware('permission:crear-role',['only' => ['create','store']]);
        $this->middleware('permission:editar-role',['only' => ['show','update']]);
        $this->middleware('permission:eliminar-role',['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return view('role.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permisos= Permission::all();
        return view('role.create',compact('permisos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required'
        ]);

        // dd($request);

        try {
            DB::beginTransaction();
                //Crear rol
                $rol = Role::create(['name' => $request->name]);

                //Asignar permisos
                $rol->syncPermissions(array_map('intval',$request->permission));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('roles.index')->with('success','Vuelve a intentar el registro');
        }

        

        return redirect()->route('roles.index')->with('success','rol registrado');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $permisos = Permission::all();
        return view('role.edit', compact('role','permisos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'=> 'required|unique:roles,name,'.$role->id,
            'permission' => 'required'
        ]);

        try{
            DB::beginTransaction();

            //Actualizar rol
            Role::Where('id',$role->id)
            ->update([
                'name' => $request->name
            ]);

            ////Actualizar permisos
            $role->syncPermissions(array_map('intval',$request->permission));

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }

        return redirect()->route('roles.index')->with('success','rol editado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Role::where('id',$id)
        ->delete();

        return redirect()->route('roles.index')->with('success','rol eliminado');
    }
}
