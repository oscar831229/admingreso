@extends('layouts.portal.principal')

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
  <div class="br-mainpanel">
    @include('includes/mensaje')
    @include('includes/form-error')
    <div class="br-pageheader pd-y-15 pd-l-20">
      <nav class="breadcrumb pd-0 mg-0 tx-12">
        <a class="breadcrumb-item" href="#">Panel administración</a>
        <span class="breadcrumb-item active">Email</span>
      </nav>
    </div><!-- br-pageheader -->

    

    <div class="br-pagebody" style="padding: 0 10px;">
      <div class="br-section-wrapper">

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

        <div class="card-body table-responsive p-0">
          <table class="table table-hover width60" id="tabla-data">
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

        {{-- <div class="table-wrapper">
          
          <table id="emails" class="table display responsive nowrap dataTable no-footer dtr-inline" role="grid" aria-describedby="datatable1_info" style="width: 1110px;">
            <thead>
              <tr role="row">
                <th class="wd-15p sorting_asc" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" style="width: 20px;" aria-sort="ascending" aria-label="First name: activate to sort column descending">#</th>
                <th class="wd-20p sorting" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" style="width: 170px;" aria-label="Usuario: activate to sort column ascending">Email</th>
                <th class="wd-15p sorting" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" style="width: 142px;" aria-label="Start date: activate to sort column ascending">Servidor smtp</th>
                <th class="wd-10p sorting" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" style="width: 87px;" aria-label="Salary: activate to sort column ascending">Encriptación</th>
                <th class="wd-25p sorting" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" style="width: 45px;" aria-label="E-mail: activate to sort column ascending">Puerto</th>
                <th class="wd-25p sorting" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" style="width: 45px;" aria-label="E-mail: activate to sort column ascending"></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($emails as $key => $email)
                <tr>
                  <td>{{ $key+1 }}</td>
                  <td>{{ $email->email }}</td>
                  <td>{{ $email->server }}</td>
                  <td>{{ $email->encryption }}</td>
                  <td>{{ $email->puerto }}</td>
                  <td>
                    
                    @if ($email->getPlantillas()->count() == 0)
                    {!! Form::open(['method' => 'DELETE','route' => ['emails.destroy', $email->id],'style'=>'display:inline', 'onsubmit'=>"return emails.delete(this)"]) !!}
                      {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                    {!! Form::close() !!} 
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table> <!-- table-wrapper --> --}}
        </div>
      </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->

  </div>

  <!-- MODAL -->
  <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    
    <div class="modal-dialog" role="document" style="width: 50%">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirm.titulo">Confirmación</h5>
        </div>
        <div class="modal-body">
          <p>¿Se va a eliminar la cuenta correo, desea continuar?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="emails.anular()">Aceptar</button>
        </div>
      </div>
    </div>
    
  </div>
  <!-- FIN MODAL -->


@endsection
