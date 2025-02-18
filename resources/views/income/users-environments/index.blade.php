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
  <link href="{{ asset('js/plugins/jquery.autocomplete/css/autocomplete.css') }}" rel="stylesheet">
@endsection

@section('scripts_content')
    <script src= "{{ asset('js/plugins/jquery.autocomplete/js/jquery.autocomplete.min.js') }}"></script>
    <script src="{{ asset('js/portal/income/users-environments/index.js') }}"></script>
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
            <span class="breadcrumb-item active">Usuarios - ambientes</span>
          </nav>
        </div><!-- br-pageheader -->
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table width='100%' style="margin-bottom: 40px;">
          <tr>
              <td width='50' align="center" valign="top" class="pr-4">
                  <h1 class="text-primary"><i class="fa fa-building-o" aria-hidden="true"></i></h1>
              </td>
              <td>
                  <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Usuarios - ambientes</h4>
                  <span class='titulos'>&nbsp;</span>
              </td>
          </tr>
        </table>

        <div class="row">
            <div class="col-sm-12">
                <div class="row mb-4">
                    <div class="col-xl-12 mg-t-20 mg-xl-t-0">
                        <div>
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

                        <div class="br-section-wrapper">

                            <table class="table width60" id="tabla-data">
                                <thead>
                                    <tr>
                                        <th class="width20">ID</th>
                                        <th class="width30">Usuario</th>
                                        <th class="width30">Estado</th>
                                        <th class="width30">Nombre</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id='id'></td>
                                        <td id='login'></td>
                                        <td id='estado'></td>
                                        <td id='name'></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="card card-success">
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-striped table-bordered table-hover" id="tblentities">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Salon</th>
                                                <th class="text-center">Habilitar</th>
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
  </div>
</div>

<div class="modal fullscreen-modal fade" id="md-new-step" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white bg-primary">
                <h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-file mr-2" aria-hidden="true"></i><span id="label-type">Servicio ingreso sedes</span></h6>
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
                                    {!! Form::label('name','Nombre producto',[],false) !!}
                                    {!! Form::text('name', null, array('id' => 'name','placeholder' => 'Nombre producto','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    {!! Form::hidden('id') !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                    {!! Form::label('code','Código producto',[],false) !!}
                                    {!! Form::text('code', null, array('id' => 'code','placeholder' => 'Código producto','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                    {!! Form::label('invima_registration','Número cupos',[],false) !!}
                                    {!! Form::text('invima_registration', null, array('id' => '','placeholder' => 'Número cupos','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        {!! Form::label('icm_brand_id','Tarifa', [], false) !!}
                                        {!! Form::text('invima_registration', null, array('id' => '','placeholder' => 'Tarifa','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        {!! Form::label('state','Estado', [], false) !!}
                                        {!! Form::select('state',['A'=>'Activo', 'I' => 'Inactivo'],null, array('id' => 'state','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;', 'data-live-search'=>'true')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        {!! Form::label('comments','Observaciones', [], false) !!}
                                        {!! Form::textarea('comments', null, ['class'=>'form-control','rows' => '2','id'=>'comments']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {!! Form::label('state','Servicio factura', [], false) !!}
                                        {!! Form::select('state',['A'=>'Activo', 'I' => 'Inactivo'],null, array('id' => 'state','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;', 'data-live-search'=>'true')) !!}
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                            <hr class="mt-4">
                            <h5>Tarifas por categoria</h5>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                    {!! Form::label('code','Tipo ingreso',[],false) !!}
                                    {!! Form::select('state',['A'=>'Afilaido', 'B' => 'Presentado'], null, array('id' => 'state','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;', 'data-live-search'=>'true')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                    {!! Form::label('code','Cátegoria',[],false) !!}
                                    {!! Form::select('state',['A'=>'Categoria A', 'B' => 'Categoria B', 'C' => 'Categoria C', 'D' => 'Categoria D'], null, array('id' => 'state','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;', 'data-live-search'=>'true')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                    {!! Form::label('code','Tarifa',[],false) !!}
                                    {!! Form::number('code', null, array('id' => 'code','placeholder' => 'Tarifa','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                    {!! Form::label('code','Aplica fidelidad',[],false) !!}
                                    {!! Form::select('state',['S'=>'Aplica', 'N' => 'Sin fidelidad'], null, array('id' => 'state','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;', 'data-live-search'=>'true')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-41">
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
                                        <th>Categoria</th>
                                        <th>Tarifa</th>
                                        <th>Tarifa fidelidad</th>
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
