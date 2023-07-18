@extends('layouts.belectronica.principal')

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12 ">
      @include('includes/mensaje')
      @include('includes/form-error')
      <div class="x_panel">
        <div class="x_title">
            <div class="br-pageheader pd-y-15 pd-l-20">
                <nav class="breadcrumb pd-0 mg-0 tx-12">
                    <a class="breadcrumb-item" href="{{ url('Admin/users') }}">Usuarios</a>
                    <span class="breadcrumb-item active">Datos usuario</span>
                </nav>
            </div><!-- br-pageheader -->
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="table-wrapper">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Usuario</strong>
                            {{ $user->login }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $user->name }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Email:</strong>
                            {{ $user->email }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Activado:</strong>
                            {{ $user->active }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Access Token:</strong>
                            {{ $access_token }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Access Token:</strong>
                            @if(!empty($user->getRoleNames()))
                                @foreach($user->getRoleNames() as $v)
                                    <label class="badge badge-success">{{ $v }}</label>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <table id="tbl-solicitudes" class="table display responsive nowrap dataTable no-footer dtr-inline" role="grid" aria-describedby="datatable1_info" style="width: 1110px; font-size: 12px;">
                    <thead>
                      <tr role="row">
                        <th>#</th>
                        <th>DIRECCION IP</th>
                        <th>ACCION</th>
                        <th>FECHA LOG</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($autenticate_logs as $key => $log)
                        <tr>
                          <td>{{ $key+1 }}</td>
                          <td>{{ $log->ipaddress  }}</td>
                          <td>{{ $log->observation }}</td>
                          <td>{{ $log->created_at }}</td>
                          <td>{{ $log->area_covid }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                </table><!-- table-wrapper -->
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
