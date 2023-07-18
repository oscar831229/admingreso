@extends('layouts.belectronica.principal')


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
    <script src="{{ asset('js/plugins/jquery.mask/jquery.maskedinput.js') }}"></script>
    <script src= "{{ asset('js/plugins/jquery.autocomplete/js/jquery.autocomplete.min.js') }}"></script> 
    <script src="{{ asset('js/portal/wallet/business-users/index.js') }}"></script>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12 ">
      @include('includes/mensaje')
      @include('includes/form-error')
      <div class="x_panel">
        <div class="x_title">
          <div class="br-pageheader pd-y-15 pd-l-20">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
              <a class="breadcrumb-item" href="#">Administrador</a>
              <span class="breadcrumb-item active">Permisos comercios</span>
            </nav>
          </div><!-- br-pageheader -->
          <div class="clearfix"></div>
        </div>
        <div class="x_content">

            <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
                @csrf
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Nombre usuario..." id='username'>
                    <input class="form-control" type="text" id="username-x" disabled="disabled" style="color: #CCC; position: absolute; background: transparent; z-index: 1; display:none"/>
                    <input type="hidden" name="userid" id="userid">
                    <span class="input-group-append">
                        <button class="btn btn-dark" type="button" id="load">
                            Cargar datos
                        </button>
                    </span>
                </div>
            </div>

            <table width='100%' style="margin-bottom: 20px;">
                <tr>
                    <td width='50' align="center" valign="top" class="pr-4">
                        <h1><i class="fa fa-users" aria-hidden="true"></i></h1>
                    </td>
                    <td>
                        <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Permisos usuarios - comercios</h4>
                        <span class='titulos'><?php echo date('yy-m-d h:m'); ?></span>
                    </td>
                </tr>
            </table>

            <table class="table width60" id="tabla-data">
                <thead>
                    <tr>
                        <th class="width20">ID</th>
                        <th class="width30">Usuario</th>
                        <th class="width30">Identificación</th>
                        <th class="width30">Nombre</th>
                        <th class="width30">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id='id'></td>
                        <td id='login'></td>
                        <td id='document_number'></td>
                        <td id='name'></td>
                        <td id='estado'></td>
                    </tr>
                </tbody>
            </table>
            <div class="card card-success">
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-bordered table-hover" id="tblpermissionuser">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre comercio</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th class="text-center">Habilitar</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{--  <tr>
                                <td class="pl-40"><i class="fa fa-arrow-right pr-4"></i>Hospital universitario</td>
                                <td class="text-center">
                                    <input type="checkbox" class="menu_rol" name="menu_rol[]" data-menuid="2" value="1" checked="">
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-40"><i class="fa fa-arrow-right pr-4"></i>Proadsalud</td>
                                <td class="text-center">
                                    <input type="checkbox" class="menu_rol" name="menu_rol[]" data-menuid="2" value="1" checked="">
                                </td>
                            </tr>
                            <tr>
                                <td class="pl-40"><i class="fa fa-arrow-right pr-4"></i>Anesmedic</td>
                                <td class="text-center">
                                    <input type="checkbox" class="menu_rol" name="menu_rol[]" data-menuid="2" value="1" checked="">
                                </td>
                            </tr>  --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection