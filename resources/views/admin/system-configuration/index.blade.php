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
        .table th, .table td {
            padding: 0.25rem !important;
        }
        .table {
            font-size: 13px;
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
    <script src="{{ asset('theme/lib/internacionalizacion/es.js') }}"></script>
    <script src="{{ asset('theme/lib/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/portal/admin/system-configuration/index.js') }}"></script>
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
              <a class="breadcrumb-item" href="#">Administración</a>
              <span class="breadcrumb-item active">Configuracion del sistema</span>
            </nav>
          </div><!-- br-pageheader -->
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="card-body p-0">
                <table width='100%' style="margin-bottom: 20px;">
                    <tr>
                        <td width='50' align="center" valign="top" class="pr-4">
                            <h1 class="text-primary"><i class="fa fa-cogs" aria-hidden="true"></i></h1>
                        </td>
                        <td>
                            <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Configuración del sistema</h4>
                            <span class='titulos'>&nbsp;</span>
                        </td>
                    </tr>
                </table>

                @if ($create)
                <button class="btn btn-warning btn-sm btn-block mg-b-10" id="btn-new-configuration"><i class="fa fa-plus-square-o mg-r-10"></i> INGRESAR CONFIGURACIÓN</button>
                @endif

                <div class="row text-center mt-2">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="rounded table-responsive">
                                <table class="table table-bordered dataTable" style="width: 99%;" id="tbl-configuration">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Url pos bamboo</th>
                                            <th>Politica grupo familiar</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
      </div>
    </div>
  </div>

    {{--  Modal nuevo o actualización producto --}}
    <div class="modal fade" id="md-configuration" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-white bg-primary">
                    <h6 class="modal-title" id="exampleModalLabel"><i class="mdi mdi-store"></i><span id="label-type">Tipos de tarifa</span></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-xl-12 mg-t-20 mg-xl-t-0">
                            <div class="form-layout form-layout-5 bd-info">
                                {{ Form::open(array(
                                    'id'=>'form-configuration',
                                    'autocomplete'=>'off',
                                    'onsubmit' => 'return false;'
                                )) }}
                                    <div class="form-group">
                                        {!! Form::label('url_pos_system','Nombre tarifa', [], false) !!}
                                        {!! Form::text('url_pos_system', null, array('id' => 'url_pos_system','placeholder' => 'Url sistema POS Bamboo','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                        {!! Form::hidden('id') !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('policy_enabled','Politica reportar grupo familiar', [], false) !!}
                                        {!! Form::select('policy_enabled',['0'=>'Inactiva', '1' => 'Activa'],null, array('id' => 'state','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('state','Estado', [], false) !!}
                                        {!! Form::select('state',['A'=>'Activa', 'I' => 'Inactiva'],null, array('id' => 'state','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('infrastructure_code','Código infraestructura', [], false) !!}
                                        {!! Form::text('infrastructure_code', null, array('placeholder' => 'Código infraestructura','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('system_names','Nombre sistema', [], false) !!}
                                        {!! Form::text('system_names', null, array('placeholder' => 'Nombre sistema','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                {{ Form::close() }}
                                <div class="row">
                                    <div class="col-sm-12 text-center">
                                        <div class="form-group">
                                            <br>
                                            <button class="btn btn-success btn-sm" id="btn-save"><i class="fa fa-floppy-o mg-r-10"></i> Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- form-layout -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    {{--  Fin modal nuevo o actualización producto  --}}
@endsection
