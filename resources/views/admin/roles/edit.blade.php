@extends('layouts.portal.principal')

@section('content')
<div class="br-mainpanel">
      @include('includes/mensaje')
      @include('includes/form-error')
      <div class="br-pageheader pd-y-15 pd-l-20">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
          <a class="breadcrumb-item" href="{{ url('Admin/roles') }}">Roles</a>
          <span class="breadcrumb-item active">Editar rol</span>
        </nav>
      </div><!-- br-pageheader -->
  
      <div class="br-pagebody">
        <div class="br-section-wrapper">
          
          <div class="table-wrapper">
            
          {!! Form::model($role, ['method' => 'PATCH','route' => ['roles.update', $role->id]]) !!}
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control','readonly' => 'readonly')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Permission:</strong>
                        @if(!empty($permission))
                        <div class="row">
                          @foreach($permission as $value)
                            <div class="col-sm-4 col-xs-4 col-md-4 col-lg-4">
                              <div class="card mt-4" style="width: 18rem;">
                                <div class="card-header">
                                  <label>{{ Form::checkbox('permission[]', $value->id, in_array($value->id, $rolePermissions) ? true : false, array('class' => 'name')) }}
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
