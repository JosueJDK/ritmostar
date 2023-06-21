@extends('backend.layouts.master')
@section('title','RITMOSTAR || Banner Page')
@section('main-content')
 <!-- DataTales Example -->
 <div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('backend.layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Lista de Banners</h6>
      <a href="{{route('banner.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Crear Banner</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($banners)>0)
        <table class="table table-bordered" id="banner-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>S.N.</th>
              <th>Titulo</th>
              <th>Slug</th>
              <th>Foto</th>
              <th>Estado</th>
              <th>Accion</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>S.N.</th>
              <th>Titulo</th>
              <th>Slug</th>
              <th>Foto</th>
              <th>Estado</th>
              <th>Accion</th>
              </tr>
          </tfoot>
          <tbody>
            @foreach($banners as $banner)
                <tr>
                    <td>{{$banner->id}}</td>
                    <td>{{$banner->title}}</td>
                    <td>{{$banner->slug}}</td>
                    <td>
                        @if($banner->photo)
                            <img src="{{$banner->photo}}" class="img-fluid zoom" style="max-width:80px" alt="{{$banner->photo}}">
                        @else
                            <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid zoom" style="max-width:100%" alt="avatar.png">
                        @endif
                    </td>
                    <td>
                        @if($banner->status=='active')
                            <span class="badge badge-success w-50 h-50">{{$banner->status}}</span>
                        @else
                            <span class="badge badge-warning w-50 h-50">{{$banner->status}}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('banner.edit',$banner->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{route('banner.destroy',[$banner->id])}}">
                          @csrf
                          @method('delete')
                              <button class="btn btn-danger btn-sm dltBtn" data-id={{$banner->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
          </tbody>
        </table>
        <span style="float:right">{{$banners->links()}}</span>
        @else
          <h6 class="text-center">No hay banners, agregue uno a la lista!</h6>
        @endif
      </div>
    </div>
</div>
@endsection

@push('scripts')

  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <script>
      $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
          $('.dltBtn').click(function(e){
            var form=$(this).closest('form');
              var dataID=$(this).data('id');
              // alert(dataID);
              e.preventDefault();
              swal({
                    title: "Estas seguro?",
                    text: "¡Una vez eliminados, no podrá recuperar estos datos!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                       form.submit();
                    } else {
                        swal("¡Tus datos están seguros!");
                    }
                });
          })
      })
  </script>
@endpush
