@extends('layouts.belectronica.principal')

@section('css_custom')
  <link href="{{ asset('css/portal/style-datetable.css') }}" rel="stylesheet">
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

      #md-procces-donor .modal-body {
        overflow-y: auto;
        height: 604px;
      }

    }
    @media (min-width: 992px) {

      .fullscreen-modal .modal-dialog {
        width: 970px;
      }

      #md-procces-donor .modal-body {
        height: 604px;
        overflow-y: auto;
      }

    }
    @media (min-width: 1200px) {

      .fullscreen-modal .modal-dialog {
        width: 1170px;
      }

      #md-procces-donor .modal-body {
        height: 604px;
        overflow-y: auto;
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
  <link href="{{ asset('js/plugins/bootstrap-select/bootstrap-select.css') }}" rel="stylesheet">
  <link href="{{ asset('js/plugins/bootstrap-fileinput/css/fileinput.min.css') }}" rel="stylesheet">
  <style>
    .nav-pills-custom .nav-link {
      color: #aaa;
      background: #fff;
      position: relative;
    }
    
    .nav-pills-custom .nav-link.active {
        color: #45b649;
        background: #fff;
    }

    /* Add indicator arrow for the active tab */
    @media (min-width: 992px) {
      .nav-pills-custom .nav-link::before {
          content: '';
          display: block;
          border-top: 8px solid transparent;
          border-left: 10px solid #fff;
          border-bottom: 8px solid transparent;
          position: absolute;
          top: 50%;
          right: -10px;
          transform: translateY(-50%);
          opacity: 0;
      }
    }

    .nav-pills-custom .nav-link.active::before {
      opacity: 1;
    }
    .note-editor .note-editable {
      line-height: 1.2;
      font-size: 18px;
    }
    #tbl-tracking td p, #tbl-tracking td h6 {
      font-size: 12px !important;
    }
  </style>
@endsection

@section('scripts_content')
  <script src="{{ asset('theme/lib/internacionalizacion/es.js') }}"></script>
  <script src="{{ asset('theme/lib/datatables/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('js/plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>

  <!-- include summernote css/js -->
  <link href="{{ asset('theme/lib/summernote/summernote.min.css') }}" rel="stylesheet">
  <script src="{{ asset('theme/lib/summernote/summernote.min.js') }}"></script>
  <script src="{{ asset('theme/lib/summernote/lang/summernote-es-ES.min.js') }}"></script>

  <script src="{{ asset('js/portal/wallet/transactions/index.js') }}"></script>
  <script src="{{ asset('js/plugins/bootstrap-fileinput/js/fileinput.min.js') }}"></script>
  <script src="{{ asset('js/plugins/bootstrap-fileinput/js/locales/es.js') }}"></script>
@endsection


