@extends('layouts.portal.principal')

@section("scripts_content")
   <script src="{{asset("js/portal/admin/consecutivo/crear.js")}}" type="text/javascript"></script>
@endsection

@section('content')
<div class="br-mainpanel">
      @include('includes/mensaje')
      @include('includes/form-error')
      <div class="br-pageheader pd-y-15 pd-l-20">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
          <a class="breadcrumb-item" href="{{ url('Admin/consecutivo') }}">Consecutivos</a>
          <span class="breadcrumb-item active">Editar consecutivo</span>
        </nav>
      </div><!-- br-pageheader -->

      <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
        <h5 class="tx-gray-800 mg-b-5"><i class="fa fa-crosshairs tx-24"></i>&nbsp;&nbsp;Actualizar consecutivo solicitud</h5>
      </div>
  
      <div class="br-pagebody">
        <div class="br-section-wrapper">
          
          <div class="table-wrapper">
            
          {!! Form::model($consecutivo, ['method' => 'PATCH','route' => ['consecutivo.update', $consecutivo->id], 'autocomplete' => 'off']) !!}
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Prefijo:</strong>
                    {!! Form::text('prefijo', null, array('placeholder' => 'Prefijo facturación','class' => 'form-control','required' => 'required')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Consecutivo inicial:</strong>
                    {!! Form::number('consecutivo_inicial', null, array('placeholder' => 'Consecutivo inicial','class' => 'form-control','required' => 'required')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Consecutivo final:</strong>
                    {!! Form::text('consecutivo_final', null, array('placeholder' => 'Consecutivo final','class' => 'form-control','required' => 'required')) !!}
                </div>
            </div> 
            <div class="col-xs-12 col-sm-12 col-md-12">
              <div class="form-group">
                  <strong>Fecha inicial:</strong>
                  {!! Form::text('fecha_inicial', null, array('placeholder' => 'Consecutivo final','class' => 'form-control','required' => 'required', 'id' => 'fecha_inicial')) !!}
              </div>
            </div>                     
            <div class="col-xs-12 col-sm-12 col-md-12">
              <div class="form-group">
                  <strong>Fecha final:</strong>
                  {!! Form::text('fecha_final', null, array('placeholder' => 'Consecutivo final','class' => 'form-control','required' => 'required', 'id' => 'fecha_final')) !!}
              </div>
            </div>  
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Observacion:</strong>
                    {!! Form::text('observacion', null, array('placeholder' => 'Observación','class' => 'form-control','required' => 'required')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Estado:</strong>
                    {!! Form::select('estado',[''=>'Seleccione...','A'=>'Activo','I' => 'Inactivo'],null, array('class' => 'form-control','required' => 'required')) !!}
                </div>
            </div>
                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
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
