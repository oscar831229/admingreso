@extends('layouts.belectronica.principal')

@section('css_custom')
  <style>
    .table-bordered {
      border: 1px solid #dee2e6;
    }

    .table-bordered th,
    .table-bordered td {
      border: 1px solid #dee2e6;
    }

    .table-bordered thead th,
    .table-bordered thead td {
      border-bottom-width: 2px;
    }

    .table-bordered th,
    .table-bordered td {
      border: 1px solid #dee2e6 !important;
    }
  </style>  
@endsection

@section('scripts_content')
  

  <!-- include libraries(jQuery, bootstrap) -->
  <script src="{{ asset('theme/lib/bootstrap/bootstrap.min.js') }}"></script>

  <!-- include summernote css/js -->
  <link href="{{ asset('theme/lib/summernote/summernote.min.css') }}" rel="stylesheet">
  <script src="{{ asset('theme/lib/summernote/summernote.min.js') }}"></script>
  <script src="{{ asset('theme/lib/summernote/lang/summernote-es-ES.min.js') }}"></script>

  <script src="{{ asset('js/portal/admin/plantillas/plantilla.update.js') }}"></script>

<script>
  
</script>
  
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
            <a class="breadcrumb-item" href="{{ url('Admin/plantillas') }}">Plantillas</a>
            <span class="breadcrumb-item active">Actualizando - Plantilla</span>
          </nav>
        </div><!-- br-pageheader -->
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div class="table-wrapper">
          <div class="row">
              <div class="col-sm-2">
                  <div class="row">
                    <div class="col-sm-12 tx-bold tx-inverse pb-4">Variables</div>
                    @foreach ($variables as $key => $item)
                    <div class="col-sm-12">{{ '@'.$key }}</div>
                    @endforeach
                  </div>
              </div>
              <div class="col-sm-10">
                {!! Form::model($plantilla, ['method' => 'PATCH','route' => ['plantillas.update', $plantilla->id],'id' => 'plantialla-update']) !!}
                <div class="row">
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                      <strong>Codigo:</strong>
                      {!! Form::text('codigo', $plantilla->codigo, array('placeholder' => 'Codigo','class' => 'form-control form-control-sm','readOnly' => 'readOnly')) !!}
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                      <strong>Nombre:</strong>
                      {!! Form::text('name', $plantilla->nombre, array('placeholder' => 'Nombre','class' => 'form-control form-control-sm','readOnly')) !!}
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                      <strong>Asunto:</strong>
                      {!! Form::text('asunto', $plantilla->asunto, array('placeholder' => 'Asunto','class' => 'form-control form-control-sm','required' => 'required')) !!}
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                      <strong>Mensaje:</strong>
                      {!! Form::textarea('mensaje', null, ['class'=>'form-control','id'=>'mensaje','required' => 'required']) !!}
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                      <strong>Email:</strong>
                      {!! Form::select('emails_id', $emails, null, array('placeholder'=>'Seleccione...','class' => 'form-control form-control-sm','required' => 'required')) !!}
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Grabar</button>
                  </div>
                </div>
                {!! Form::close() !!}
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
