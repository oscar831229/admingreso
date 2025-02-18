@extends('layouts.belectronica.principal')

@section('content')
  @include('includes/mensaje')
  @include('includes/form-error')
  <div class="row">
    <div class="col-md-12 col-sm-12 ">
      <div class="x_panel">
        <div class="x_title">
            <div class="br-pageheader pd-y-15 pd-l-20">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="{{ url('Admin/roles') }}">Roles</a>
                <span class="breadcrumb-item active">Crear rol</span>
            </nav>
            </div><!-- br-pageheader -->
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div class="table-wrapper">
           {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Permission:</strong>
                            @if(!empty($permission))
                                <div class="row">
                                @foreach($permission as $value)
                                    <div class="col-sm-3 col-xs-3 col-md-3 col-lg-3">
                                    <div class="card mt-4" style="width: 18rem;">
                                        <div class="card-header">
                                            <label>{{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                                                {{ $value->name }}</label>
                                        </div>
                                        <ul class="list-group list-group-flush">
                                        <li class="list-group-item">{{ $value->descripcion }}</li>
                                        </ul>
                                    </div>
                                    </div>
                                @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                        <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                    </div>
                </div>
                {!! Form::close() !!} 
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
