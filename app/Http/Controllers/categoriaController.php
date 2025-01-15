<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Caracteristica;
use App\Models\Categoria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class categoriaController extends Controller
{

    function __construct() {
        $this->middleware('permission:ver-categoria|crear-categoria|editar-categoria|eliminar-categoria',['only' => ['index']]);
        $this->middleware('permission:crear-categoria',['only' => ['create','store']]);
        $this->middleware('permission:editar-categoria',['only' => ['edit','update']]);
        $this->middleware('permission:eliminar-categoria',['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $categorias = Categoria::with('caracteristica')->get();
        $categorias = Categoria::with('caracteristica')->latest()->get();

        return view('categoria.index',['categorias' => $categorias]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categoria.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(StoreCategoriaRequest $request)
    {
        //  dd($request);

        try{
            DB::beginTransaction();
            $caracteristica = Caracteristica::create($request->validated());//Crear registro
            $caracteristica->categoria()->create([ //Guardar llave foranea
                'caracteristica_id' => $caracteristica->id
            ]);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }

        return redirect()->route('categorias.index')->with('success','Categoria registrada');

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
    public function edit(Categoria $categoria)
    {
        // dd($categoria);
        return view('categoria.edit',['categoria'=> $categoria]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        Caracteristica::where('id',$categoria->caracteristica->id)
        ->update($request->validated());

        return redirect()->route('categorias.index')->with('success', 'Categoría editada');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        // dd($id);
        $categoria = Categoria::find($id);
        $estado = $categoria->caracteristica->estado == 1 ? 0 : 1;
        $message = $estado == 1 ? 'Categoría restaurada': 'Categoría eliminada';
        Caracteristica::where('id', $categoria->caracteristica->id)
        ->update([
            'estado'=> $estado
        ]);
        return redirect()->route('categorias.index')->with('success',$message);
    }
}
