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

      #md-view-detail-transaction .modal-body {
        overflow-y: auto;
        height: 604px;
      }

    }
    @media (min-width: 992px) {

      .fullscreen-modal .modal-dialog {
        width: 970px;
      }

      #md-view-detail-transaction .modal-body {
        height: 604px;
        overflow-y: auto;
      }

    }
    @media (min-width: 1200px) {

      .fullscreen-modal .modal-dialog {
        width: 1170px;
      }

      #md-view-detail-transaction .modal-body {
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
        <a class="breadcrumb-item" href="#">Tiquetera</a>
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
                  {!! Form::label('store_id', 'Comercio', ['class' => 'col-md-4 control-label']) !!}
                  {!! Form::select('store_id', $stores, null, array('class' => 'form-control form-control-sm','id'=>'store_id', 'style' => 'height: 25px;', 'placeholder' => '[ Todos ]')) !!}
                </div>
              </div>
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
                  {!! Form::label('movement_type_id', 'Movimiento', ['class' => 'col-md-4 control-label']) !!}
                  {!! Form::select('movement_type_id', $movement_types, null, array('class' => 'form-control form-control-sm','id'=>'movement_type_id', 'style' => 'height: 25px;', 'placeholder' => '[ Todos ]')) !!}
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
                    <th>Bolsillo</th>
                    <th>Dcumento</th>
                    <th>Cliente</th>
                    <th>Movimiento</th>
                    <th>Valor</th>
                    <th>Usuario</th>
                    <th>Comercio</th>
                    <th>CUS</th>
                    <th>Fecha Mov</th>
                    <th class="search-disabled"></th>
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
        <div class="modal fullscreen-modal fade" id="md-view-detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document" style="width: 800px;">
            <div class="modal-content">
              <div class="modal-header text-white bg-primary">
                <h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-bell-o mr-2" aria-hidden="true"></i>Detalle transacción</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body" >
                <div class="row">
                  <div class="col-sm-12">
                    <div class="row">
                      <div class="col-xl-12">
                          {{ Form::open(array(
                            'id'=>'form-new-step',
                            'method' => 'POST',
                            'autocomplete'=>'off', 
                            'onsubmit' => 'return false;'
                          )) }}
                          {!! Form::hidden('id', null, ['id'=>'id']) !!}
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('electrical_pocket_name','Bolsillo <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::text('electrical_pocket_name', null, array('id' => 'electrical_pocket_name','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'readonly' => 'readonly' )) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('customer','Cliente <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::text('customer', null, array('id' => 'customer','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'readonly' => 'readonly' )) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('cus','CUS <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::text('cus', null, array('id' => 'cus','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'readonly' => 'readonly' )) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('value','Valor <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::text('value', null, array('id' => 'value','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'readonly' => 'readonly' )) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('type_operation','Tipo operación <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::text('type_operation', null, array('id' => 'type_operation','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'readonly' => 'readonly' )) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('store_name','Comercio <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::text('store_name', null, array('id' => 'store_name','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'readonly' => 'readonly' )) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('user_code','Usuario comercio <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::text('user_code', null, array('id' => 'user_code','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'readonly' => 'readonly' )) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('transaction_document_number','Documento origen <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::text('transaction_document_number', null, array('id' => 'transaction_document_number','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'readonly' => 'readonly' )) !!}
                                </div>
                            </div>
                          </div>
                          {{ Form::close() }}
                      </div>
                      <div class="col-sm-12 mt-4">
                        <h6 class="text-primary"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Tickets vinculados en el movimiento</h6>
                        <table class="table" id="tbl-tickets">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Número ticket</th>
                              <th>Valor</th>
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
              <div class="modal-footer">
              </div>
            </div>
          </div>
        </div>
        {{--  Fin nueva programación  --}}
@endsection