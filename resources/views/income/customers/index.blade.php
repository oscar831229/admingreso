@extends('layouts.belectronica.principal')

@section('css_custom')
    <link href="{{ asset('css/portal/style-datetable.css') }}" rel="stylesheet">
    <style>
        .table th, .table td {
        padding: 0.25rem !important;
        }
        .table {
        font-size: 11px;
        }
        .font-head {
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        color: #343a40;
        letter-spacing: 0.5px;
        }
        .text-u {
        text-transform: uppercase !important;
        }
        .text-l {
        text-transform: lowercase !important;
        }
        .table th, .table td {
            padding: 0.25rem !important;
        }
        .table {
            font-size: 13px;
        }
        .font-head {
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            color: #343a40;
            letter-spacing: 0.5px;
        }
        .text-u {
            text-transform: uppercase !important;
        }
        .text-l {
            text-transform: lowercase !important;
        }

    </style>
@endsection

@section('scripts_content')
    <script src="{{ asset('theme/lib/internacionalizacion/es.js') }}"></script>
    <script src="{{ asset('theme/lib/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/portal/income/customers/index.js') }}"></script>
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
              <a class="breadcrumb-item" href="#">Ingreso a sedes</a>
              <span class="breadcrumb-item active">Clientes</span>
            </nav>
          </div><!-- br-pageheader -->
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="card-body p-0">
                <table width='100%' style="margin-bottom: 20px;">
                    <tr>
                        <td width='50' align="center" valign="top" class="pr-4">
                            <h1 class="text-primary"><i class="fa fa-users" aria-hidden="true"></i></h1>
                        </td>
                        <td>
                            <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Clientes</h4>
                            <span class='titulos'>&nbsp;</span>
                        </td>
                    </tr>
                </table>

                <button class="btn btn-warning btn-sm btn-block mg-b-10" id="btn-new-customers" style="display:none;"><i class="fa fa-plus-square-o mg-r-10"></i> NUEVO TIPO TARIFA</button>

                <div class="row text-center mt-2">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="rounded table-responsive">
                                <table class="table table-bordered dataTable" style="width: 99%;" id="tbl-customers">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Número documento</th>
                                            <th>Nombre</th>
                                            <th>Telefono</th>
                                            <th>Email</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
      </div>
    </div>
  </div>

    {{--  Modal nuevo o actualización producto --}}
    <div class="modal fade" id="md-customer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-white bg-primary">
                    <h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-user" aria-hidden="true"></i> Cliente</span></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-xl-12 mg-t-20 mg-xl-t-0">
                            <div class="form-layout form-layout-5 bd-info">
                                {{ Form::open(array(
                                    'id'=>'form-customers',
                                    'autocomplete'=>'off',
                                    'onsubmit' => 'return false;'
                                )) }}
                                    <div class="form-group">
                                        {!! Form::label('document_type','Tipo documento', [], false) !!}
                                        {!! Form::select('document_type', $identification_document_types, null, array('id' => 'document_type','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;')) !!}
                                        {!! Form::hidden('id') !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('document_number','Número documento', [], false) !!}
                                        {!! Form::text('document_number', null, array('id' => 'document_number','placeholder' => 'Número documento','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;', 'disabled' => 'disabled')) !!}
                                    </div>


                                    <div class="form-group">
                                        {!! Form::label('first_name','Primer nombre', [], false) !!}
                                        {!! Form::text('first_name', null, array('id' => 'first_name','placeholder' => 'Primer nombre','class' => 'form-control form-control-sm uppercase','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('second_name','Segundo nombre', [], false) !!}
                                        {!! Form::text('second_name', null, array('id' => 'second_name','placeholder' => 'Segundo nombre','class' => 'form-control form-control-sm uppercase', 'style' => 'height: 25px;')) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('first_surname','Primer apellido', [], false) !!}
                                        {!! Form::text('first_surname', null, array('id' => 'first_surname','placeholder' => 'Primer apellido','class' => 'form-control form-control-sm uppercase','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('second_surname','Segundo apellido', [], false) !!}
                                        {!! Form::text('second_surname', null, array('id' => 'second_surname','placeholder' => 'Segundo apellido','class' => 'form-control form-control-sm uppercase', 'style' => 'height: 25px;')) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('birthday_date','Fecha nacimiento', [], false) !!}
                                        {!! Form::date('birthday_date', null, array('id' => 'birthday_date','placeholder' => 'Primer nombre','class' => 'form-control form-control-sm uppercase','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('gender','Genero', [], false) !!}
                                        {!! Form::select('gender', $genders, null, array('id' => 'gender','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('icm_municipality_id','Municipio de residencia', [], false) !!}
                                        {!! Form::select('icm_municipality_id', $common_cities, null, array('id' => 'icm_municipality_id','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('address','Dirección de residencia', [], false) !!}
                                        {!! Form::text('address', null, array('id' => 'address','placeholder' => 'Dirección','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('phone','Teléfono', [], false) !!}
                                        {!! Form::text('phone', null, array('id' => 'phone','placeholder' => 'Teléfono','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('email','Correo electrónico', [], false) !!}
                                        {!! Form::text('email', null, array('id' => 'email','placeholder' => 'Correo electrónico','class' => 'form-control form-control-sm','required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('type_regime_id','Regimen fiscal', [], false) !!}
                                        {!! Form::select('type_regime_id', $tax_regime, null, array('id' => 'type_regime_id','class' => 'form-control form-control-sm','placeholder' => 'Seleccione..', 'required' => 'required', 'style' => 'height: 25px;')) !!}
                                    </div>

                                {{ Form::close() }}
                                <div class="row">
                                    <div class="col-sm-12 text-center">
                                        <div class="form-group">
                                            <br>
                                            <button class="btn btn-success btn-sm" id="btn-save"><i class="fa fa-floppy-o mg-r-10"></i> Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- form-layout -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    {{--  Fin modal nuevo o actualización producto  --}}
@endsection
