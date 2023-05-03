@extends('layouts.portal.principal')

@section('css_custom')
  <link href="{{ asset('css/portal/style-datetable.css') }}" rel="stylesheet">
  <style>
    .table th, .table td {
      padding: 0.35rem !important;
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
  <script src="{{ asset('theme/lib/datatables/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('theme/lib/datatables-responsive/dataTables.responsive.js') }}"></script>
  <script src="{{ asset('js/portal/admin/usuarios/user.index.js') }}"></script>
@endsection

@section('content')
  <div class="br-mainpanel">
    @include('includes/mensaje')
    @include('includes/form-error')
    <div class="br-pageheader pd-y-15 pd-l-20">
      <nav class="breadcrumb pd-0 mg-0 tx-12">
        <a class="breadcrumb-item" href="#">Panel administración</a>
        <span class="breadcrumb-item active">Usuarios</span>
      </nav>
    </div><!-- br-pageheader -->

    

    <div class="br-pagebody" style="padding: 0 10px;">
      <div class="br-section-wrapper">

        <table width='100%' style="margin-bottom: 40px;">
          <tr>
              <td width='50' align="center" valign="top" class="pr-4">
                  <h1 class="text-primary"><i class="fa fa-users" aria-hidden="true"></i></h1>
              </td>
              <td>
                  <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Usuarios</h4>
                  <span class='titulos'><?php echo date('Y-m-d h:m'); ?></span>
              </td>
          </tr>
        </table>

        <div class="mb-4">
          @can('user-create')
          <a  href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Nuevo usuario</a>
          @endcan
        </div>

        <div class="table-wrapper">
          
          <table id="datatable1" class="display table table-hover" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th>#</th>
                <th>Usuario</th>
                <th>Name</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($data as $key => $user)
                <tr>
                  <td class="text-center">{{ $key+1 }}</td>
                  <td>{{ $user->login }}</td>
                  <td class="text-u">{{ $user->name }}</td>
                  <td class="text-l">{{ $user->email }}</td>
                  <td>
                    @if(!empty($user->getRoleNames()))
                      @foreach($user->getRoleNames() as $v)
                        <label class="badge badge-success">{{ $v }}</label>
                      @endforeach
                    @endif
                  </td>
                  <td class="text-center">
                    <a href="{{ route('users.show',$user->id) }}" class="btn-accion-tabla tooltipsC" title="ver registro">
                      <i class="fa fa-eye"></i>
                    </a>
                    @can('user-edit')
                      <a href="{{ route('users.edit',$user->id) }}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                          <i class="fa fa-edit"></i>
                      </a>
                    @endcan
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table><!-- table-wrapper -->
        </div>
      </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->
    <footer class="br-footer">
      <div class="footer-left">
        <div class="mg-b-2"></div>
        <div></div>
      </div>
      <div class="footer-right d-flex align-items-center">
        <span class="tx-uppercase mg-r-10">Share:</span>
      </div>
    </footer>
  </div>

  <!-- MODAL -->
  <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
    <div class="modal-dialog" role="document" style="width: 50%">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirm.titulo">Confirmación</h5>
        </div>
        <div class="modal-body">
          <p>¿Se va a eliminar el usuario, desea continuar?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="users.anular()">Aceptar</button>
        </div>
      </div>
    </div>
    
  </div>
  <!-- FIN MODAL -->


@endsection
