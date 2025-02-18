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
            <span class="breadcrumb-item active">Plantillas</span>
          </nav>
        </div><!-- br-pageheader -->
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table width='100%' style="margin-bottom: 40px;">
          <tr>
              <td width='50' align="center" valign="top" class="pr-4">
                  <h1 class="text-primary"><i class="fa fa-envelope-o" aria-hidden="true"></i></h1>
              </td>
              <td>
                  <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Plantillas de correo electrónico</h4>
                  <span class='titulos'>&nbsp;</span>
              </td>
          </tr>
        </table>

          <table class="table table-hover width60" id="tabla-data">
              <thead>
                  <tr>
                      <th class="width20">#</th>
                      <th class="width30">Código</th>
                      <th class="width30">Descripción</th>
                      <th class="width10">Email</th>
                      <th class="width10"></th>
                  </tr>
              </thead>
              <tbody>
                @foreach ($plantillas as $key => $plantilla)
                  <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $plantilla->codigo }}</td>
                    <td>{{ $plantilla->nombre }}</td>
                    <td>{{ $plantilla->email->email?? '' }}</td>
                    <td>
                      @can('plantillas-edit')
                      <a href="{{ route('plantillas.edit',$plantilla->id) }}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                          <i class="fa fa-edit"></i>
                      </a>
                      @endcan
                    </td>
                  </tr>
                  @endforeach
              </tbody>
          </table>

        </div>
      </div>
      </div>
    </div>
  </div>
</div>
@endsection
