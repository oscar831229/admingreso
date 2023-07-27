@extends('layouts.belectronica.principal')

@section('css_custom')
  <link href="{{ asset('css/portal/style-datetable.css') }}" rel="stylesheet">
  <style>
    #morecsspure-trigger-toggle { cursor: pointer; }
    #morecsspure-element-toggle { display:none; }
    #morecsspure-element-toggle:not(:checked) ~ #morecsspure-toggled-element { display:none; }
    #morecsspure-element-toggle:not(:checked) ~ #morecsspure-trigger-toggle .morecsspure-lesslink { display:none; }
    #morecsspure-element-toggle:checked ~ #morecsspure-abstract { display:none; }
    #morecsspure-element-toggle:checked ~ #morecsspure-trigger-toggle .morecsspure-morelink { display:none; }
    #morecsspure .morecsspure-morelink, #morecsspure .morecsspure-lesslink { display: block; cursor: pointer; color:#2196f3; }
    #morecsspure .morecsspure-morelink:hover, #morecsspure .morecsspure-lesslink:hover { text-decoration:underline; }
  </style>
  <style>

    @media (min-width: 1200px) {

      .modal-lg, .modal-xl {
        width: 1170px;
        max-width: 1170px;
      }
    }
  </style>
  
  <style>
    .table td {
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
  <link href="{{ asset('js/plugins/bootstrap-select/bootstrap-select.css') }}" rel="stylesheet">
  <link href="{{ asset('js/plugins/bootstrap-fileinput/css/fileinput.min.css') }}" rel="stylesheet">
  <style>
    .nav-pills-custom .nav-link {
      color: #aaa;
      background: #fff;
      position: relative;
    }
    
    .nav-pills-custom .nav-link.active {
        color: #45b649;
        background: #fff;
    }

    /* Add indicator arrow for the active tab */
    @media (min-width: 992px) {
      .nav-pills-custom .nav-link::before {
          content: '';
          display: block;
          border-top: 8px solid transparent;
          border-left: 10px solid #fff;
          border-bottom: 8px solid transparent;
          position: absolute;
          top: 50%;
          right: -10px;
          transform: translateY(-50%);
          opacity: 0;
      }
    }

    .nav-pills-custom .nav-link.active::before {
      opacity: 1;
    }
    .note-editor .note-editable {
      line-height: 1.2;
      font-size: 18px;
    }
    #tbl-tracking td p, #tbl-tracking td h6 {
      font-size: 12px !important;
    }
  </style>
@endsection

@section('scripts_content')
  <script src="{{ asset('theme/lib/internacionalizacion/es.js') }}"></script>
  <script src="{{ asset('theme/lib/datatables/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('js/plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>

  <!-- include summernote css/js -->
  <link href="{{ asset('theme/lib/summernote/summernote.min.css') }}" rel="stylesheet">
  <script src="{{ asset('theme/lib/summernote/summernote.min.js') }}"></script>
  <script src="{{ asset('theme/lib/summernote/lang/summernote-es-ES.min.js') }}"></script>

  <script src="{{ asset('js/portal/wallet/wallet-users/index.js') }}"></script>
  <script src="{{ asset('js/plugins/bootstrap-fileinput/js/fileinput.min.js') }}"></script>
  <script src="{{ asset('js/plugins/bootstrap-fileinput/js/locales/es.js') }}"></script>
@endsection


