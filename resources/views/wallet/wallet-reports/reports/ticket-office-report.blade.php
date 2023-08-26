@extends('layouts.app-aux')

@section('scripts_content')
  <script src= "{{ asset('js/plugins/jquery.autocomplete/js/jquery.autocomplete.min.js') }}"></script> 
  <script src="{{ asset('js/portal/wallet/wallet-reports/reports/'.$code.'.js') }}"></script>
@endsection


@section('content')
  {!! Form::open([
    'route' => 'wallet-reports.store',
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
        {!! Form::label('wallet_user_name', 'Nombre usuario', ['class' => 'col-md-4 control-label', 'style' => 'height: 25px;']) !!}
        <div class="col-md-12">
          {!! Form::text('wallet_user_name', null, array('id' => 'wallet_user_name','class' => 'form-control form-control-sm uppercase', 'style' => 'height: 25px;' )) !!}
          <input class="form-control" type="text" id="wallet_user_x" disabled="disabled" style="color: #CCC; position: absolute; background: transparent; z-index: 1; display:none">
          {!! Form::hidden('wallet_user_id', null, ['class' => 'form-control form-control-sm', 'id' => 'wallet_user_id']) !!}
        </div>
      </div>
    </div>

    <div class="col-sm-12">
      <div class="form-group">
        {!! Form::label('cus', 'CUS', ['class' => 'col-md-4 control-label']) !!}
        <div class="col-md-12">
          {!! Form::text('cus', null, array('id' => 'cus','class' => 'form-control form-control-sm uppercase', 'style' => 'height: 25px;' )) !!}
        </div>
      </div>
    </div>

    <div class="col-sm-12">
      <div class="form-group">
        {!! Form::label('store_id', 'Estado ticketera', ['class' => 'col-md-4 control-label']) !!}
        <div class="col-md-12">
          {!! Form::select('store_id', ['P' => 'SIN REDIMIR', 'R' => 'REDIMIDOS', 'A' => 'ANULADOS'], null, array('class' => 'form-control form-control-sm','id'=>'store_id', 'style' => 'height: 25px;', 'placeholder' => '[ Todos ]')) !!}
        </div>
      </div>
    </div>

    <div class="col-sm-12">
      <div class="form-group">
        {!! Form::label('movement_state_from', 'Fecha inicial (REDIMIDO, ANULADO)', ['class' => 'col-md-4 control-label']) !!}
        <div class="col-md-12">
          {!! Form::date('movement_state_from', null, ['class' => 'form-control form-control-sm', 'style' => 'height: 25px;', 'id' => 'movement_state_from']) !!}
        </div>
      </div>
    </div>

    <div class="col-sm-12">
      <div class="form-group">
        {!! Form::label('movement_estado_to', 'Fecha final (REDIMIDO, ANULADO)', ['class' => 'col-md-4 control-label']) !!}
        <div class="col-md-12">
          {!! Form::date('movement_estado_to', null, ['class' => 'form-control form-control-sm', 'style' => 'height: 25px;', 'id' => 'movement_estado_to']) !!}
          {!! Form::hidden('report', $code, ['class' => 'form-control form-control-sm ']) !!}
        </div>
      </div>
    </div>

    <div class="col-sm-12">
      <div class="form-group">
        {!! Form::label('movement_date_from', 'Fecha inicial registro', ['class' => 'col-md-4 control-label']) !!}
        <div class="col-md-12">
          {!! Form::date('movement_date_from', null, ['class' => 'form-control form-control-sm', 'style' => 'height: 25px;', 'id' => 'movement_date_from']) !!}
        </div>
      </div>
    </div>

    <div class="col-sm-12">
      <div class="form-group">
        {!! Form::label('movement_date_to', 'Fecha final registro', ['class' => 'col-md-4 control-label']) !!}
        <div class="col-md-12">
          {!! Form::date('movement_date_to', null, ['class' => 'form-control form-control-sm', 'style' => 'height: 25px;', 'id' => 'movement_date_to']) !!}
          {!! Form::hidden('report', $code, ['class' => 'form-control form-control-sm ']) !!}
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
