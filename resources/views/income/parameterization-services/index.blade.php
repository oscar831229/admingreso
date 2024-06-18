@extends('layouts.belectronica.principal')

@section('css_custom')
    <link href="{{ asset('css/portal/style-datetable.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/lib/datatables/jquery.dataTables.css') }}" rel="stylesheet">
    <link href="{{ asset('js/plugins/bootstrap-select/bootstrap-select.css') }}" rel="stylesheet">
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
    .form-control {
        color: #010101 !important;
    }
  </style>
@endsection

@section('scripts_content')
    <script src="{{ asset('js/plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('theme/lib/internacionalizacion/es.js') }}"></script>
    <script src="{{ asset('theme/lib/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('theme/lib/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ asset('js/portal/income/parameterization-services/index.js') }}"></script>
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
            <span class="breadcrumb-item active">Servicios ingreso a sedes</span>
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
                  <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Servicios ingreso a sedes</h4>
                  <span class='titulos'>&nbsp;</span>
              </td>
          </tr>
        </table>

        <div class="row" id="div-environments">
            @foreach ($icm_environments as $icm_environment)
            <div class="col-sm-3 mt-2">
                <div class="card" style="width: 18rem;">
                    <img class="card-img-top" src="data:image/jpg;base64,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" />
                    <div class="card-body">
                      <h5 class="card-title text-center"><a href="javascript:void(0)" class="environment" data-environment_id = '{{ $icm_environment->id }}'>{{ $icm_environment->name }}</a></h5>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row" id="div-created-services" style="display:none;">
            <div class="col-sm-12">

                <div class="row row-sm mb-4 text-center">
                    <div class="col-sm-6 text-left">
                        <h4>
                            <i class="fa fa-arrow-right text-primary" aria-hidden="true"></i>
                            <span id="name-environtment"></span>
                        </h4>
                    </div>
                    <div class="col-sm-6 text-left">
                      <button id="btn-new-environment-service" class="btn btn-primary btn-sm" style="margin-bottom: 1px;">
                          <i class="fa fa-plus-square-o mr-2"></i>NUEVO SERVICIO INGRESO A SEDES
                      </button>
                    </div>
                </div>

                <table class="table table-hover" id="tbl-environment-services" style="width: 100% !important;">
                    <thead>
                        <tr>
                            <th class="search-disabled">#</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Valor</th>
                            <th>Cupos</th>
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
  </div>
</div>

