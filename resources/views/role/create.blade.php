@extends('template')
@section('title', 'Crear rol')


@push('css')


@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Rol</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active"><a href="{{ route('panel')}}">Inicio</a></li>
        <li class="breadcrumb-item active"><a href="{{ route('roles.index')}}">Rol</a></li>
        <li class="breadcrumb-item active">Crear rol</li>
    </ol>
</div>

<div class="container w-100">
    <form action=" {{ route('roles.store')}}" method="post">
        @csrf
        <div class="row g-3">
           
            {{-- Nombre del rol --}}
            <div class="row mb-4 mt-4">
                <label for="name" class="col-sm-2 col-form-label">Nombre del rol</label>
                <div class="col-sm-4">
                    <input type="text" name="name" class="form-control" id="name" value="{{old('name')}}">
                </div>
                <div class="col-sm-6">
                    @error('name')
                        <small class="text-danger">{{'*'.$message}}</small>
                    @enderror
                </div>
            </div>

            {{-- Permisos --}}
            <div class="col-12 mb-4">
                <label for="" class="form-label">Permisos para el rol:</label>
                @foreach ($permisos as $item)
                    <div class="form-check mb-2">
                        <input type="checkbox" name="permission[]" id="{{$item->id}}" class="form-check-input" value="{{$item->id}}">
                    <label for="{{$item->id}}" class="form-check-label">{{$item->name}}</label>
                    </div>                   
                @endforeach
            </div>
            <div class="col-sm-6">
                @error('permission')
                    <small class="text-danger">{{'*'.$message}}</small>
                @enderror
            </div>


            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary">Guardar</button>
                
            </div>
        </div>
    </form>


</div>


@endsection


@push('js')

@endpush