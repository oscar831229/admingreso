@extends('layouts.belectronica.principal')

@section('css_custom')
<link href="{{ asset('css/portal/style-datetable.css') }}" rel="stylesheet">
<style>
.table th,.table td{padding:.25rem!important}.table{font-size:13px}.font-head{font-weight:700;font-size:12px;text-transform:uppercase;color:#343a40;letter-spacing:.5px}.text-u{text-transform:uppercase!important}.text-l{text-transform:lowercase!important}.customer-filter{background:#f8f9fa;border:1px solid #dee2e6;padding:15px;margin-bottom:15px;border-radius:4px}.customer-filter label{font-weight:600;margin-bottom:4px}.customer-filter .form-control{height:32px}
</style>
@endsection

@section('scripts_content')
<script src="{{ asset('theme/lib/internacionalizacion/es.js') }}"></script>
<script src="{{ asset('theme/lib/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/portal/income/customers/index.js') }}"></script>
@endsection

@section('content')
<div class="row"><div class="col-md-12 col-sm-12">
@include('includes/mensaje')
@include('includes/form-error')

<div class="x_panel"><div class="x_title">
<div class="br-pageheader pd-y-15 pd-l-20"><nav class="breadcrumb pd-0 mg-0 tx-12">
<a class="breadcrumb-item" href="#">Ingreso a sedes</a><span class="breadcrumb-item active">Clientes</span>
</nav></div><div class="clearfix"></div>
</div>

<div class="x_content"><div class="card-body p-0">

<table width="100%" style="margin-bottom:20px;"><tr>
<td width="50" align="center" valign="top" class="pr-4"><h1 class="text-primary"><i class="fa fa-users"></i></h1></td>
<td><h4 class="tx-gray-800 mg-b5" style="margin-bottom:0;">Clientes</h4><span class="titulos">Consulta de clientes</span></td>
<td width="220" align="right" valign="middle">
<button type="button" class="btn btn-success btn-sm" id="btn-export-customers"><i class="fa fa-download"></i> Exportar clientes CSV</button>
</td>
</tr></table>

<div class="card mb-3" id="customer-export-card" style="display:none;">
<div class="card-header"><i class="fa fa-download"></i> Exportación de clientes</div>
<div class="card-body">
<div class="row">
<div class="col-md-4"><strong>Estado</strong><div id="customer-export-status">-</div></div>
<div class="col-md-4"><strong>Procesados</strong><div><span id="customer-export-processed">0</span> / <span id="customer-export-total">0</span></div></div>
<div class="col-md-4 text-right">
<a href="#" class="btn btn-primary btn-sm" id="btn-download-customers-export" style="display:none;"><i class="fa fa-download"></i> Descargar CSV</a>
</div>
</div>
<div class="progress mt-2" style="height:23px;"><div id="customer-export-progress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width:0%;">0%</div></div>
<div id="customer-export-error" class="alert alert-danger mt-2 mb-0" style="display:none;"></div>
</div>
</div>

<form id="form-filter-customers" onsubmit="return false;">@csrf
<div class="customer-filter"><div class="row">
<div class="col-md-3"><div class="form-group"><label>Número documento</label><input type="text" id="filter-document-number" class="form-control form-control-sm" maxlength="20" placeholder="Número exacto"></div></div>
<div class="col-md-3"><div class="form-group"><label>Nombre o apellido</label><input type="text" id="filter-name" class="form-control form-control-sm text-u" maxlength="150" placeholder="Mínimo 3 caracteres"></div></div>
<div class="col-md-3"><div class="form-group"><label>Teléfono</label><input type="text" id="filter-phone" class="form-control form-control-sm" maxlength="150" placeholder="Mínimo 3 caracteres"></div></div>
<div class="col-md-3"><div class="form-group"><label>Correo electrónico</label><input type="text" id="filter-email" class="form-control form-control-sm" maxlength="150" placeholder="Mínimo 3 caracteres"></div></div>
</div>
<div class="text-center">
<button type="button" class="btn btn-primary btn-sm" id="btn-search-customers"><i class="fa fa-database"></i> Buscar en todos</button>
<button type="button" class="btn btn-secondary btn-sm" id="btn-clear-customers"><i class="fa fa-eraser"></i> Limpiar</button>
</div>
<div id="customer-search-info" class="alert alert-info mt-2 mb-0"></div>
</div></form>

<button class="btn btn-warning btn-sm btn-block mg-b-10" id="btn-new-customers" style="display:none;"><i class="fa fa-plus-square-o mg-r-10"></i> NUEVO CLIENTE</button>

<div class="row text-center mt-2"><div class="col-sm-12"><div class="form-group"><div class="rounded table-responsive">
<table class="table table-bordered dataTable" style="width:99%;" id="tbl-customers">
<thead><tr><th>#</th><th>Número documento</th><th>Nombre</th><th>Teléfono</th><th>Email</th><th></th></tr></thead><tbody></tbody>
</table>
</div></div></div></div>

</div></div></div></div></div>

<div class="modal fade" id="md-customer" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document"><div class="modal-content">
<div class="modal-header text-white bg-primary"><h6 class="modal-title"><i class="fa fa-user"></i> Cliente</h6><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<div class="modal-body"><div class="row mb-4"><div class="col-xl-12 mg-t-20 mg-xl-t-0"><div class="form-layout form-layout-5 bd-info">

{{ Form::open(['id'=>'form-customers','autocomplete'=>'off','onsubmit'=>'return false;']) }}
<div class="form-group">{!! Form::label('document_type','Tipo documento') !!}{!! Form::select('document_type',$identification_document_types,null,['id'=>'document_type','class'=>'form-control form-control-sm','placeholder'=>'Seleccione..','required'=>'required','style'=>'height:25px;']) !!}{!! Form::hidden('id') !!}</div>
<div class="form-group">{!! Form::label('document_number','Número documento') !!}{!! Form::text('document_number',null,['id'=>'document_number','class'=>'form-control form-control-sm','disabled'=>'disabled','style'=>'height:25px;']) !!}</div>
<div class="form-group">{!! Form::label('first_name','Primer nombre') !!}{!! Form::text('first_name',null,['id'=>'first_name','class'=>'form-control form-control-sm uppercase','required'=>'required','style'=>'height:25px;']) !!}</div>
<div class="form-group">{!! Form::label('second_name','Segundo nombre') !!}{!! Form::text('second_name',null,['id'=>'second_name','class'=>'form-control form-control-sm uppercase','style'=>'height:25px;']) !!}</div>
<div class="form-group">{!! Form::label('first_surname','Primer apellido') !!}{!! Form::text('first_surname',null,['id'=>'first_surname','class'=>'form-control form-control-sm uppercase','required'=>'required','style'=>'height:25px;']) !!}</div>
<div class="form-group">{!! Form::label('second_surname','Segundo apellido') !!}{!! Form::text('second_surname',null,['id'=>'second_surname','class'=>'form-control form-control-sm uppercase','style'=>'height:25px;']) !!}</div>
<div class="form-group">{!! Form::label('birthday_date','Fecha nacimiento') !!}{!! Form::date('birthday_date',null,['id'=>'birthday_date','class'=>'form-control form-control-sm','required'=>'required','style'=>'height:25px;']) !!}</div>
<div class="form-group">{!! Form::label('gender','Genero') !!}{!! Form::select('gender',$genders,null,['id'=>'gender','class'=>'form-control form-control-sm','placeholder'=>'Seleccione..','required'=>'required','style'=>'height:25px;']) !!}</div>
<div class="form-group">{!! Form::label('icm_municipality_id','Municipio de residencia') !!}{!! Form::select('icm_municipality_id',$common_cities,null,['id'=>'icm_municipality_id','class'=>'form-control form-control-sm','placeholder'=>'Seleccione..','required'=>'required','style'=>'height:25px;']) !!}</div>
<div class="form-group">{!! Form::label('address','Dirección de residencia') !!}{!! Form::text('address',null,['id'=>'address','class'=>'form-control form-control-sm','required'=>'required','style'=>'height:25px;']) !!}</div>
<div class="form-group">{!! Form::label('phone','Teléfono') !!}{!! Form::text('phone',null,['id'=>'phone','class'=>'form-control form-control-sm','required'=>'required','style'=>'height:25px;']) !!}</div>
<div class="form-group">{!! Form::label('email','Correo electrónico') !!}{!! Form::text('email',null,['id'=>'email','class'=>'form-control form-control-sm','required'=>'required','style'=>'height:25px;']) !!}</div>
<div class="form-group">{!! Form::label('type_regime_id','Regimen fiscal') !!}{!! Form::select('type_regime_id',$tax_regime,null,['id'=>'type_regime_id','class'=>'form-control form-control-sm','placeholder'=>'Seleccione..','required'=>'required','style'=>'height:25px;']) !!}</div>
{{ Form::close() }}

<div class="row"><div class="col-sm-12 text-center"><br><button class="btn btn-success btn-sm" id="btn-save"><i class="fa fa-floppy-o mg-r-10"></i> Guardar</button></div></div>

</div></div></div></div>
<div class="modal-footer"></div>
</div></div></div>
@endsection
