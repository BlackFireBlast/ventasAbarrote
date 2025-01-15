@extends('template')
@section('title', 'Editar usuario')


@push('css')


@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Editar usuario</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active"><a href="{{ route('panel')}}">Inicio</a></li>
        <li class="breadcrumb-item active"><a href="{{ route('users.index')}}">Usuario</a></li>
        <li class="breadcrumb-item active">Editar usuario</li>
    </ol>
</div>

<div class="container w-100">
    <form action=" {{ route('users.update',['user'=>$user])}}" method="post">
        @method('PATCH')
        @csrf
        <div class="row g-3">
           
            {{-- Nombre --}}
            <div class="row mb-4 mt-4">
                <label for="name" class="col-sm-2 col-form-label">Nombres:</label>
                <div class="col-sm-4">
                    <input type="text" name="name" class="form-control" id="name" value="{{old('name',$user->name)}}">
                </div>
                <div class="col-sm-4">
                    <div class="form-text">
                        Escribe un solo nombre
                    </div>
                </div>
                <div class="col-sm-2">
                    @error('name')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>
            </div>

              
            {{-- Email --}}
            <div class="row mb-4">
                <label for="email" class="col-sm-2 col-form-label">Email:</label>
                <div class="col-sm-4">
                    <input type="email" name="email" class="form-control" id="email" value="{{old('email',$user->email)}}">
                </div>
                <div class="col-sm-4">
                    <div class="form-text">
                        Dirección de correo eléctronico
                    </div>
                </div>
                <div class="col-sm-2">
                    @error('email')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>
            </div>


                

                {{-- Password --}}
               <div class="row mb-4">
                <label for="password" class="col-sm-2 col-form-label">Contraseña:</label>
                <div class="col-sm-4">
                    <input type="password" name="password" class="form-control" id="password">
                </div>
                <div class="col-sm-4">
                    <div class="form-text">
                        Escriba una contraseña segura. Debe incluir números.
                    </div>
                </div>
                <div class="col-sm-2">
                    @error('password')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>
                </div>

                  {{--Confirm Password --}}
               <div class="row mb-4">
                <label for="password_confirm" class="col-sm-2 col-form-label">Confirmar contraseña:</label>
                <div class="col-sm-4">
                    <input type="password" name="password_confirm" class="form-control" id="password_confirm">
                </div>
                <div class="col-sm-4">
                    <div class="form-text">
                        Vuelva a escribir su contraseña.
                    </div>
                </div>
                <div class="col-sm-2">
                    @error('password_confirm')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>
                </div>


                {{-- Roles --}}
               <div class="row mb-4">
                <label for="role" class="col-sm-2 col-form-label">Seleccionar rol:</label>
                <div class="col-sm-4">
                    <select name="role" id="role" class="form-select">
                        {{-- <option value="" disabled selected>Seleccione:</option> --}}
                        @foreach ($roles as $item)

                            @if (in_array($item->name,$user->roles->pluck('name')->toArray()))
                            <option selected value="{{$item->name}}" @selected(old('role')== $item->name) > {{$item->name}} </option>
 
                            @else
                            <option value="{{$item->name}}" @selected(old('role')== $item->name) > {{$item->name}} </option>
 
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <div class="form-text">
                        Escoja un rol para el usuario.
                    </div>
                </div>
                <div class="col-sm-2">
                    @error('role')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>
                </div>



            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary">Actualizar</button>
                
            </div>
        </div>
    </form>


</div>


@endsection


@push('js')

@endpush