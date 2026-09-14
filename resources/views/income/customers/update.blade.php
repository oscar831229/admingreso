@extends('layouts.belectronica.principal')

@section('scripts_content')
<script src="{{ asset('js/portal/income/customers/update.js') }}"></script>
@endsection

@section('content')

@if($activeImport)
<input type="hidden" id="active-import-id" value="{{ $activeImport->id }}">
<input type="hidden" id="active-import-status" value="{{ $activeImport->status }}">
@endif

<div class="row">
<div class="col-md-12 col-sm-12">
@include('includes/mensaje')
@include('includes/form-error')

<div class="x_panel">
<div class="x_title">
<div class="br-pageheader pd-y-15 pd-l-20">
<nav class="breadcrumb pd-0 mg-0 tx-12">
<a class="breadcrumb-item" href="#">Ingreso a sedes</a>
<span class="breadcrumb-item">Clientes</span>
<span class="breadcrumb-item active">Actualización masiva</span>
</nav>
</div>
<div class="clearfix"></div>
</div>

<div class="x_content">

<div class="card">
<div class="card-header bg-primary text-white">
<i class="fa fa-upload"></i> Actualización masiva de clientes
</div>
<div class="card-body">

<div class="alert alert-info">
<strong>Formato requerido:</strong> archivo CSV UTF-8 separado por punto y coma (<strong>;</strong>).<br>
Los valores que contengan punto y coma deben encontrarse encapsulados entre comillas dobles.
</div>

<div class="alert alert-secondary">
<strong>Columnas:</strong><br>
document_type; document_number; first_surname; second_surname; first_name; second_name; birthday_date; gender; address; email
</div>

<form id="form-customer-update" enctype="multipart/form-data" onsubmit="return false;">
@csrf
<div class="form-group">
<label>Archivo CSV</label>
<input type="file" class="form-control" name="file" id="file" accept=".csv" required>
</div>

<button type="button" class="btn btn-primary" id="btn-upload">
<i class="fa fa-upload"></i> Cargar y validar archivo
</button>
</form>
</div>
</div>

<div class="card mt-3" id="process-card" style="{{ $activeImport ? '' : 'display:none;' }}">
<div class="card-header">Estado del proceso</div>
<div class="card-body">

<h5 id="process-status">{{ $activeImport ? $activeImport->status : '-' }}</h5>

<div class="progress" style="height:25px;">
<div id="process-progress"
class="progress-bar progress-bar-striped progress-bar-animated"
role="progressbar"
style="width:{{ $activeImport ? $activeImport->progress : 0 }}%;">
{{ $activeImport ? $activeImport->progress : 0 }}%
</div>
</div>

<br>

<div class="row">
<div class="col-md-3">
<strong>Total</strong>
<h4 id="total-rows">{{ $activeImport ? number_format($activeImport->total_rows,0,',','.') : 0 }}</h4>
</div>

<div class="col-md-3">
<strong>Válidos</strong>
<h4 class="text-success" id="valid-rows">{{ $activeImport ? number_format($activeImport->valid_rows,0,',','.') : 0 }}</h4>
</div>

<div class="col-md-3">
<strong>Errores</strong>
<h4 class="text-danger" id="error-rows">{{ $activeImport ? number_format($activeImport->error_rows,0,',','.') : 0 }}</h4>
</div>

<div class="col-md-3">
<strong>Actualizados</strong>
<h4 class="text-primary" id="updated-rows">{{ $activeImport ? number_format($activeImport->updated_rows,0,',','.') : 0 }}</h4>
</div>
</div>

<div id="process-error" class="alert alert-danger mt-2" style="display:none;"></div>

<div class="text-center mt-3">
<button type="button" class="btn btn-success" id="btn-apply"
style="{{ $activeImport && $activeImport->status == 'READY' && $activeImport->valid_rows > 0 ? '' : 'display:none;' }}">
<i class="fa fa-check"></i> Aplicar actualización
</button>

<button type="button" class="btn btn-secondary" id="btn-cancel-import"
style="{{ $activeImport && $activeImport->status == 'READY' ? '' : 'display:none;' }}">
<i class="fa fa-times"></i> Cancelar proceso
</button>

<a href="{{ $activeImport ? url('/Admin/customer-update/'.$activeImport->id.'/errors') : '#' }}"
class="btn btn-danger" id="btn-errors"
style="{{ $activeImport && $activeImport->status == 'READY' && $activeImport->error_rows > 0 ? '' : 'display:none;' }}">
<i class="fa fa-download"></i> Descargar errores
</a>
</div>

</div>
</div>

<div class="card mt-3">
<div class="card-header">Últimas importaciones</div>
<div class="card-body">
<div class="table-responsive">

<table class="table table-bordered table-sm">
<thead>
<tr>
<th>#</th>
<th>Archivo</th>
<th>Estado</th>
<th>Total</th>
<th>Válidos</th>
<th>Errores</th>
<th>Actualizados</th>
<th>Fecha</th>
<th>Acciones</th>
</tr>
</thead>

<tbody>
@foreach($imports as $import)
<tr>
<td>{{ $import->id }}</td>
<td>{{ $import->original_name }}</td>
<td>{{ $import->status }}</td>
<td>{{ number_format($import->total_rows,0,',','.') }}</td>
<td>{{ number_format($import->valid_rows,0,',','.') }}</td>
<td>{{ number_format($import->error_rows,0,',','.') }}</td>
<td>{{ number_format($import->updated_rows,0,',','.') }}</td>
<td>{{ $import->created_at }}</td>
<td style="white-space:nowrap;">
<a href="{{ url('/Admin/customer-update/'.$import->id.'/download') }}"
class="btn btn-primary btn-sm" title="Descargar CSV original">
<i class="fa fa-download"></i> CSV
</a>

@if($import->status == 'READY' && $import->error_rows > 0)
<a href="{{ url('/Admin/customer-update/'.$import->id.'/errors') }}"
class="btn btn-danger btn-sm" title="Descargar errores">
<i class="fa fa-exclamation-triangle"></i> Errores
</a>
@endif
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
</div>

@endsection