<div class="modal fullscreen-modal fade" id="md-icm_environment_income_items" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <h5 class="mt-2"><i class="fa fa-server text-primary" aria-hidden="true"></i> Servicio ingreso a sedes</h5>
                            {{ Form::open(array(
                                'id'=>'form-environment-income-items',
                                'autocomplete'=>'off',
                                'onsubmit' => 'return false;'
                            )) }}
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                    {!! Form::label('name','Nombre producto <span class="text-danger">*</span>',[],false) !!}
                                    {!! Form::text('name', null, array('placeholder' => 'Nombre producto','class' => 'form-control form-control-sm uppercase','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    {!! Form::hidden('id') !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                    {!! Form::label('code','Código producto <span class="text-danger">*</span>',[],false) !!}
                                    {!! Form::text('code', null, array('placeholder' => 'Código producto','class' => 'form-control form-control-sm uppercase','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                    {!! Form::label('number_places','Número cupos <span class="text-danger">*</span>',[],false) !!}
                                    {!! Form::text('number_places', null, array('placeholder' => 'Número cupos','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        {!! Form::label('value','Venta temporada baja <span class="text-danger">*</span>', [], false) !!}
                                        {!! Form::text('value', null, array('placeholder' => 'Temporada baja','class' => 'form-control form-control-sm monto','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        {!! Form::label('value_high','Venta temporada alta <span class="text-danger">*</span>', [], false) !!}
                                        {!! Form::text('value_high', null, array('placeholder' => 'Temporada alta','class' => 'form-control form-control-sm monto','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        {!! Form::label('state','Estado <span class="text-danger">*</span>', [], false) !!}
                                        {!! Form::select('state',['A'=>'Activo', 'I' => 'Inactivo'],null, array('class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;', 'data-live-search'=>'true')) !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('observations','Observaciones', [], false) !!}
                                        {!! Form::textarea('observations', null, ['class'=>'form-control','rows' => '2']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {!! Form::label('icm_environment_icm_menu_item_id','Item liquidación <span class="text-danger">*</span>', [], false) !!}
                                        {!! Form::select('icm_environment_icm_menu_item_id', [], null, array('id' => 'icm_environment_icm_menu_item_id','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;', 'data-live-search'=>'true')) !!}
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}

                            <div id="content-parameterization-services">

                                <div class="row text-center">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-primary btn-block btn-sm" id="btn-save-income-item"><i class="fa fa-floppy-o mg-r-10"></i> Guardar </button>
                                        </div>
                                    </div>
                                </div>

                                @foreach ($rate_types as $rate_type)
                                <hr class="mt-4">
                                <h6><i class="fa fa-chevron-right text-primary" aria-hidden="true"></i> {{ $rate_type['name'] }}  <i class="{{ $rate_type['icon'] }}" aria-hidden="true"></i></h6>
                                <form class="form-tarifas-available" data-icm_rate_type_id="{{ $rate_type['id'] }}">
                                    <table class="table table-bordered" id="tbl-categories" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th rowspan="3" class="text-center" style="margin: 0px auto;">TIPO DE INGRESO</th>
                                                <th colspan="8" class="text-center">CATEGORIAS</th>
                                            </tr>

                                            <tr>
                                                @foreach ($affiliatecategories as $affiliatecategory)
                                                    <th colspan="2">{{ $affiliatecategory->name }}</th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th>Venta</th>
                                                <th>Subsidio</th>
                                                <th>Venta</th>
                                                <th>Subsidio</th>
                                                <th>Venta</th>
                                                <th>Subsidio</th>
                                                <th>Venta</th>
                                                <th>Subsidio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($income_rates as $income_rate)
                                            <tr>
                                                <th class="text-right">{{ $income_rate['type_income_name'] }}</th>
                                                @foreach ($income_rate['categories'] as $categorie)
                                                    @if ($income_rate['type_income_name'] == 'AFILIADO')
                                                        @php
                                                            $colspan = 1;
                                                        @endphp
                                                    @else
                                                        @php
                                                            $colspan = 2;
                                                        @endphp
                                                    @endif
                                                    <td colspan="{{ $colspan }}">
                                                        <input @php echo $categorie['control'] ? '' : 'readonly';   @endphp placeholder="Venta {{ strtoupper($categorie['affiliatecategory']->code ) }}" data-type="{{ $income_rate['type_income_id'] }}" data-category_id = "{{ $categorie['affiliatecategory']->id }}" data-icm_rate_type_id = "{{ $rate_type['id'] }}" class="form-control form-control-sm monto rate  valor_venta" style="height: 25px;" value="">
                                                    </td>
                                                    @if ($income_rate['type_income_name'] == 'AFILIADO')
                                                    <td>
                                                        <input @php echo $categorie['control'] ? '' : 'readonly';   @endphp placeholder="Subsidio {{ strtoupper($categorie['affiliatecategory']->code ) }}" data-type="{{ $income_rate['type_income_id'] }}" data-category_id = "{{ $categorie['affiliatecategory']->id }}" data-icm_rate_type_id = "{{ $rate_type['id'] }}" class="form-control form-control-sm monto subsidio_venta" style="height: 25px;" value="">
                                                    </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </form>
                                @endforeach

                            </div> <!-- end content parameterization services -->

                            <div id="content-environments" style="display: none;">
                                <hr>
                                <h5 class="mt-2"><i class="fa fa-server text-primary" aria-hidden="true"></i> Ambientes habilitados para venta</h5>
                                <div class="row text-center">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-primary btn-block btn-sm" id="btn-update-income-item"><i class="fa fa-pencil-square mg-r-10"></i> Habilitar ventas </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-hover width60" id="tabla-data" style="font-size: 12px;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5% !important;">ID</th>
                                                    <th class="width20">Nombre</th>
                                                    <th style="width: 15% !important;">Estado ambiente</th>
                                                    <th style="width: 10% !important;">Habilitado</th>
                                                    <th class="width20">Servicio liquida</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                              @foreach ($icm_environments as $key => $icm_environment)
                                                <tr>
                                                    <td>{{$icm_environment->id}}</td>
                                                    <td>{{$icm_environment->name}}</td>
                                                    <td>{!! $icm_environment->state == 'A' ? '<span class="badge badge-success">Activa</span>' : '<span class="badge badge-secondary">Inactiva</span>' !!}</td>
                                                    <td>
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" data-environment_id="{{ $icm_environment->id }}"> Inactivo
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {!! Form::select('icm_environment_icm_menu_item_id', $icm_environment->getPluckItems() , null, array('id' => 'icm_environment_icm_menu_item_id','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;', 'data-environment_id' => $icm_environment->id )) !!}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div> <!-- end content-environments -->
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
