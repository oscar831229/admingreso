@extends('layouts.belectronica.principal')

@section('scripts_content')
  <script>
    var route_mail = '{{ url('Admin/emails') }}';
    var token = '{{csrf_token()}}';
  </script>
  <script src="{{ asset('js/portal/admin/emails/email.crear.js') }}"></script>
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
            <a class="breadcrumb-item" href="{{ url('Admin/emails') }}">Emails</a>
            <span class="breadcrumb-item active">Nuevo email</span>
          </nav>
        </div><!-- br-pageheader -->
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div class="table-wrapper">
          {!! Form::open(array('route' => 'emails.store','method'=>'POST', 'id' => 'form-email','autocomplete'=>'off')) !!}
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <label class="requerido"><strong>Servidor smtp:</strong></label>
                  {!! Form::text('server', null, array('placeholder' => 'Servidor smtp','class' => 'form-control form-control-sm', 'id' => 'server','required' => 'required')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <label class="requerido"><strong>Encriptación:</strong></label>
                  {!! Form::select('encryption',[''=>'NINGUNA','tls'=>'TLS','ssl' => 'SSL'],[], array('class' => 'form-control form-control-sm','id'=>'encryption')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <label class="requerido"><strong>Puerto:</strong></label>
                  {!! Form::text('puerto', null, array('placeholder' => 'Puerto','class' => 'form-control form-control-sm','required' => 'required', 'id'=>'puerto')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <label class="requerido"><strong>Email:</strong></label>
                  {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control form-control-sm', 'id'=>'email','required' => 'required','id'=>'email')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <label class="requerido"><strong>Contraseña:</strong></label>
                  {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control form-control-sm', 'id' => 'password','id'=>'password')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <a class="btn btn-info btn-sm mr-3" href="#" id='testConexion'>Probar conexión</a>
                <button type="submit" class="btn btn-primary btn-sm d-none" id="saveMail">Guardar</button>
              </div>
            </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
