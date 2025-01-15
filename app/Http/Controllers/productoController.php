<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacione;
use App\Models\Producto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;

class productoController extends Controller
{
    function __construct() {
        $this->middleware('permission:ver-producto|crear-producto|editar-producto|eliminar-producto',['only' => ['index']]);
        $this->middleware('permission:crear-producto',['only' => ['create','store']]);
        $this->middleware('permission:editar-producto',['only' => ['edit','update']]);
        $this->middleware('permission:eliminar-producto',['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $productos = Producto::with(['categorias.caracteristica','marca.caracteristica','presentacione.caracteristica'])
      ->latest()->get();
    //   dd($productos);
        return view('producto.index',compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $marcas = Marca::join('caracteristicas as c', 'marcas.caracteristica_id','=','c.id')
        ->select('marcas.id as id','c.nombre as nombre')
        ->where('c.estado',1)
        ->get();
        // dd($marcas);

        $presentaciones = Presentacione::join('caracteristicas as c','presentaciones.caracteristica_id','=','c.id')
        ->select('presentaciones.id as id','c.nombre as nombre')
        ->where('c.estado',1)
        ->get();

        $categorias = Categoria::join('caracteristicas as c', 'categorias.caracteristica_id','=','c.id')
        ->select('categorias.id as id','c.nombre as nombre')
        ->where('c.estado',1)
        ->get();
        // dd($categorias);

        return view('producto.create',compact('marcas','presentaciones','categorias'));

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        try{
            DB::beginTransaction();
            //Tabla producto
            $producto = new Producto();
            if($request->hasFile('img_path')){
                $name = $producto->handleUploadImage($request->file('img_path'));
            }else{
                $name = null;
            }

            $producto->fill([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'img_path' => $name,
                'marca_id' => $request->marca_id,
                'presentacione_id' => $request->presentacione_id
            ]);
            $producto->save();

            //Llenar la Tabla categoría producto
            $categorias = $request->get('categorias');//Guardar el arreglo categorias
            $producto->categorias()->attach($categorias);//Attach nos ayuda a llenar tablas pivote


            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }

        return redirect()->route('productos.index')->with('success','Producto registrado');

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
    public function edit(Producto $producto)
    {
        $marcas = Marca::join('caracteristicas as c', 'marcas.caracteristica_id','=','c.id')
        ->select('marcas.id as id','c.nombre as nombre')
        ->where('c.estado',1)
        ->get();
        // dd($marcas);

        $presentaciones = Presentacione::join('caracteristicas as c','presentaciones.caracteristica_id','=','c.id')
        ->select('presentaciones.id as id','c.nombre as nombre')
        ->where('c.estado',1)
        ->get();

        $categorias = Categoria::join('caracteristicas as c', 'categorias.caracteristica_id','=','c.id')
        ->select('categorias.id as id','c.nombre as nombre')
        ->where('c.estado',1)
        ->get();
        // dd($categorias);
        return view('producto.edit',compact('producto','marcas','presentaciones','categorias'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductoRequest $request,Producto $producto)
    {
        // dd('Hola');

        try {
            DB::beginTransaction();

             //Tabla producto
            
             if($request->hasFile('img_path')){
                 $name = $producto->handleUploadImage($request->file('img_path'));
                 //Eliminar si existe una imagen
                 if(Storage::disk('public')->exists('productos/'.$producto->img_path)){
                    Storage::disk('public')->delete('productos/'.$producto->img_path);
                 }
             }else{
                 $name = $producto->img_path;
             }
 
             $producto->fill([
                 'codigo' => $request->codigo,
                 'nombre' => $request->nombre,
                 'descripcion' => $request->descripcion,
                 'fecha_vencimiento' => $request->fecha_vencimiento,
                 'img_path' => $name,
                 'marca_id' => $request->marca_id,
                 'presentacione_id' => $request->presentacione_id
             ]);
             $producto->save();
 
             //Llenar la Tabla categoría producto
             $categorias = $request->get('categorias');//Guardar el arreglo categorias
             $producto->categorias()->sync($categorias);//Sync elimina todas las categorias del producto y luego añade
              //las nuevas categorias
 
           
           

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }

        return redirect()->route('productos.index')->with('success','Producto editado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $producto = Producto::find($id);
        $estado = $producto->estado == 1 ? 0 : 1;
        $message = $estado == 1 ? 'Producto restaurado': 'Producto eliminado';
        Producto::where('id', $producto->id)
        ->update([
            'estado'=> $estado
        ]);
        return redirect()->route('productos.index')->with('success',$message);
    }
}
