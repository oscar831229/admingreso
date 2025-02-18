@extends('layouts.belectronica.principal')

@section('css_custom')
  <link href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
  <link href="{{ asset('theme/lib/datatables-plugins/Buttons-1.4.2/css/buttons.dataTables.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/portal/style-datetable.css') }}" rel="stylesheet">
  <style>
    .table th, .table td {
      padding: 0.45rem !important;
    }
    .table {
      font-size: 12px;
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
    <script src="{{ asset('theme/lib/datatables-plugins/Buttons-1.4.2/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('theme/lib/datatables-plugins/Buttons-1.4.2/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('theme/lib/datatables-plugins/Buttons-1.4.2/js/jszip.min.js') }}"></script>
    <script src="{{ asset('theme/lib/datatables-plugins/Buttons-1.4.2/js/buttons.html5.min.js') }}"></script>
    <script src= "{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src= "{{ asset('js/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') }}"></script>
    <script src="{{ asset('js/portal/income/income-reports/index.js') }}"></script>
@endsection


@section('content')
<div class="br-mainpanel">
      @include('includes/mensaje')
      @include('includes/form-error')
      <div class="br-pageheader pd-y-15 pd-l-20">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
          <a class="breadcrumb-item" href="#">Módulo suministros</a>
          <span class="breadcrumb-item active">Reporteador</span>
        </nav>
      </div><!-- br-pageheader -->

      <div class="br-pagebody">
        <div class="br-section-wrapper mt-4">

          <table width='100%' style="margin-bottom: 20px;">
            <tr>
                <td width='50' align="center" valign="top" class="pr-4">
                    <h1><i class="fa fa-file-excel-o" aria-hidden="true"></i></h1>
                </td>
                <td>
                    <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">{{ $module_name }}</h4>
                    <span class='titulos'><?php echo date('yy-m-d h:m'); ?></span>
                </td>
            </tr>
          </table>

          <div class="card-body table-responsive">
            <table class="table table-hover width60" id="tblpeoplecompany" style="font-size: 12px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th style="width: 100px;">código</th>
                        <th>Reporte</th>
                        <th class="text-center">Actión</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach ($reports as $report)
                    <tr>
                      <td>{{ $report->id }}</td>
                      <td style="width: 200px;">{{ $report->code }}</td>
                      <td>{{ $report->description }}</td>
                      <td class="text-center">
                        <a href="javascript:void(0)" class="btn-accion-tabla tooltipsC view-form-report" title="" data-original-title="Generar reporte" data-code="{{ $report->code }}">
                          <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
            </table>
          </div>
        </div><!-- br-section-wrapper -->
      </div><!-- br-pagebody -->

      {{--modal for view detail --}}
      <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

          <div class="modal-dialog modal-lg" role="document" style="width:1000px !important; max-width: 800px !important;">
              <div class="modal-content">
                  <div class="modal-header" id="modal-header" style="background-color: #3c5984;color: #f1f1f1;text-transform: uppercase;">
                      <span class="report-name"></span>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body" id="modal-body"></div>
              </div>
          </div>
      </div>


    </div>
@endsection
