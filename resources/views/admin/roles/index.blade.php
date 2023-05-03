@extends('layouts.portal.principal')

@section('css_custom')
  <link href="{{ asset('css/portal/style-datetable.css') }}" rel="stylesheet">
  <style>
    .table th, .table td {
      padding: 0.25rem !important;
    }
    .table {
      font-size: 11px;
    }
    .font-head {
      font-weight: 700;
      font-size: 12px;
      text-transform: uppercase;
      color: #343a40;
      letter-spacing: 0.5px;
    }
    .text-u {
      text-transform: uppercase !important;
    } 
    .text-l {
      text-transform: lowercase !important;
    } 
  </style>
@endsection

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
          <span class="breadcrumb-item active">Roles</span>
        </nav>
      </div><!-- br-pageheader -->

      <div class="br-pagebody">
        <div class="br-section-wrapper">
          <div class="card-body table-responsive p-0">
            <table width='100%' style="margin-bottom: 40px;">
              <tr>
                  <td width='50' align="center" valign="top" class="pr-4">
                      <h1 class="text-primary"><i class="fa fa-user-plus" aria-hidden="true"></i></h1>
                  </td>
                  <td>
                      <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Roles</h4>
                      <span class='titulos'><?php echo date('Y-m-d h:m'); ?></span>
                  </td>
              </tr>
            </table>
            <div class="mb-4">
              @can('role-create')
                  <a  href="{{ route('roles.create') }}" style="width: 150px;" class="btn btn-primary btn-sm">Crear rol</a>
              @endcan
            </div>
            <table class="table table-hover width60" id="tabla-data" style="font-size: 12px;">
                <thead>
                    <tr>
                        <th class="width20">ID</th>
                        <th class="width60">Nombre</th>
                        <th class="width20">Action</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach ($roles as $key => $role)
                    <tr>
                      <td>{{$role->id}}</td>
                      <td>{{$role->name}}</td>
                      <td>
                        {{--  @can('role-edit')  --}}
                        <a href="{{ route('roles.edit',$role->id) }}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                            <i class="fa fa-edit"></i>
                        </a>
                        {{--  @endcan  --}}
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
