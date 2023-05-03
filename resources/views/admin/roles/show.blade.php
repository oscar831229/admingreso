@extends('layouts.portal.principal')

@section('content')
<div class="br-mainpanel">
  <div class="br-pageheader pd-y-15 pd-l-20">
    <nav class="breadcrumb pd-0 mg-0 tx-12">
      <a class="breadcrumb-item" href="{{ url('Admin/roles') }}"">Roles</a>
      <span class="breadcrumb-item active">Datos rol</span>
    </nav>
  </div><!-- br-pageheader -->

  <div class="br-pagebody">
    <div class="br-section-wrapper">
      <div class="table-wrapper">
        
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
              <p><strong>Name:</strong> {{ $role->name }}</p>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
              <strong>Permissions:</strong>
              @if(!empty($rolePermissions))
              <div class="row">
                @foreach($rolePermissions as $v)
                  <div class="col-sm-3 col-xs-3 col-md-3 col-lg-3">
                    <div class="card mt-4" style="width: 18rem;">
                      <div class="card-header">
                        {{ $v->name }}
                      </div>
                      <ul class="list-group list-group-flush">
                        <li class="list-group-item">{{ $v->descripcion }}</li>
                      </ul>
                    </div>
                  </div>
                @endforeach
              </div>
              @endif
            </div>
          </div>
        </div>
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
