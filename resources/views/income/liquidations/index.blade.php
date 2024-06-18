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
    <script src="{{ asset('js/portal/income/liquidations/index.js') }}"></script>
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
              <a class="breadcrumb-item" href="#">Ingreso a sedes</a>
              <span class="breadcrumb-item active">Liquidaciones generadas</span>
            </nav>
          </div><!-- br-pageheader -->
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="card-body p-0">
                <table width='100%' style="margin-bottom: 20px;">
                    <tr>
                        <td width='50' align="center" valign="top" class="pr-4">
                            <h1 class="text-primary"><i class="fa fa-list" aria-hidden="true"></i></h1>
                        </td>
                        <td>
                            <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Liquidaciones generadas</h4>
                            <span class='titulos'>&nbsp;</span>
                        </td>
                    </tr>
                </table>

                <div class="row">
                    <div class="col-sm-12">
                      <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                {!! Form::label('date_from','<i class="fa fa-play-circle text-success" aria-hidden="true"></i> Estado liquidación', [], false) !!}
                                {!! Form::select('state_liquidation', ['P' => 'PENDIENTES', 'F' => 'FACTURADA' ],  null, ['class' => 'form-control form-control-sm', 'id' => 'state_liquidation', 'placeholder' => 'Seleccione...', 'style'=>'height: 25px;']) !!}
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                {!! Form::label('date_from','<i class="fa fa-calendar" aria-hidden="true"></i> Fecha inicial', [], false) !!}
                                {!! Form::date('date_from', null, array('id' => 'date_from','placeholder' => 'Número de lote','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                {!! Form::label('date_to','<i class="fa fa-calendar" aria-hidden="true"></i> Fecha final', [], false) !!}
                                {!! Form::date('date_to', null, array('id' => 'date_to','placeholder' => 'Número de lote','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <br>
                                <button class="btn btn-primary btn-sm mr-3" id="btn-refresh-liquidation"><i class="fa fa-refresh mg-r-10"></i> Actualizar </button>
                            </div>
                        </div>
                      </div>
                    </div>
                </div>

                <div class="row text-center mt-2">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="rounded table-responsive">
                                <table class="table table-bordered dataTable" style="width: 99%;" id="tbl-liquidations">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Número liquidación</th>
                                            <th>Fecha liquidación</th>
                                            <th>Nombre cliente</th>
                                            <th>Ambiente</th>
                                            <th>Estado</th>
                                            <th>Factura</th>
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
    <div class="modal fade" id="md-rate-type" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-white bg-primary">
                    <h6 class="modal-title" id="exampleModalLabel"><i class="mdi mdi-store"></i><span id="label-type">Categoria afiliados</span></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-xl-12 mg-t-20 mg-xl-t-0">
                            <div class="form-layout form-layout-5 bd-info">
                                {{ Form::open(array(
                                    'id'=>'form-liquidations',
                                    'autocomplete'=>'off',
                                    'onsubmit' => 'return false;'
                                )) }}
                                    <div class="form-group">
                                        {!! Form::label('code','Código <span class="text-danger">*</span>', [], false) !!}
                                        {!! Form::text('code', null, array('placeholder' => 'Código categoria','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                        {!! Form::hidden('id') !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('name','Nombre <span class="text-danger">*</span>', [], false) !!}
                                        {!! Form::text('name', null, array('placeholder' => 'Nombre categoria','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('state','Estado <span class="text-danger">*</span>', [], false) !!}
                                        {!! Form::select('state',['A'=>'Activa', 'I' => 'Inactiva'],null, array('id' => 'state','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;')) !!}
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
