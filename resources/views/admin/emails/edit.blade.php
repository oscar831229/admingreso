@extends('layouts.portal.principal')

@section('scripts_content')
  <script>
    var route_mail = '{{ url('Admin/emails') }}';
    var token = '{{csrf_token()}}';
  </script>
  <script src="{{ asset('js/portal/admin/emails/email.crear.js') }}"></script>
@endsection

@section('content')
  <div class="br-mainpanel">
    @include('includes/mensaje')
    @include('includes/form-error')
    <div class="br-pageheader pd-y-15 pd-l-20">
      <nav class="breadcrumb pd-0 mg-0 tx-12">
        <a class="breadcrumb-item" href="{{ url('Admin/emails') }}">Emails</a>
        <span class="breadcrumb-item active">Editar email</span>
      </nav>
    </div><!-- br-pageheader -->

    <div class="br-pagebody">
      <div class="br-section-wrapper">
        <div class="table-wrapper">
          {!! Form::model($email, ['method' => 'PATCH','route' => ['emails.update', $email->id], 'id' => 'form-email']) !!}
          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
              <div class="form-group">
                <label class="requerido"><strong>Servidor smtp:</strong></label>
                {!! Form::text('server', null, array('placeholder' => 'Servidor smtp','class' => 'form-control', 'id' => 'server','required' => 'required')) !!}
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
              <div class="form-group">
                <label class="requerido"><strong>Encriptación:</strong></label>
                {!! Form::select('encryption',[''=>'Seleccione...','tls'=>'TLS','ssl' => 'SSL'], $email->encryption, array('class' => 'form-control','id'=>'encryption')) !!}
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
              <div class="form-group">
                <label class="requerido"><strong>Puerto:</strong></label>
                {!! Form::text('puerto', null, array('placeholder' => 'Puerto','class' => 'form-control','required' => 'required', 'id'=>'puerto')) !!}
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
              <div class="form-group">
                <label class="requerido"><strong>Email:</strong></label>
                {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control', 'id'=>'email','required' => 'required','id'=>'email')) !!}
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
              <div class="form-group">
                <label class="requerido"><strong>Contraseña:</strong></label>
                {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control', 'id' => 'password','id'=>'password')) !!}
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
              <a class="btn btn-info btn-sm mr-3" href="#" id='testConexion'>Probar conexión</a>
              <button type="submit" class="btn btn-primary btn-sm" id="saveMail">Guardar</button>
            </div>
          </div>
          {!! Form::close() !!}
        </div>
      </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->
    <footer class="br-footer">
      <div class="footer-left">
        <div class="mg-b-2"></div>
        <div></div>
      </div>
      <div class="footer-right d-flex align-items-center">
        <span class="tx-uppercase mg-r-10"></span>
        </div>
    </footer>
  </div>
@endsection
