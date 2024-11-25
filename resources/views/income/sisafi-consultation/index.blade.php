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
@endsection

@section('scripts_content')
    <script src="{{ asset('theme/lib/internacionalizacion/es.js') }}"></script>
    <script src="{{ asset('theme/lib/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/portal/income/sisafi-consultation/index.js') }}"></script>
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
              <span class="breadcrumb-item active">Consultar información SISAFI</span>
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
                            <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Consultar información SISAFI</h4>
                            <span class='titulos'>&nbsp;</span>
                        </td>
                    </tr>
                </table>

                <div class="row">
                    <div class="col-sm-12">
                      <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                {!! Form::label('identificacion','<i class="fa fa-play-circle text-success" aria-hidden="true"></i> Número documento beneficiario', [], false) !!}
                                {!! Form::text('identificacion',  null, ['class' => 'form-control form-control-sm', 'id' => 'identificacion', 'placeholder' => 'Número documento', 'style'=>'height: 25px;']) !!}
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                {!! Form::label('id_principal','<i class="fa fa-play-circle text-success" aria-hidden="true"></i> Número documento beneficiario', [], false) !!}
                                {!! Form::text('id_principal',  null, ['class' => 'form-control form-control-sm', 'id' => 'id_principal', 'placeholder' => 'Número documento trabajador', 'style'=>'height: 25px;']) !!}
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <br>
                                <button class="btn btn-primary btn-sm mr-3 btn-block" id="btn-refresh-liquidation"><i class="fa fa-search" aria-hidden="true"></i> Buscar </button>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <br>
                                <button class="btn btn-success btn-sm btn-block" id="btn-procesar-sync"><i class="fa fa-tasks" aria-hidden="true"></i> Sincronizar datos</button>
                            </div>
                        </div>
                      </div>
                    </div>
                </div>

                <div class="row text-center mt-2">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="rounded table-responsive">
                                <table class="table table-bordered dataTable" style="width: 99%;" id="tbl-consultations">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>T.D.B</th>
                                            <th>Documento B.</th>
                                            <th>Nombre Beneficiario</th>
                                            <th>T.D.T</th>
                                            <th>Documento T.</th>
                                            <th>Nombre Trabajador</th>
                                            <th>Nit</th>
                                            <th>Razon social</th>
                                            <th>Categoria Ingresos</th>
                                            <th>Categoria SISAFI</th>
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


    {{--  Modal detalle de liquidación --}}
    <div class="modal fullscreen-modal fade show" id="md-liquidation-invoice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" >
            <div class="modal-content">
                <div class="modal-header text-white bg-primary">
                    <h6 class="modal-title" id="exampleModalLabel"><i class="mdi mdi-store"></i><span id="label-type">Liquidación</span></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 text-left">
                            <h4><i class="fa fa-calculator text-primary mr-2" aria-hidden="true"></i> LIQUIDACIÓN: <span id="number-liquidation" style="font-weight: 900; color: #11239c;"></span></h4>
                        </div>
                        <div class="col-sm-6 text-left">
                            <h4><i class="fa fa-money text-primary mr-2" aria-hidden="true"></i> FACTURA: <span id="number-invoice" style="font-weight: 900; color: #11239c;"></span></h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <h6><i class="fa fa-server text-primary" aria-hidden="true"></i> SERVICIO LIQUIDADOS</h6>
                            <table class="table table-hover" id="tbl-details" style="width: 100% !important;">
                                <thead>
                                    <tr>
                                        <th class="search-disabled" style="width: 5%">#</th>
                                        <th>Nombre servicio</th>
                                        <th>Código Tarifa</th>
                                        <th>Subsidio</th>
                                        <th>Valor</th>
                                        <th>Iva</th>
                                        <th>Impoconsumo</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-right">Subtotal</th>
                                        <th id="subtotal" style="padding-left: 10px !important;">$0.00</th>
                                    </tr>
                                    <tr>
                                        <th colspan="7" class="text-right">Iva</th>
                                        <th id="iva" style="padding-left: 10px !important;">$0.00</th>
                                    </tr>
                                    <tr>
                                        <th colspan="7" class="text-right">Impoconsumo</th>
                                        <th id="impoconsumo" style="padding-left: 10px !important;">$0.00</th>
                                    </tr>
                                    <tr>
                                        <th colspan="7" class="text-right">Subsidio</th>
                                        <th id="total_subsidy" style="padding-left: 10px !important;">$0.00</th>
                                    </tr>
                                    <tr>
                                        <th colspan="7" class="text-right" style="font-size: 16px;">Total</th>
                                        <th id="total" style="padding-left: 10px !important; font-size: 16px;">$0.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    {{--  Fin modal detalle liquidacion facturada  --}}

@endsection
