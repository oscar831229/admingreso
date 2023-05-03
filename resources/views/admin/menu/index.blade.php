@extends('layouts.portal.principal')

@section("css_custom")
    <link href="{{asset("js/plugins/jquery-nestable/jquery.nestable.css")}}" rel="stylesheet" type="text/css" />
@endsection

@section("scriptsPlugins")
    <script src="{{asset("js/plugins/jquery-nestable/jquery.nestable.js")}}" type="text/javascript"></script>
@endsection

@section("scripts_content")
    <script src="{{asset("js/portal/admin/menu/index.js")}}" type="text/javascript"></script>
@endsection


@section('content')
<div class="br-mainpanel">
    @include('includes.form-error')
    @include('includes.mensaje')
      <div class="br-pageheader pd-y-15 pd-l-20">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
          <a class="breadcrumb-item" href="#">Panel administración</a>
          <span class="breadcrumb-item active">Menus</span>
        </nav>
      </div><!-- br-pageheader -->

      <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
        <table width='100%' style="margin-bottom: 10px;" class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
            <tr>
                <td width='50' align="center" valign="top" class="pr-4">
                    <h1 class="text-primary"><i class="fa fa-bars" aria-hidden="true"></i></h1>
                </td>
                <td>
                    <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Menus</h4>
                    <span class='titulos'><?php echo date('Y-m-d h:m'); ?></span>
                </td>
            </tr>
          </table>

      </div>
      
      <div class="br-pagebody">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-info">
                    <div class="card-body">
                        @csrf
                        <div class="dd" id="nestable">
                            <ol class="dd-list">
                                @foreach ($menus as $key => $item)
                                    @if ($item["menu_id"] != 0)
                                        @break
                                    @endif
                                    @include("admin.menu.menu-item",["item" => $item])
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>          
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