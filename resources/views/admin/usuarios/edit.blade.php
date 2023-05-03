@extends('layouts.portal.principal')

@section('scripts_content')
  <script>
    var url_users = '{{ url('Admin/users') }}';
  </script>
  <script src="{{ asset('js/portal/comunes.js') }}"></script>    
  <script src="{{ asset('js/portal/admin/usuarios/user.update.js') }}"></script>
@endsection

@section('content')
  <div class="br-mainpanel">
    @include('includes/mensaje')
    @include('includes/form-error')
    <div class="br-pageheader pd-y-15 pd-l-20">
      <nav class="breadcrumb pd-0 mg-0 tx-12">
        <a class="breadcrumb-item" href="{{ url('Admin/users') }}">Usuarios</a>
        <span class="breadcrumb-item active">Nuevo usuario</span>
      </nav>
    </div><!-- br-pageheader -->

    <div class="br-pagebody">
      <div class="br-section-wrapper">
        <div class="table-wrapper">
          {!! Form::model($user, ['method' => 'PATCH','route' => ['users.update', $user->id], 'id'=>'usuario-update']) !!}
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Login:</strong>
                  {!! Form::text('login', null, array('placeholder' => 'Login','class' => 'form-control','required' => 'required')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Nombre:</strong>
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
                  <strong>Email:</strong>
                  {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control','required' => 'required')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Unidad:</strong>
                  {!! Form::select('Units[]', $MedicalUnit, $userUnits, array('class' => 'form-control','multiple')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Role:</strong>
                  {!! Form::select('roles[]', $roles, $userRole, array('class' => 'form-control','multiple')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Activo:</strong>
                  {!! Form::select('active', [1 => 'Activo', 0 => 'Inactivo'], null, array('class' => 'form-control','multiple')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Guardar</button>
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