@section('content')
  <div class="br-mainpanel">

    @include('includes/mensaje')
    @include('includes/form-error')

    <div class="br-pageheader pd-y-15 pd-l-20">
      <nav class="breadcrumb pd-0 mg-0 tx-12">
        <a class="breadcrumb-item" href="#">Billetera</a>
        <span class="breadcrumb-item active">Transacciones</span>
      </nav>
    </div><!-- br-pageheader -->

    <div class="br-pagebody" style="padding: 0 10px;">
      <div class="br-section-wrapper">

        <table width='100%' style="margin-bottom: 40px;">
          <tr>
              <td width='50' align="center" valign="top" class="pr-4">
                  <h1 class="text-primary"><i class="fa fa-bell-o" aria-hidden="true"></i></h1>
              </td>
              <td>
                  <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Transacciones</h4>
                  <span class='titulos'><?php echo date('Y-m-d h:m'); ?></span>
              </td>
          </tr>
        </table>

        <div class="card-body table-responsive p-0">
          <div class="mb-4">
            
            {!! Form::open(['id'=>'form-transaction']) !!}
            
            <div class="row">
              <div class="col-sm-2">
                <div class="form-group">
                  <label for="from_date">Fecha inicial</label>
                  {!! Form::date('from_date', date('Y-m-d'), array('class' => 'form-control form-control-sm','id'=>'from_date', 'placeholder' => 'Seleccione..', 'style' => 'height: 25px;', 'required'=>'required', 'data-live-search' => 'true')) !!}
                </div>
              </div>
              <div class="col-sm-2">
                <div class="form-group">
                  <label for="to_date">Fecha final</label> 
                  {!! Form::date('to_date', date('Y-m-d'), array('class' => 'form-control form-control-sm','id'=>'to_date', 'placeholder' => 'Seleccione..', 'style' => 'height: 25px;', 'required'=>'required', 'data-live-search' => 'true')) !!}
                </div>
              </div>
              <div class="col-sm-2">
                <div class="form-group">
                  <label for="">&nbsp;</label> 
                  <a href="javascript:void(0)" style="height: 25px;" id="btn-refresh" class="btn btn-block btn-outline-secondary btn-sm"><i class="fa fa-refresh mr-2" aria-hidden="true"></i>refrescar</a>
                </div>
              </div>
            </div>
            {!! Form::close() !!}

          </div>
          <table class="table table-hover" id="tbl-alert-donor" style="width: 100% !important;">
              <thead>
                  <tr>
                    <th class="search-disabled" style="width: 2%">#</th>
                    <th style="width: 5%">Identificación</th>
                    <th>Donante</th>
                    <th>F. ingreso</th>
                    <th>Municipio</th>
                    <th style="width: 10%">IPS</th>
                    <th style="width: 10%">Usuario registro</th>
                    <th class="text-center search-disabled" style="width: 10%">TODAS</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
          </table>
        </div>
        
      </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->
  </div>

  {{--  Nueva secuencia  --}}
  <div class="modal fullscreen-modal fade" id="md-new-step" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 7x0%">
      <div class="modal-content">
        <div class="modal-header text-white bg-primary">
          <h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-braille mr-2" aria-hidden="true"></i>NUEVA ALERTA POSIBLE DONANTE</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" >
          <div class="row">
            <div class="col-sm-12">
              <div class="card card-body">
              <div class="row">
                <div class="col-xl-12">
                    {{ Form::open(array(
                      'id'=>'form-new-step',
                      'method' => 'POST',
                      'autocomplete'=>'off', 
                      'onsubmit' => 'return false;'
                    )) }}
                    {!! Form::hidden('id', null, ['id'=>'id']) !!}
                    <h5 class="card-title" style="font-weight: 500;"><i class="fa fa-user-o mr-4" aria-hidden="true"></i>Datos paciente</h5>
                    <div class="row">
                      <div class="col-sm-3">
                        <div class="form-group">
                          {!! Form::label('document_type_id','Tipo documento <span class="tx-danger">*</span>',[],false) !!} 
                          {!! Form::select('document_type_id', [], null, array('class' => 'form-control form-control-sm','id'=>'document_type_id', 'style' => 'height: 25px;', 'placeholder' => 'Seleccione..', 'required'=>'required')) !!}
                         </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          {!! Form::label('document_number','Número documento <span class="tx-danger">*</span>',[],false) !!} 
                          {!! Form::text('document_number', null, array('id' => 'document_number','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'required'=>'required' )) !!}
                         </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                          {!! Form::label('name','Nombre paciente <span class="tx-danger">*</span>',[],false) !!} 
                          {!! Form::text('name', null, array('id' => 'name','class' => 'form-control form-control-sm uppercase','required' => 'required', 'style' => 'height: 25px;', 'required'=>'required' )) !!}
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-3">
                        <div class="form-group">
                          {!! Form::label('gender_id','Genero <span class="tx-danger">*</span>',[],false) !!} 
                          {!! Form::select('gender_id', [], null, array('class' => 'form-control form-control-sm', 'style' => 'height: 25px;','id'=>'gender_id', 'placeholder' => 'Seleccione..', 'required'=>'required')) !!}
                         </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          {!! Form::label('birth_date','Fecha nacimiento <span class="tx-danger">*</span>',[],false) !!} 
                          {!! Form::date('birth_date', null, array('id' => 'birth_date','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'required'=>'required' )) !!}
                        </div>
                      </div>
                    </div>
                    <h5 class="card-title mt-4" style="font-weight: 500;"><i class="fa fa-hospital-o mr-4" aria-hidden="true"></i>Historia clinica</h5>
                    <div class="row">
                      
                      <div class="col-sm-2">
                        <div class="form-group">
                          {!! Form::label('admission_date','Fecha ingreso <span class="tx-danger">*</span>',[],false) !!} 
                          {!! Form::date('admission_date', null, array('id' => 'admission_date','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'required'=>'required' )) !!}
                        </div>
                      </div>
                      <div class="col-sm-5">
                        <div class="form-group">
                          {!! Form::label('pda_health_provider_unit_id','IPS <span class="tx-danger">*</span>',[],false) !!} 
                          {!! Form::select('pda_health_provider_unit_id', [], null, array('class' => 'form-control form-control-sm','id'=>'pda_health_provider_unit_id', 'style' => 'height: 25px;' ,'placeholder' => 'Seleccione..', 'required'=>'required', 'data-live-search' => 'true')) !!}
                         </div>
                      </div>
                      <div class="col-sm-5">
                        <div class="form-group">
                          {!! Form::label('city_reports_alert_id','Municipio alerta <span class="tx-danger">*</span>',[],false) !!} 
                          {!! Form::select('city_reports_alert_id', [], null, array('class' => 'form-control form-control-sm','id'=>'city_reports_alert_id', 'placeholder' => 'Seleccione..', 'style' => 'height: 25px;', 'required'=>'required', 'data-live-search' => 'true')) !!}
                         </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12">
                        <div class="form-group">
                          {!! Form::label('medical_evolution','Evolución <span class="tx-danger">*</span>',[],false) !!} 
                          {!! Form::textarea('medical_evolution', null, array('id' => 'medical_evolution', 'rows' => '4', 'class' => 'form-control form-control-sm', 'required'=>'required')) !!}
                         </div>
                      </div>
                    </div>

                    {{ Form::close() }}
                </div>
              </div>
              </div>
            </div>
          </div> 
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary btn-sm" id='btn-save-step'><i class="fa fa-floppy-o mg-r-10"></i> Guardar </button>
        </div>
      </div>
    </div>
  </div>
  {{--  Fin nueva programación  --}}

  {{--  Nueva secuencia  --}}
  <div class="modal fullscreen-modal fade" id="md-procces-donor" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 7x0%">
      <div class="modal-content">
        <div class="modal-header text-white bg-primary">
          <h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-bell-o mr-2" aria-hidden="true"></i>PROCESO POSIBLE DONANTE</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" >
          <!-- Demo header-->
          <section class="header">
            <div class="container py-2">

              <div class="row">
                  <div class="col-md-3">
                    <!-- Tabs nav -->
                    <div class="nav flex-column nav-pills nav-pills-custom" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                      <a class="nav-link mb-3 p-3 shadow active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">
                        <i class="fa fa-user-circle-o mr-2"></i>
                        <span class="font-weight-bold small text-uppercase">Información donante</span>
                      </a>
                      <a class="nav-link mb-3 p-3 shadow" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">
                        <i class="fa fa-calendar-minus-o mr-2"></i>
                        <span class="font-weight-bold small text-uppercase">Seguimiento</span>
                      </a>
                      <a class="nav-link mb-3 p-3 shadow" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">
                        <i class="fa fa-file mr-2"></i>
                        <span class="font-weight-bold small text-uppercase">Documentos</span>
                      </a>
                      {{--  <a class="nav-link mb-3 p-3 shadow" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                        <i class="fa fa-money mr-2"></i>
                        <span class="font-weight-bold small text-uppercase">Facturación</span>
                      </a>  --}}
                    </div>
                  </div>
                  <div class="col-md-9">
                    <!-- Tabs content -->
                      <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade shadow rounded bg-white show active p-2" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">

                          {{--  Datos registro  --}}
                          <h6 class="mb-4"><i class="fa fa-database mr-2 text-primary"></i>Quien registra la alerta</h6>
                          <div class="form-layout form-layout-2">
                            <div class="row no-gutters">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-control-label font-weight-bold">Usuario registra: <span class="tx-danger">*</span></label>
                                  <span class="form-control" data-name="user_register_name"></span>
                                </div>
                              </div><!-- col-4 -->
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-control-label font-weight-bold">Fecha registro: <span class="tx-danger">*</span></label>
                                  <span class="form-control" data-name="created_at"></span>
                                </div>
                              </div><!-- col-4 -->
                            </div><!-- row -->
                          </div>  
                          {{--  fin datos registro  --}}

                          {{--  informacion donante  --}}
                          <h6 class="mb-4 mt-4"><i class="fa fa-user-circle-o mr-2 text-success"></i>Información donante</h6>
                          <div class="form-layout form-layout-2">
                            <div class="row no-gutters">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-control-label">Tipo de documento: <span class="tx-danger">*</span></label>
                                  <span class="form-control" data-name="type_document"></span>
                                </div>
                              </div><!-- col-4 -->
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-control-label">Número de documento: <span class="tx-danger">*</span></label>
                                  <span class="form-control" data-name="document_number"></span>
                                </div>
                              </div><!-- col-4 -->
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label class="form-control-label">Nombre posible donante: <span class="tx-danger">*</span></label>
                                  <span class="form-control" data-name="name"></span>
                                </div>
                              </div><!-- col-4 -->
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-control-label">Fecha nacimiento: <span class="tx-danger">*</span></label>
                                  <span class="form-control" data-name="birth_date"></span>
                                </div>
                              </div><!-- col-4 -->
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-control-label">Genero: <span class="tx-danger">*</span></label>
                                  <span class="form-control" data-name="gender"></span>
                                </div>
                              </div><!-- col-4 -->
                            </div><!-- row -->
                          </div>  
                          {{--  fin informacion donante  --}}


                          {{--  informacion donante  --}}
                          <h6 class="mb-4 mt-4"><i class="fa fa-user-circle-o mr-2 text-success"></i>Historia clinica</h6>
                          <div class="form-layout form-layout-2">
                            <div class="row no-gutters">
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label class="form-control-label">Fecha ingreso: <span class="tx-danger">*</span></label>
                                  <span class="form-control" data-name="admission_date"></span>
                                </div>
                              </div><!-- col-4 -->
                              <div class="col-md-5">
                                <div class="form-group">
                                  <label class="form-control-label">IPS: <span class="tx-danger">*</span></label>
                                  <span class="form-control" data-name="pda_health_provider_unit"></span>
                                </div>
                              </div><!-- col-4 -->
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label class="form-control-label">Municipio alerta: <span class="tx-danger">*</span></label>
                                  <span class="form-control" data-name="city_reports_alert"></span>
                                </div>
                              </div><!-- col-4 -->
                              <div class="col-md-12 mg-t--1 mg-md-t-0">
                                <div class="form-group mg-md-l--1">
                                  <label class="form-control-label">Evolucion: <span class="tx-danger">*</span></label>
                                  <span class="form-control" data-name="medical_evolution"></span>
                                </div>
                              </div><!-- col-4 -->
                            </div><!-- row -->
                          </div>  
                          {{--  fin informacion donante  --}}

                        </div>
                              
                        <div class="tab-pane fade shadow rounded bg-white p-2" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                            <h6 class="mb-4"><i class="fa fa-calendar-minus-o mr-2 text-success"></i>Seguimientos
                              <a href="javascript:void(0)" id="btn-new-tracking" class="tooltipsC text-warning" title="Registrar nuevo seguimiento"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
                            </h6>
                            <div class="card card-body" id="div-form-trackin" style="display:none;">
                              <div class="row">
                                <div class="col-xl-12">
                                    {{ Form::open(array(
                                      'id'=>'form-record-tracking',
                                      'method' => 'POST',
                                      'autocomplete'=>'off', 
                                      'onsubmit' => 'return false;'
                                    )) }}
                                      <div class="row">
                                        <div class="col-sm-7">
                                          <div class="form-group">
                                            {!! Form::label('step_id','Paso proceso <span class="tx-danger">*</span>',[],false) !!} 
                                            <select class="form-control form-control-sm" id="step_id" style="height: 25px;" required="required" data-live-search="true" name="step_id" aria-describedby="step_id-error" aria-invalid="false">
                                              <option value="">Seleccione..</option>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="col-sm-5" style="display:none;" id="div-cause-non-donation">
                                          <div class="form-group">
                                            {!! Form::label('pda_cause_non_donation_id','Causal de no donación <span class="tx-danger">*</span>',[],false) !!} 
                                            {!! Form::select('pda_cause_non_donation_id', [], null, array('class' => 'form-control form-control-sm','id'=>'pda_cause_non_donation_id', 'placeholder' => 'Seleccione..', 'style' => 'height: 25px;', 'data-live-search' => 'true')) !!}
                                          </div>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="col-sm-12">
                                          <div class="form-group">
                                            {!! Form::label('description','Evolución <span class="tx-danger">*</span>',[],false) !!} 
                                            {!! Form::textarea('description', null, array('id' => 'description', 'rows' => '4', 'class' => 'form-control form-control-sm', 'required'=>'required')) !!}
                                          </div>
                                        </div>
                                      </div>
                                    {{ Form::close() }}
                                    <div class="row mb-4">
                                      <div class="col-sm-12 text-center">
                                        <button class="btn btn-danger btn-sm mr-4" id='btn-cancel-tracking'><i class="fa fa-ban mg-r-10"></i> Cancelar </button>
                                        <button class="btn btn-primary btn-sm" id='btn-save-tracking'><i class="fa fa-floppy-o mg-r-10"></i> Guardar </button>
                                      </div>
                                    </div>
                                </div>
                              </div>
                            </div>
                            <div class="bd rounded table-responsive" id="div-detail-trackin">
                              <table class="table table-bordered mg-b-0" id="tbl-tracking">
                                <thead>
                                  <tr>
                                    <th>ID</th>
                                    <th>Paso</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                  </tr>
                                </thead>
                                <tbody>
                                </tbody>
                              </table>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade shadow rounded bg-white p-2" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                          <h6 class="mb-4"><i class="fa fa-file mr-2 text-success"></i>Documentos</h6>
                          <div class="row mb-4">
                            <div class="col-xl-12">
                                {{ Form::open(array(
                                  'id'=>'form-record-tracking',
                                  'method' => 'POST',
                                  'autocomplete'=>'off', 
                                  'onsubmit' => 'return false;'
                                )) }}
                                <div class="row">
                                  <div class="col-sm-6">
                                    {!! Form::text('original_name', null,['class'=>'form-control input-date', 'placeholder'=>'Nombre del documento', 'id'=>'original_name']) !!}
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="file-loading ml-4 mb-4">
                                      <input id="file-es" name="file" type="file" multiple>
                                    </div>
                                  </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                          </div>
                          <div class="bd rounded table-responsive">
                            <table class="table table-bordered mg-b-0" id="tbl-donor-documentations">
                              <thead>
                                <tr>
                                  <th>ID</th>
                                  <th>Nombre documento</th>
                                  <th>usuario</th>
                                  <th>Fecha registran</th>
                                  <th></th>
                                </tr>
                              </thead>
                              <tbody>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        
                        <div class="tab-pane fade shadow rounded bg-white p-2" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                          <h6 class="mb-4"><i class="fa fa-money mr-2 text-success"></i>Facturación</h6>
                          <div class="bd rounded table-responsive">
                            <table class="table table-bordered mg-b-0">
                              <thead>
                                <tr>
                                  <th>ID</th>
                                  <th>Numero factura</th>
                                  <th>Usuario</th>
                                  <th>Fecha</th>
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
          </section>
        </div>
        <div class="modal-footer">
        </div>
      </div>
    </div>
  </div>
  {{--  Fin nueva programación  --}}



@endsection