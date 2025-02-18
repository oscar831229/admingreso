@extends('layouts.belectronica.principal')

@section('scripts_content')
  <script>
    var url_users = '{{ url('Admin/users') }}';
  </script>
  <script src="{{ asset('js/portal/comunes.js') }}"></script>    
  <script src="{{ asset('js/portal/admin/usuarios/user.crear.js') }}"></script>
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
            <span class="breadcrumb-item active">Usuarios</span>
          </nav>
        </div><!-- br-pageheader -->
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div class="table-wrapper">
          {!! Form::open(array('route' => 'users.store','method'=>'POST','id'=>'usuario-crear')) !!}
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Login: <span class="tx-danger">*</span></strong>
                  {!! Form::text('login', null, array('placeholder' => 'Login','class' => 'form-control','required' => 'required')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Nombre: <span class="tx-danger">*</span></strong>
                  {!! Form::text('name', null, array('placeholder' => 'Nombre','class' => 'form-control','required' => 'required')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Número documento: <span class="tx-danger">*</span></strong>
                  {!! Form::text('document_number', null, array('placeholder' => 'Número de documento','class' => 'form-control','required' => 'required')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Email: <span class="tx-danger">*</span></strong>
                  {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control','required' => 'required')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Password: <span class="tx-danger">*</span></strong>
                  {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control','required' => 'required')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Confirm Password: <span class="tx-danger">*</span></strong>
                  {!! Form::password('password_confirmation', array('placeholder' => 'Confirm Password','class' => 'form-control','required' => 'required')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Role:</strong>
                  {!! Form::select('roles[]', $roles,[], array('class' => 'form-control','multiple')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Guardar</button>
              </div>
            </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
