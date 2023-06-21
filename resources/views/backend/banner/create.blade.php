@extends('backend.layouts.master')

@section('title','RITMOSTAR || Crear Banner ')

@section('main-content')

<div class="card">
    <h5 class="card-header">Añadir Banner</h5>
    <div class="card-body">
      <form method="post" action="{{route('banner.store')}}"  enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Titulo <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" value="{{old('title')}}" class="form-control">
        @error('title')
        <span class="text-danger">{{$message}}</span>
        @enderror
        </div>

        <div class="form-group">
          <label for="inputDesc" class="col-form-label">Descripcion</label>
          <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
        <label for="inputPhoto" class="col-form-label">Foto <span class="text-danger">*</span></label>
        <div class="input-group">
          <input id="photo" class="form-control" type="file" name="photo" >
        </div>
        <div id="holder" style="margin-top:15px;max-height:100px;"></div>
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="status" class="col-form-label">Estado <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
              <option value="active">Activo</option>
              <option value="inactive">Inactivo</option>
          </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
            <button type="reset" class="btn btn-warning">Limpiar</button>
            <button class="btn btn-success" type="submit">Enviar</button>
        </div>
      </form>
    </div>
</div>

@endsection
