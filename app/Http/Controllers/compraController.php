<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Models\Compra;
use App\Models\Comprobante;
use App\Models\Producto;
use App\Models\Proveedore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class compraController extends Controller
{
    function __construct() {
        $this->middleware('permission:ver-compra|crear-compra|mostrar-compra|eliminar-compra',['only' => ['index']]);
        $this->middleware('permission:crear-compra',['only' => ['create','store']]);
        $this->middleware('permission:mostrar-compra',['only' => ['show','update']]);
        $this->middleware('permission:eliminar-compra',['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $compras = Compra::with('comprobante', 'proveedore.persona')
        ->where('estado',1)
        ->latest()
        ->get();

        return view('compra.index',compact('compras'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $proveedores = Proveedore::all();
        $proveedores = Proveedore::whereHas('persona', function($query){
            $query->where('estado',1);
        })->get();

        $comprobantes = Comprobante::all();

        // $productos = Producto::all();
        $productos = Producto::where('estado',1)->get();

        return view('compra.create',compact('proveedores','comprobantes','productos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(StoreCompraRequest $request)
    {
        // dd($request);
        //Modificaremos tres tablas, compras, compra_producto y productos
        
        try{
            DB::beginTransaction();

            $compra = Compra::create($request->validated());

            //Lenar tabla compra_producto
            //1. Recuperar los arrays
            $arrayProducto_id = $request->get('arrayidproducto');
            $arrayCantidad = $request->get('arraycantidad');
            $arrayPrecioCompra = $request->get('arraypreciocompra');
            $arrayPrecioVenta = $request->get('arrayprecioventa');


            //2. Realizar el llenado
            $sizeArray = count($arrayProducto_id);
            $cont = 0;
            while($cont < $sizeArray){
                $compra->productos()->syncWithoutDetaching([
                    $arrayProducto_id[$cont] => [
                        'cantidad' => $arrayCantidad[$cont],
                        'precio_compra' => $arrayPrecioCompra[$cont],
                        'precio_venta' => $arrayPrecioVenta[$cont]
                    ]
                ]);

                //3. Actualizar el stock
                $producto = Producto::find($arrayProducto_id[$cont]);
                $stockActual = $producto->stock;
                $stockNuevo = intval($arrayCantidad[$cont]);

                DB::table('productos')
                ->where('id',$producto->id)
                ->update([
                    'stock' => $stockActual + $stockNuevo
                ]);

                $cont++;
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }

        return redirect()->route('compras.index')->with('success','compra exitosa');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Compra $compra)
    {
        return view('compra.show', compact('compra'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Compra::where('id',$id)
        ->update([
            'estado'=> 0
        ]);

        return redirect()->route('compras.index')->with('success','Compra eliminada');
        
    }
}
