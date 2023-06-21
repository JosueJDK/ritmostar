@extends('backend.layouts.master')

@section('main-content')
 <!-- DataTales Example -->
 <div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('backend.layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Lista de Cursos</h6>
      <a href="{{route('course.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Añadir Curso</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($courses)>0)
        <table class="table table-bordered" id="product-dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                  <th>Nº</th>
                  <th>Título</th>
                  <th>Categoría</th>
                  <th>Destacado</th>
                  <th>Precio</th>
                  <th>Descuento</th>
                  <th>Condición</th>
                  <th>Vacantes</th>
                  <th>Foto</th>
                  <th>Estado</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>Nº</th>
                  <th>Título</th>
                  <th>Categoría</th>
                  <th>Destacado</th>
                  <th>Precio</th>
                  <th>Descuento</th>
                  <th>Condición</th>
                  <th>Vacantes</th>
                  <th>Foto</th>
                  <th>Estado</th>
                  <th>Acción</th>
                </tr>
              </tfoot>
          <tbody>

            @foreach($courses as $course)
              @php
              $sub_cat_info=DB::table('categories')->select('title')->where('id',$course->child_cat_id)->get();
              @endphp
                <tr>
                    <td>{{$course->id}}</td>
                    <td>{{$course->title}}</td>
                    <td>{{$course->cat_info['title']}}
                      <sub>
                          {{$course->sub_cat_info->title ?? ''}}
                      </sub>
                    </td>
                    <td>{{(($course->is_featured==1)? 'Yes': 'No')}}</td>
                    <td>Rs. {{$course->price}} /-</td>
                    <td>  {{$course->discount}}% OFF</td>
                    <td>{{$course->condition}}</td>
                    <td>
                      @if($course->vacantes>0)
                      <span class="badge badge-primary">{{$course->vacantes}}</span>
                      @else
                      <span class="badge badge-danger">{{$course->vacantes}}</span>
                      @endif
                    </td>
                    <td>
                        @if($course->photo)
                            @php
                              $photo=explode(',',$course->photo);
                              // dd($photo);
                            @endphp
                            <img src="{{$photo[0]}}" class="img-fluid zoom" style="max-width:80px" alt="{{$course->photo}}">
                        @else
                            <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid" style="max-width:80px" alt="avatar.png">
                        @endif
                    </td>
                    <td>
                        @if($course->status=='active')
                            <span class="badge badge-success">{{$course->status}}</span>
                        @else
                            <span class="badge badge-warning">{{$course->status}}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('course.edit',$course->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{route('course.destroy',[$course->id])}}">
                      @csrf
                      @method('delete')
                          <button class="btn btn-danger btn-sm dltBtn" data-id={{$course->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
          </tbody>
        </table>
        <span style="float:right">{{$courses->links()}}</span>
        @else
          <h6 class="text-center">No hay cursos!!! Aguegue cursos!</h6>
        @endif
      </div>
    </div>
</div>
@endsection

@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
  <style>
      div.dataTables_wrapper div.dataTables_paginate{
          display: none;
      }
      .zoom {
        transition: transform .2s; /* Animation */
      }

      .zoom:hover {
        transform: scale(5);
      }
  </style>
@endpush

@push('scripts')

  <!-- Page level plugins -->
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>
  <script>

      $('#product-dataTable').DataTable( {
        "scrollX": false
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[10,11,12]
                }
            ]
        } );

        // Sweet alert

        function deleteData(id){

        }
  </script>
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
                title: "¿Estás seguro?",
                text: "¡Una vez eliminado, no podrás recuperar estos datos!",
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
