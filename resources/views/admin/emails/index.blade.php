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
  </style>
@endsection

@section('scripts_content')
  <script src="{{ asset('theme/lib/datatables/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('theme/lib/datatables-responsive/dataTables.responsive.js') }}"></script>
  <script src="{{ asset('js/portal/admin/emails/email.index.js') }}"></script>
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
            <a class="breadcrumb-item" href="#">Panel administración</a>
            <span class="breadcrumb-item active">Email</span>
          </nav>
        </div><!-- br-pageheader -->
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table width='100%' style="margin-bottom: 40px;">
          <tr>
              <td width='50' align="center" valign="top" class="pr-4">
                  <h1 class="text-primary"><i class="fa fa-envelope-o" aria-hidden="true"></i></h1>
              </td>
              <td>
                  <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Emails</h4>
                  <span class='titulos'><?php echo date('Y-m-d h:m'); ?></span>
              </td>
          </tr>
        </table>

        <div class="mb-4">
          <a href="{{ route('emails.create') }}" class="btn btn-primary btn-sm">Nuevo email</a>
        </div>

          <table class="table table-hover width60" id="tabla-data" style="font-size: 12px;">
              <thead>
                  <tr>
                      <th class="width20">#</th>
                      <th class="width40">Email</th>
                      <th class="width10">Servidor</th>
                      <th class="width10">Encriptación</th>
                      <th class="width10">Puerto</th>
                      <th class="width10"></th>
                  </tr>
              </thead>
              <tbody>
                  @foreach($emails as $key => $email)
                  <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{$email->email}}</td>
                    <td>{{$email->server}}</td>
                    <td>{{$email->encryption}}</td>
                    <td>{{$email->puerto}}</td>
                    <td>
                      @can('emails-edit')
                        <a href="{{ route('emails.edit',$email->id) }}" style="width: 145px;" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                            <i class="fa fa-edit"></i>
                        </a>
                      @endcan
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
@endsection
