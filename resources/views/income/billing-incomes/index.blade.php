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
  <script src="{{ asset('js/portal/income/billing-incomes/index.js') }}"></script>
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
            <span class="breadcrumb-item active">Facturación ingresos</span>
          </nav>
        </div><!-- br-pageheader -->
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table width='100%' style="margin-bottom: 20px;">
          <tr>
              <td width='50' align="center" valign="top" class="pr-4">
                  <h1 class="text-primary"><i class="fa fa-money" aria-hidden="true"></i></h1>
              </td>
              <td>
                  <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Facturación ingresos</h4>
                  <span class='titulos'><?php echo date('Y-m-d h:m'); ?></span>
              </td>
          </tr>



        </table>
            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="code">Servicio ingreso</label>
                        <select id="state" class="form-control form-control-sm" required="required" style="height: 25px;" data-live-search="true" name="state"><option selected="selected" value="">Seleccione..</option></select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="code">Cupos</label>
                        {!! Form::text('code', null, array('id' => 'code','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                    </div>
                </div>
                <div class="col-sm-12">

                <hr class="mt-4">
                <div class="form-layout form-layout-5 bd-info">
                    {{ Form::open(array(
                        'id'=>'form-products',
                        'autocomplete'=>'off',
                        'onsubmit' => 'return false;'
                    )) }}
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                            {!! Form::label('name','Tipo documento',[],false) !!}
                            {!! Form::select('state',['C' => 'Cédula de ciudadania','T' => 'Tarjeta de identidad','P' => 'Pasaporte','N' => 'Permisos especial',],null, array('id' => 'state','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;', 'data-live-search'=>'true')) !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                            {!! Form::label('name','Número documento',[],false) !!}
                            {!! Form::text('code', null, array('id' => 'code','placeholder' => 'Número documento','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                            {!! Form::label('name','Nombre empresa',[],false) !!}
                            {!! Form::text('code', null, array('id' => 'code','placeholder' => 'Nombre cliente','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                            {!! Form::label('code','Teléfono',[],false) !!}
                            {!! Form::text('code', null, array('id' => 'code','placeholder' => 'Teléfono','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                            {!! Form::label('code','Dirección',[],false) !!}
                            {!! Form::text('code', null, array('id' => 'code','placeholder' => 'Dirección','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                            {!! Form::label('code','Email',[],false) !!}
                            {!! Form::text('code', null, array('id' => 'code','placeholder' => 'Email','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                    <div class="row text-center">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <button class="btn btn-success btn-sm  btn-block" id="btn-save"><i class="fa fa-floppy-o mg-r-10"></i> Ingresar  </button>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <button class="btn btn-warning btn-sm  btn-block" id="btn-save"><i class="fa fa-credit-card-alt mg-r-10"></i> Lector de cedula  </button>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <button class="btn btn-primary btn-sm  btn-block" id="btn-save"><i class="fa fa-money mg-r-10"></i> Pagar  </button>
                            </div>
                        </div>
                    </div>
                    <br>
                    <h6>Personas coberturas</h6>
                    <table class="table table-hover" id="tbl-categories" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th class="search-disabled" style="width: 2%">#</th>
                                <th>Tipo documento</th>
                                <th>Número documento</th>
                                <th>Nombre</th>
                                <th>Documento empresa</th>
                                <th>Nombre empresa</th>
                                <th>Tipo ingreso</th>
                                <th>Categoria</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <br>
                    <h5>Servicios liquidados</h5>
                    <table class="table table-hover" id="tbl-categories" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th class="search-disabled" style="width: 2%">#</th>
                                <th>Nombre servicio</th>
                                <th>Valor</th>
                                <th>Iva</th>
                                <th>Impoconsumo</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-right">Subtotal</th>
                                <th>$0.00</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-right">Iva</th>
                                <th>$0.00</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-right">Impoconsumo</th>
                                <th>$0.00</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-right">Total</th>
                                <th>$0.00</th>
                            </tr>
                        </tfoot>
                        <tbody></tbody>
                    </table>
                </div><!-- form-layout -->
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

                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

@endsection
