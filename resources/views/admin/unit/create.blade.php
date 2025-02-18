@extends('layouts.portal.principal')

@section('scripts_content')
  <script src="{{ asset('js/portal/admin/servicios/servicio.crear.js') }}"></script>
@endsection

@section('content')
  <div class="br-mainpanel">
    @include('includes/mensaje')
    @include('includes/form-error')
    <div class="br-pageheader pd-y-15 pd-l-20">
      <nav class="breadcrumb pd-0 mg-0 tx-12">
        <a class="breadcrumb-item" href="{{ url('Admin/servicios') }}">Servicios</a>
        <span class="breadcrumb-item active">Nueva unidad funcional</span>
      </nav>
    </div><!-- br-pageheader -->

    <div class="br-pagebody">
      <div class="br-section-wrapper">
        <div class="table-wrapper">
          {!! Form::open(array('route' => 'units.store','method'=>'POST')) !!}
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Código unidad:</strong>
                  {!! Form::text('code', null, array('placeholder' => '','class' => 'form-control input-sm','required' => 'required','maxlength'=>'45')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <strong>Nombre unidad funcional:</strong>
                  {!! Form::text('name', null, array('placeholder' => '','class' => 'form-control input-sm','required' => 'required')) !!}
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Grabar</button>
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
