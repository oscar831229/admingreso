@extends('layouts.belectronica.principal')

@section('css_custom')
  <style>
    /*
    Full screen Modal 
    */
    .modal-dialog {
      max-width: 10000px;
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
  <script src="{{ asset('js/portal/wallet/consecutive-tickets/index.js') }}"></script>
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
            <a class="breadcrumb-item" href="#">Tiquetera</a>
            <span class="breadcrumb-item active">Consecutivos tiquetera</span>
          </nav>
        </div><!-- br-pageheader -->
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table width='100%' style="margin-bottom: 40px;">
          <tr>
              <td width='50' align="center" valign="top" class="pr-4">
                  <h1 class="text-primary"><i class="fa fa-ticket" aria-hidden="true"></i></h1>
              </td>
              <td>
                  <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Consecutivo tiquetera</h4>
                  <span class='titulos'><?php echo date('Y-m-d h:m'); ?></span>
              </td>
          </tr>
        </table>

        <div class="row row-sm mb-4 text-center">
          <div class="col-sm-12">
            <button id="btn-new-step" class="btn btn-primary btn-sm" style="margin-bottom: 1px;">
                <i class="fa fa-plus-square-o mr-2"></i>Nuevo consecutivo
            </button>
          </div>
        </div>
        
        <table class="table table-hover" id="tbl-steps" style="width: 100% !important;">
            <thead>
                <tr>
                    <th class="search-disabled" style="width: 2%">#</th>
                    <th>Prefijo</th>
                    <th>Consecutivo inicial</th>
                    <th>Consecutivo final</th>
                    <th>Consecutivo actual</th>
                    <th>Desde el día</th>
                    <th>Hasta el dia</th>
                    <th>Fecha creación</th>
                    <th>Usuario crea</th>
                    <th>Estado</th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        {{--  Nueva secuencia  --}}
        <div class="modal fullscreen-modal fade" id="md-new-step" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document" style="width: 800px;">
            <div class="modal-content">
              <div class="modal-header text-white bg-primary">
                <h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>CONSECUTIVO TIQUETERA</h6>
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
                                  {!! Form::label('prefix','Prefijo',[],false) !!} 
                                  {!! Form::text('prefix', null, array('id' => 'prefix','class' => 'form-control form-control-sm', 'style' => 'height: 25px;','required' => 'required')) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('initial_consecutive','Consecutivo inicial <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::number('initial_consecutive', null, array('id' => 'initial_consecutive','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'required'=>'required' )) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('final_consecutive','Consecutivo final <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::number('final_consecutive', null, array('id' => 'final_consecutive','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'required'=>'required' )) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('date_from','Aplicar desde el día <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::date('date_from', null, array('id' => 'date_from','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'required'=>'required' )) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('date_to','Aplicar hasta el día <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::date('date_to', null, array('id' => 'date_to','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'required'=>'required' )) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                  {!! Form::label('observation','Descripción <span class="text-danger">*</span>',[],false) !!} 
                                  {!! Form::textarea('observation', null, ['id'=>'observation','class'=>'form-control', "rows"=>"10", "cols" => "10", "style" =>"height: 79px;"]) !!}
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-12">
                              <div class="form-group">
                                {!! Form::label('state','Estado <span class="text-danger">*</span>',[],false) !!} 
                                {!! Form::select('state', ['A'=>'Activo', 'I'=>'Inactivo'],null, array('class' => 'form-control form-control-sm','id'=>'state', 'placeholder' => 'Seleccione..', 'required'=>'required')) !!}
                              </div>
                            </div>
                          </div>
                          {{ Form::close() }}
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

      </div>
    </div>
  </div>
</div>
@endsection