@section('content')
  <div class="br-mainpanel">

    @include('includes/mensaje')
    @include('includes/form-error')

    <div class="br-pageheader pd-y-15 pd-l-20">
      <nav class="breadcrumb pd-0 mg-0 tx-12">
        <a class="breadcrumb-item" href="#">Billetera</a>
        <span class="breadcrumb-item active">Usuarios billetera electrónica</span>
      </nav>
    </div><!-- br-pageheader -->

    <div class="br-pagebody" style="padding: 0 10px;">
      <div class="br-section-wrapper">

        <table width='100%' style="margin-bottom: 40px;">
          <tr>
              <td width='50' align="center" valign="top" class="pr-4">
                  <h1 class="text-primary"><i class="fa fa-bell-o" aria-hidden="true"></i></h1>
              </td>
              <td>
                  <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Usuarios billetera electrónica</h4>
                  <span class='titulos'><?php echo date('Y-m-d h:m'); ?></span>
              </td>
          </tr>
        </table>
        @csrf

        <div class="card-body table-responsive p-0">
          <table class="table table-hover" id="tbl-alert-donor" style="width: 100% !important;">
              <thead>
                  <tr>
                    <th class="search-disabled" style="width: 2%">#</th>
                    <th style="width: 5%">Identificación</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Telefono</th>
                    <th style="width: 10%">Fecha registro</th>
                    <th style="width: 10%">Usuario registro</th>
                    <th class="text-center search-disabled" style="width: 10%"></th>
                </tr>
              </thead>
              <tbody>
              </tbody>
          </table>
        </div>
        
      </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->
  </div>


  <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;" id="md-info-customer">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header text-white bg-primary">
          <h4 class="modal-title" id="myModalLabel"><i class="fa fa-credit-card-alt mr-2" aria-hidden="true"></i>Estado de cuenta</h4>
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="col-md-12 col-sm-12  profile_details">
            <div class="well profile_view" style="width: 100%;">
              <div class="col-sm-12">
                <div class="left col-md-6 col-sm-6">
                  <h2><i class="fa fa-user mr-1" aria-hidden="true"></i><span id="customer_name"></span></h2>
                  <ul class="list-unstyled">
                    <li><i class="fa fa-phone"></i> Teléfono # :<span id="customer_phone"></span></li>
                    <li><i class="fa fa-envelope"></i> Email : <span id="customer_email"></span></li>
                  </ul>
                </div>
                <div class="left col-md-6 col-sm-6">
                  <div class="table-responsive">
                    <table class="table table-striped jambo_table bulk_action" id="tbl-pocket">
                      <thead>
                        <tr class="headings">
                          <th class="column-title">Bolsillo</th>
                          <th class="column-title">Saldo</th>
                          <th class="column-title no-link last"><span class="nobr">Action</span></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="even pointer">
                          <td class="a-center ">
                            <div class="icheckbox_flat-green" style="position: relative;"><input type="checkbox" class="flat" name="table_records" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                          </td>
                          <td></td>
                          <td></td>
                          <td></td>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="right col-md-5 col-sm-5 text-center">
                  <img src="images/img.jpg" alt="" class="img-circle img-fluid">
                </div>
              </div>
            </div>
          </div>
          <div class="row mg-b-25">
            <div class="col-sm-12 col-lg-12">
                <div class="row mg-b-25 person-basic" style="margin-top: 20px;">
                    <div class="col-sm-12 col-lg-12">
                        <center><h2 class="tx-gray-800 headings"><i class="fa fa-table mr-2" aria-hidden="true"></i><span id="pocket_name"></span></h2></center>
                        <h6 class="tx-gray-800"><i class="fa fa-history mr-2" aria-hidden="true"></i>Historico de movimientos</h6>
                        <div class="card-body table-responsive p-0 mb-4">
                            <table class="table table-hover width60" id="tbl-waller-user-movements">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cuenta</th>
                                        <th>Cedula</th>
                                        <th>Movimiento</th>
                                        <th>valor</th>
                                        <th>Usuario</th>
                                        <th>Comercio</th>
                                        <th>CUS</th>
                                        <th>Fecha transaccion</th>
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
        <div class="row" id="row-tickets" style="display: none;">
          <div class="col-sm-12">
            <h2 class="tx-gray-800 headings"><i class="fa fa-tablet mr-2" aria-hidden="true"></i><span id="pocket_name">Tiquetera pendiente de redimir</span></h2>
            <div class="card-body table-responsive p-0 mb-4">
              <table class="table table-hover width60" id="tbl-tickets">
                  <thead>
                      <tr>
                          <th>#</th>
                          <th>Número</th>
                          <th>Valor</th>
                          <th>CUS</th>
                          <th>Codigo Usuario</th>
                          <th>Fecha transaccion</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
          </div>
          </div>
        </div>
        </div>
        <div class="modal-footer">
        </div>
      </div>
    </div>
  </div>

  

@endsection