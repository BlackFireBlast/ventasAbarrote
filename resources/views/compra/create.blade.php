@extends('template')
@section('title','Crear compra')
    

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
@endpush


@section('content')

    <div class="container-fluid px-4">
        <h1 class="mt-4 text-center">Crear compra</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active"><a href="{{ route('panel')}}">Inicio</a></li>
            <li class="breadcrumb-item active"><a href="{{ route('compras.index')}}">Compra</a></li>
            <li class="breadcrumb-item active">Crear compra</li>
        </ol>
    </div>

    <form action="{{route('compras.store')}}" method="post">
        @csrf
        <div class="container mt-4">
            <div class="row gy-4">

                {{-- Compra producto --}}
                <div class="col-md-8">
                    <div class="text-white bg-primary p-1 text-center">
                        Detalles de la compra
                    </div>
                    <div class="p-3 border border-primary border-3">
                        <div class="row">
                            {{-- Producto --}}
                            <div class="col-md-12 mb-2">
                                <select name="producto_id" id="producto_id" class="form-control selectpicker" data-live-search="true" data-size="2" title="Busque un producto">
                                    @foreach ($productos as $item)
                                        <option value="{{$item->id}}">{{$item->codigo.' '.$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                             {{-- Cantidad --}}
                             <div class="col-md-4 mb-2">
                               <label for="cantidad" class="form-label">Cantidad:</label>
                               <input type="number" name="cantidad" id="cantidad" class="form-control">
                            </div>
                             {{-- Precio de compra --}}
                             <div class="col-md-4 mb-2">
                                <label for="precio_compra" class="form-label">Precio de compra:</label>
                                <input type="number" name="precio_compra" id="precio_compra" class="form-control" step="0.1">
                             </div>
                             {{-- Precio de venta --}}
                             <div class="col-md-4 mb-2">
                                <label for="precio_venta" class="form-label">Precio de venta:</label>
                                <input type="number" name="precio_venta" id="precio_venta" class="form-control" step="0.1">
                             </div>
                             {{-- Boton para agregar --}}
                             <div class="col-md-12 mb-2 text-end">
                                <button id="btn_agregar" type="button" class="btn btn-primary ">Agregar</button>
                             </div>
                             {{-- Tabla para el detalle de la compra --}}
                             <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="tabla_detalle" class="table table-hover">
                                        <thead class="bg-primary text-white">
                                            <tr>
                                                <th>#</th>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Precio compra</th>
                                                <th>Precio venta</th>
                                                <th>Subtotal</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th colspan="4">Sumas</th>
                                                <th colspan="2"><span id="sumas">0</span></th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th colspan="4">IGV %</th>
                                                <th colspan="2"><span id="igv">0</span></th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th colspan="4">Total</th>
                                                <th colspan="2"><input type="hidden" name="total" value="0" id="inputTotal"><span id="total">0</span></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                             </div>

                             {{-- Boton para cancelar compra --}}
                             <div class="col-md-12 mb-2">
                                <button type="button" id="cancelar" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Cancelar compra
                                </button>

                             </div>
                        </div>
                    </div>
                </div>

                {{-- Producto --}}
                <div class="col-md-4">
                    <div class="text-white bg-success p-1 text-center">
                        Datos generales
                    </div>
                    <div class="p-3 border border-success border-3">
                        <div class="row">

                            {{-- Proveedor --}}
                            <div class="col-md-12 mb-2">
                                <label for="proveedore_id" class="form-label">Proovedor:</label>
                                <select name="proveedore_id" id="proveedore_id" class="form-control selectpicker show-tick" data-live-search='true'  title="Selecciona" data-size=2>
                                    @foreach ($proveedores as $item)
                                        <option value="{{$item->id}}">{{$item->persona->razon_social}}</option>
                                    @endforeach
                                </select>
                                @error('proveedore_id')
                                    <small class="text-danger">{{'*'.$message}}</small>
                                @enderror
                            </div>

                             {{-- Tipo de comprobante --}}
                             <div class="col-md-12 mb-2">
                                <label for="comprobante_id" class="form-label">Comprobante:</label>
                                <select name="comprobante_id" id="comprobante_id" class="form-control selectpicker show-tick" title="Selecciona">
                                    @foreach ($comprobantes as $item)
                                        <option value="{{$item->id}}">{{$item->tipo_comprobante}}</option>
                                    @endforeach
                                </select>
                                @error('comprobante_id')
                                    <small class="text-danger">{{'*'.$message}}</small>
                                @enderror
                            </div>

                            {{-- Numero de comprobante --}}
                            <div class="col-md-12 mb-2">
                                <label for="numero_comprobante" class="form-label">Número de comprobante:</label>
                                <input type="text" name="numero_comprobante" id="numero_comprobante" class="form-control" required>
                                @error('numero_comprobante')
                                    <small class="text-danger">{{'*'.$message}}</small>
                                @enderror
                            </div>

                            {{--Impuesto--}}
                            <div class="col-md-6 mb-2">
                                <label for="impuesto" class="form-label">Impuesto:</label>
                                <input type="text" name="impuesto" id="impuesto" class="form-control border-success" readonly>
                                @error('impuesto')
                                    <small class="text-danger">{{'*'.$message}}</small>
                                @enderror
                            </div>

                            {{--Fecha--}}
                            <div class="col-md-6 mb-2">
                                <label for="fecha" class="form-label">Impuesto:</label>
                                <input type="date" name="fecha" id="fecha" class="form-control border-success" value="<?php echo date("Y-m-d") ?>" readonly>
                                <?php
                                    use Carbon\Carbon;
                                    $fecha_hora = Carbon::now()->toDateTimeString();
                                ?>
                                <input type="hidden" name="fecha_hora" value="{{$fecha_hora}}">
                            </div>


                            {{-- Botones --}}
                            <div class="col-md-12 text-center mb-2">
                                <button  type="submit" class="btn btn-success" id="guardar">Guardar</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal para cancelar la compra-->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal de confirmación</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                ¿Seguro que quieres cancelar la compra?
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button id="btnCancelarCompra" type="button" class="btn btn-danger" data-bs-dismiss="modal">Confirmar</button>
                </div>
            </div>
            </div>
        </div>


    </form>
@endsection


@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(function(){
        $('#btn_agregar').click(function(){
            agregarProducto()
           
        })

        $('#impuesto').val(impuesto +'%')

        $('#btnCancelarCompra').click(function(){
            cancelarCompra()
        })

        disableButtons()
    })

    //Variables
    let cont = 0
    let subtotal = []
    let sumas = 0;
    let igv= 0
    let total = 0

    //Constantes
    const impuesto = 18

    function cancelarCompra(){
        $('#tabla_detalle > tbody').empty()

        let fila = '<tr>' +
                                                '<td></td>'+
                                                '<td></td>'+
                                                '<td></td>'+
                                               ' <td></td>'+
                                                '<td></td>'+
                                                '<td></td>'+
                                                '<td></td>'+
                                            '</tr>'
        $('#tabla_detalle').append(fila)

        //Reiniciar valores de las variables
         cont = 0
         subtotal = []
         sumas = 0
         igv = 0
         total = 0

        //Mostrar los campos calculados
        $('#sumas').html(sumas)
        $('#igv').html(igv)
        $('#total').html(total)
        $('#impuesto').val(impuesto + '%')
        $('#inputTotal').val(total)


        limpiarCampos()
        disableButtons()
    }

    function disableButtons(){
        if(total == 0){
            $('#guardar').hide()
            $('#cancelar').hide()
        }else{
            $('#guardar').show()
            $('#cancelar').show()
        }
    }

    function agregarProducto(){

        let idProducto = $('#producto_id').val();
        //Obtener solo el nombre sin el código
        let nameProducto = ($('#producto_id option:selected').text()).split(' ')[1]
        let cantidad = $('#cantidad').val()
        let precioCompra = $('#precio_compra').val()
        let precioVenta = $('#precio_venta').val()

        //Validaciones
        //1. Para que los campos no esten vacíos
        if(nameProducto!= '' && cantidad!='' && precioCompra!='' &&precioVenta!=''){

            //2. Para que los valores ingresados sean los correctos
            if (parseInt(cantidad) > 0 && (cantidad%1 == 0) && parseFloat(precioCompra) > 0 && parseFloat(precioVenta)> 0 ) {
           
            //3.Precio de venta mayor a precio de compra
            if(parseFloat(precioVenta) > parseFloat(precioCompra)){

           
            
        //Calcular valores
        subtotal[cont] = round(cantidad * precioCompra)
        sumas+=subtotal[cont]
        igv = round(sumas/100 * impuesto)
        total = round(sumas + igv)


        let fila = '<tr id="fila'+cont+'">' +
            '<td>' + (cont +1) + '</td>' +
            '<td><input type="hidden" name="arrayidproducto[]" value="'+idProducto+'">' + nameProducto + '</td>' +
            '<td><input type="hidden" name="arraycantidad[]" value="'+cantidad+'">' + cantidad + '</td>' +
            '<td><input type="hidden" name="arraypreciocompra[]" value="'+precioCompra+'">' + precioCompra + '</td>' +
            '<td><input type="hidden" name="arrayprecioventa[]" value="'+precioVenta+'">' + precioVenta + '</td>' +
            '<td>' + subtotal[cont] + '</td>' +
            '<td><button class="btn btn-danger" type="button" onClick="eliminarProducto('+cont+')"><i class="fa-solid fa-trash"></i></button></td>' +
            '</tr>'


        //Acciones después de añadir la fila
        $('#tabla_detalle').append(fila)
        limpiarCampos()
        cont++
        disableButtons()

        //Mostrar los campos calculados
        $('#sumas').html(sumas)
        $('#igv').html(igv)
        $('#total').html(total)
        $('#impuesto').val(igv)
        $('#inputTotal').val(total)


        }else{
                showModal('Precio de compra incorrecto')    
        }

        } else {//Mostrar alerta
            showModal('Valores incorrectos')        
        }


        }else{//Mostrar alerta
            showModal('Le faltan campos por llenar')
        }

    }


    function eliminarProducto(indice){

        sumas = round(sumas - subtotal[indice])
        igv = round(sumas/100*impuesto)
        total= round(sumas+ igv)

        //Mostrar los campos calculados
        $('#sumas').html(sumas)
        $('#igv').html(igv)
        $('#total').html(total)
        $('#impuesto').val(igv)
        $('#inputTotal').val(total)



        //Eliminar la fila de la tabla
        $('#fila'+indice).remove()
    }


    function limpiarCampos(){
        let select = $('#producto_id')
        select.selectpicker('val', '')
        $('#cantidad').val('')
        $('#precio_compra').val('')
        $('#precio_venta').val('')
    }


    function round(num, decimales = 2) {
    var signo = (num >= 0 ? 1 : -1);
    num = num * signo;
    if (decimales === 0) //con 0 decimales
        return signo * Math.round(num);
    // round(x * 10 ^ decimales)
    num = num.toString().split('e');
    num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
    // x * 10 ^ (-decimales)
    num = num.toString().split('e');
    return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }


    function showModal(message, icon = 'error'){
        const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
        });
        Toast.fire({
        icon: icon,
        title: message
        });
    }

</script>
@endpush

