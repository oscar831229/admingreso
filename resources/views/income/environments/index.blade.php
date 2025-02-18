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
              <a class="breadcrumb-item" href="#">Ingreso a sedes</a>
              <span class="breadcrumb-item active">Ambientes</span>
            </nav>
          </div><!-- br-pageheader -->
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div class="card-body p-0">
            <table width='100%' style="margin-bottom: 40px;">
              <tr>
                  <td width='50' align="center" valign="top" class="pr-4">
                      <h1 class="text-primary"><i class="fa fa-building" aria-hidden="true"></i></h1>
                  </td>
                  <td>
                      <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Ambientes</h4>
                      <span class='titulos'>&nbsp;</span>
                  </td>
              </tr>
            </table>
            <div class="row">
                <div class="col-sm-8">
                    <table class="table table-hover width60" id="tabla-data" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th class="width25">ID</th>
                                <th class="width40">Nombre</th>
                                <th class="width20">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach ($environments as $key => $role)
                            <tr>
                              <td>{{$role->id}}</td>
                              <td>{{$role->name}}</td>
                              <td>{!! $role->state == 'A' ? '<span class="badge badge-success">Activa</span>' : '<span class="badge badge-secondary">Inactiva</span>' !!}</td>
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
  </div>
@endsection
