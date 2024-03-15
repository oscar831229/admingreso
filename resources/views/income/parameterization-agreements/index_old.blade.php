@extends('layouts.belectronica.principal')

@section('css_custom')
  <style>
    /*
    Full screen Modal
    */
    .modal-dialog {
      max-width: 10000px;
    }

    .fullscreen-modal .modal-dialog {
      margin: 0;
      margin-right: auto;
      margin-left: auto;
      width: 100%;
    }
    @media (min-width: 768px) {
      .fullscreen-modal .modal-dialog {
        width: 750px;
      }
    }
    @media (min-width: 992px) {
      .fullscreen-modal .modal-dialog {
        width: 970px;
      }
    }
    @media (min-width: 1200px) {
      .fullscreen-modal .modal-dialog {
        width: 1170px;
      }
    }
  </style>
  <style>
    #morecsspure-trigger-toggle { cursor: pointer; }
    #morecsspure-element-toggle { display:none; }
    #morecsspure-element-toggle:not(:checked) ~ #morecsspure-toggled-element { display:none; }
    #morecsspure-element-toggle:not(:checked) ~ #morecsspure-trigger-toggle .morecsspure-lesslink { display:none; }
    #morecsspure-element-toggle:checked ~ #morecsspure-abstract { display:none; }
    #morecsspure-element-toggle:checked ~ #morecsspure-trigger-toggle .morecsspure-morelink { display:none; }
    #morecsspure .morecsspure-morelink, #morecsspure .morecsspure-lesslink { display: block; cursor: pointer; color:#2196f3; }
    #morecsspure .morecsspure-morelink:hover, #morecsspure .morecsspure-lesslink:hover { text-decoration:underline; }
  </style>

  <style>
    .table td {
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
  <script src="{{ asset('js/portal/income/parameterization-agreements/index.js') }}"></script>
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
            <span class="breadcrumb-item active">Parametrización convenios</span>
          </nav>
        </div><!-- br-pageheader -->
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table width='100%' style="margin-bottom: 40px;">
          <tr>
              <td width='50' align="center" valign="top" class="pr-4">
                  <h1 class="text-primary"><i class="fa fa-connectdevelop" aria-hidden="true"></i></h1>
              </td>
              <td>
                  <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Parametrización convenios</h4>
                  <span class='titulos'><?php echo date('Y-m-d h:m'); ?></span>
              </td>
          </tr>
        </table>

        <div class="row row-sm mb-4 text-center">
          <div class="col-sm-12">
            <button id="btn-new-step" class="btn btn-primary btn-sm" style="margin-bottom: 1px;">
                <i class="fa fa-plus-square-o mr-2"></i>Nuevo convenio
            </button>
          </div>
        </div>

        <table class="table table-hover" id="tbl-steps" style="width: 100% !important;">
            <thead>
                <tr>
                    <th class="search-disabled" style="width: 2%">#</th>
                    <th>Empresa</th>
                    <th>Descripcion</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>F. creacion</th>
                    <th>U. creacion</th>
                    <th>Estado</th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fullscreen-modal fade" id="md-new-step" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white bg-primary">
                <h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-connectdevelop mr-2" aria-hidden="true"></i><span id="label-type">Servicio ingreso sedes</span></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-xl-12 mg-t-20 mg-xl-t-0">
                        <div class="form-layout form-layout-5 bd-info">
                            {{ Form::open(array(
                                'id'=>'form-products',
                                'autocomplete'=>'off',
                                'onsubmit' => 'return false;'
                            )) }}
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                    {!! Form::label('name','Empresa',[],false) !!}
                                    {!! Form::select('state',[],null, array('id' => 'state','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;', 'data-live-search'=>'true')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                    {!! Form::label('code','Fecha inicial',[],false) !!}
                                    {!! Form::date('code', null, array('id' => 'code','placeholder' => 'Código producto','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                    {!! Form::label('code','Fecha final',[],false) !!}
                                    {!! Form::date('code', null, array('id' => 'code','placeholder' => 'Código producto','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                            <hr class="mt-4">
                            <h5>Servicios en convenio</h5>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                    {!! Form::label('code','Servicio ofertado',[],false) !!}
                                    {!! Form::select('state',[] , null, array('id' => 'state','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;', 'data-live-search'=>'true')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                    {!! Form::label('code','Tarifa adultos',[],false) !!}
                                    {!! Form::number('code', null, array('id' => 'code','placeholder' => 'Tarifa','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                    {!! Form::label('code','Tarifa niños',[],false) !!}
                                    {!! Form::number('code', null, array('id' => 'code','placeholder' => 'Tarifa','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                    {!! Form::label('code','Observacion',[],false) !!}
                                    {!! Form::text('code', null, array('id' => 'code','placeholder' => 'Observación tarifa','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-2" id="div-new-consumable" style="">
                                    {!! Form::label('code','&nbsp;',[],false) !!}
                                    <button class="btn btn-primary btn-block mg-b-10 btn-sm" id="btn-new-product"><i class="fa fa-shopping-cart mg-r-10"></i> Guardar</button>
                                </div>
                            </div>
                            <table class="table table-hover" id="tbl-categories" style="width: 100% !important;">
                                <thead>
                                    <tr>
                                        <th class="search-disabled" style="width: 2%">#</th>
                                        <th>Servicio ofertado</th>
                                        <th>Tarifa adultos</th>
                                        <th>Tarifa niños</th>
                                        <th>Observación</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <div class="row text-center">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button class="btn btn-success btn-sm" id="btn-save"><i class="fa fa-floppy-o mg-r-10"></i> Guardar </button>
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

@endsection
