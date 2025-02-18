@extends('layouts.app-aux')

@section('scripts_content')
  <script src= "{{ asset('js/plugins/jquery.autocomplete/js/jquery.autocomplete.min.js') }}"></script>
  <script src="{{ asset('js/portal/income/income-reports/reports/'.$code.'.js') }}"></script>
@endsection


@section('content')
  {!! Form::open([
    'route' => 'income-reports.store',
    'method'=>'POST',
    'autocomplete'=>'off',
    'class' => 'form-horizontal',
    'files' => true,
    'onsubmit' => "submitButton.disabled = true",
    'id' => 'form-report'
  ]) !!}
  <div class="row pb-4 pt-2">

    <div class="col-sm-12">
        <div class="form-group">
            <div class="col-sm-12">
                <div class="form-group">
                {!! Form::label('date_from', 'Fecha inicial facturación', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-12">
                    {!! Form::date('date_from', null, ['class' => 'form-control form-control-sm', 'style' => 'height: 25px;', 'required' => 'required']) !!}
                </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                {!! Form::label('date_to', 'Fecha final facturacion', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-12">
                    {!! Form::date('date_to', null, ['class' => 'form-control form-control-sm', 'style' => 'height: 25px;' , 'required' => 'required']) !!}
                    {!! Form::hidden('report', $code, ['class' => 'form-control form-control-sm ']) !!}
                </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 text-center mt-4">
      <a href="javascript:void(0)" class="btn btn-sm btn-sm btn-primary btn-with-icon mg-t-10" id="bth-report-generate">
        <div class="ht-40">
          <span class="icon wd-40"><i class="fa fa-file-excel-o" aria-hidden="true"></i></span>
          <span class="pd-x-15">Genarar informe</span>
        </div>
      </a>
    </div>

  </div>
  {!! Form::close() !!}

@endsection
