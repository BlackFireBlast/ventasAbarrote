<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Comprobante;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Exception;

class ventaController extends Controller
{
    function __construct() {
        $this->middleware('permission:ver-venta|crear-venta|mostrar-venta|eliminar-venta',['only' => ['index']]);
        $this->middleware('permission:crear-venta',['only' => ['create','store']]);
        $this->middleware('permission:mostrar-venta',['only' => ['show','update']]);
        $this->middleware('permission:eliminar-venta',['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ventas = Venta::with(['comprobante','cliente.persona','user'])
        ->where('estado',1)
        ->latest()
        ->get();

        return view('venta.index',compact('ventas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $productos = Producto::where('estado',1)->get();
        //Solo nos interesan los precios de las compras de los productos más recientes

        //Traer el id de los productos de las compras más recientes
        $subquery = DB::table('compra_producto')
        ->select('producto_id', DB::raw('MAX(created_at) as max_created_at'))
        ->groupBy('producto_id');

        //Traer los productos que nos interesan, usamos join, in y subconsulta.
       $productos = Producto::join('compra_producto as cpr', function($join) use ($subquery){
        $join->on('cpr.producto_id','=','productos.id')
        ->whereIn('cpr.created_at',function($query) use ($subquery) {
            $query->select('max_created_at')
            ->fromSub($subquery,'subquery')
            ->whereRaw('subquery.producto_id = cpr.producto_id');
        });
       })    
       ->select('productos.nombre', 'productos.id', 'productos.stock', 'cpr.precio_venta')
       ->where('productos.estado',1)
       ->where('productos.stock','>',0)
       ->get();

        $clientes = Cliente::whereHas('persona',function($query){
            $query->where('estado',1);
        })->get();
        $comprobantes = Comprobante::all();

        return view('venta.create',compact('productos','clientes','comprobantes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVentaRequest $request)
    {
        try{
            DB::beginTransaction();
            //Llenar tabla venta
            $venta = Venta::create($request->validated());

            //Llenar tabla venta_producto
            //1. Recuperar los arrays
            $arrayProducto_id = $request->get('arrayidproducto');
            $arrayCantidad = $request->get('arraycantidad');

            $arrayPrecioVenta = $request->get('arrayprecioventa');
            $arrayDescuento = $request->get('arraydescuento');

            //Realizar el llenado
            $sizeArray = count($arrayProducto_id);
            $cont = 0;

            while($cont <$sizeArray){
                $venta->productos()->syncWithoutDetaching([
                    $arrayProducto_id[$cont] => [
                        'cantidad' => $arrayCantidad[$cont],
                        'precio_venta' => $arrayPrecioVenta[$cont],
                        'descuento' => $arrayDescuento[$cont]
                    ]
                ]);

                //Actualizar stock
                $producto = Producto::find($arrayProducto_id[$cont]);
                $stockActual = $producto->stock;
                $cantidad = intval($arrayCantidad[$cont]);

                DB::table('productos')
                ->where('id',$producto->id)
                ->update([
                    'stock' => $stockActual - $cantidad
                ]);
                $cont++;
            }

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
        }

        return redirect()->route('ventas.index')->with('success','Venta exitosa');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Venta $venta)
    {
        return view('venta.show',compact('venta'));
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
        Venta::where('id',$id)
        ->update([
            'estado'=> 0
        ]);

        return redirect()->route('ventas.index')->with('success','Venta eliminada');
    }
}
