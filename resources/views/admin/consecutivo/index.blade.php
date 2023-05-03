@extends('layouts.portal.principal')

@section('scripts_content')
<script src="{{ asset('js/portal/admin/roles/rol.index.js') }}"></script>
@endsection


@section('content')
<div class="br-mainpanel">
      @include('includes/mensaje')
      @include('includes/form-error')
      <div class="br-pageheader pd-y-15 pd-l-20">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
          <a class="breadcrumb-item" href="#">Panel administración</a>
          <span class="breadcrumb-item active">Consecutivos</span>
        </nav>
      </div><!-- br-pageheader -->

      <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
        @can('role-create')
            <a  href="{{ route('consecutivo.create') }}" class="btn btn-outline-secondary btn-sm"> Crear consecutivo</a>
        @endcan
      </div>

      <div class="br-pagebody">
        <div class="br-section-wrapper p-0">
          <div class="card-body table-responsive p-0">
            <table class="table table-hover" id="tabla-data">
                <thead>
                    <tr>
                        <th class="width5">ID</th>
                        <th class="width10">Prefijo</th>
                        <th class="width10">Cons. inicial</th>
                        <th class="width10">Cons. final</th>
                        <th class="width10">Cons. actual</th>
                        <th class="width10">Observación</th>
                        <th class="width10">Estado</th>
                        <th class="width10"></th>
                    </tr>
                </thead>
                <tbody>
                  @foreach ($consecutivos as $key => $consecutivo)
                    <tr>
                      <td>{{$consecutivo->id}}</td>
                      <td>{{$consecutivo->prefijo}}</td>
                      <td>{{$consecutivo->consecutivo_inicial}}</td>
                      <td>{{$consecutivo->consecutivo_final}}</td>
                      <td>{{$consecutivo->consecutivo_actual}}</td>
                      <td>{{$consecutivo->observacion}}</td>
                      <td><label class="badge @php echo $consecutivo->estado == 'A' ? 'badge-success':'badge-secondary' @endphp">@php echo $consecutivo->estado == 'A' ? 'Activo':'Inactivo' @endphp</label></td>
                      <td>
                        @can('consecutivo-edit')
                        <a href="{{ route('consecutivo.edit',$consecutivo->id) }}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                            <i class="fa fa-edit"></i>
                        </a>
                        @endcan
                        @can('consecutivo-delete')
                        {!! Form::open(['method' => 'DELETE','route' => ['consecutivo.destroy', $consecutivo->id],'style'=>'display:inline']) !!}
                            <a class="btn-accion-tabla eliminar tooltipsC submit-eliminar" title="Eliminar este registro">
                                <i class="fa fa-times-circle text-danger"></i>
                            </a>
                        {!! Form::close() !!}
                        @endcan
                      </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
          
          {{-- <div class="table-wrapper">
            
            <table id="datatable1" class="table display responsive nowrap dataTable no-footer dtr-inline" role="grid" aria-describedby="datatable1_info" style="width: 1110px;">
              <thead>
                <tr role="row">
                  <th class="wd-15p sorting_asc" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" style="width: 143px;" aria-sort="ascending" aria-label="First name: activate to sort column descending">#</th>
                  <th class="wd-15p sorting" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" style="width: 143px;" aria-label="Last name: activate to sort column ascending">Name</th>
                  <th class="wd-20p sorting" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" style="width: 198px;" aria-label="Position: activate to sort column ascending">Action</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($roles as $key => $role)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $role->name }}</td>
                    <td>
                        <a class="btn btn-info btn-sm" href="{{ route('roles.show',$role->id) }}">Show</a>
                        @can('role-edit')
                            <a class="btn btn-primary btn-sm" href="{{ route('roles.edit',$role->id) }}">Edit</a>
                        @endcan
                        @can('role-delete')
                            {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                            {!! Form::close() !!}
                        @endcan
                    </td>
                </tr>
                @endforeach
              </tbody>
            </table><!-- table-wrapper --> --}}

          
            </div>
        </div><!-- br-section-wrapper -->
      </div><!-- br-pagebody -->
      <footer class="br-footer">
        <div class="footer-left">
          <div class="mg-b-2"></div>
          <div></div>
        </div>
        <div class="footer-right d-flex align-items-center">
          <span class="tx-uppercase mg-r-10"></span>
          </div>
      </footer>
    </div>
@endsection
