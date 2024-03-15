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
  </style>
@endsection

@section('scripts_content')
<script src="{{ asset('js/portal/admin/roles/rol.index.js') }}"></script>
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
              <a class="breadcrumb-item" href="#">Panel administración</a>
              <span class="breadcrumb-item active">Configuración sistema</span>
            </nav>
          </div><!-- br-pageheader -->
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div class="card-body  p-0">
            <table width='100%' style="margin-bottom: 40px;">
              <tr>
                  <td width='50' align="center" valign="top" class="pr-4">
                      <h1 class="text-primary"><i class="fa fa-cog" aria-hidden="true"></i></h1>
                  </td>
                  <td>
                      <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Configuración sistema</h4>
                      <span class='titulos'><?php echo date('Y-m-d h:m'); ?></span>
                  </td>
              </tr>
            </table>
            <div class="table-wrapper">
                {!! Form::model($cmmsetting, array('route' => 'settings.store','method'=>'POST')) !!}
                  <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6">
                      <div class="form-group">
                        <strong>Salon ambiente:</strong>
                        {!! Form::select('room_id', $room, null, array('class' => 'form-control', 'placeholder' => 'Seleccione')) !!}
                      </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <button class="btn btn-success btn-sm" id="btn-save"><i class="fa fa-floppy-o mg-r-10"></i> Guardar </button>
                        </div>
                    </div>
                  </div>
                {!! Form::close() !!}
            </div>
        </div>
        </div>
      </div>
    </div>
  </div>
@endsection
