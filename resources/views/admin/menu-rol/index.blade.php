@extends('layouts.belectronica.principal')

@section("scripts_content")
    <script src="{{asset("js/portal/admin/menu-rol/index.js")}}" type="text/javascript"></script>
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
              <span class="breadcrumb-item active">Menus rol</span>
            </nav>
          </div><!-- br-pageheader -->
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-lg-12">
                    @include('includes.mensaje')
                    <table width='100%' style="margin-bottom: 40px;">
                        <tr>
                            <td width='50' align="center" valign="top" class="pr-4">
                                <h1 class="text-primary"><i class="fa fa-bars" aria-hidden="true"></i></h1>
                            </td>
                            <td>
                                <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Menús rol</h4>
                                <span class='titulos'>&nbsp;</span>
                            </td>
                        </tr>
                    </table>


                    <div class="card card-success" style="overflow-y: scroll; height: 600px;">
                        <div class="card-body table-responsive p-0">
                            @csrf
                            <table class="table table-striped table-bordered table-hover" id="tabla-data">
                                <thead>
                                    <tr>
                                        <th>Menú</th>
                                        @foreach ($rols as $id => $nombre)
                                        <th class="text-center">{{$nombre}}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($menus as $key => $menu)
                                    @if ($menu["menu_id"] != 0)
                                        @break
                                    @endif
                                        <tr>
                                            <td class="font-weight-bold"><i class="fa fa-arrows-alt"></i> {{$menu["nombre"]}}</td>
                                            @foreach($rols as $id => $nombre)
                                                <td class="text-center">
                                                    <input
                                                    type="checkbox"
                                                    class="menu_rol"
                                                    name="menu_rol[]"
                                                    data-menuid={{$menu[ "id"]}}
                                                    value="{{$id}}" {{in_array($id, array_column($menusRols[$menu["id"]], "id"))? "checked" : ""}}>
                                                </td>
                                            @endforeach
                                        </tr>
                                        @foreach($menu["submenu"] as $key => $hijo)
                                            <tr>
                                                <td class="pl-40"><i class="fa fa-arrow-right"></i> {{ $hijo["nombre"] }}</td>
                                                @foreach($rols as $id => $nombre)
                                                    <td class="text-center">
                                                        <input
                                                        type="checkbox"
                                                        class="menu_rol"
                                                        name="menu_rol[]"
                                                        data-menuid={{$hijo[ "id"]}}
                                                        value="{{$id}}" {{in_array($id, array_column($menusRols[$hijo["id"]], "id"))? "checked" : ""}}>
                                                    </td>
                                                @endforeach
                                            </tr>
                                            @foreach ($hijo["submenu"] as $key => $hijo2)
                                                <tr>
                                                    <td class="pl-50"><i class="fa fa-arrow-right"></i> {{$hijo2["nombre"]}}</td>
                                                    @foreach($rols as $id => $nombre)
                                                        <td class="text-center">
                                                            <input
                                                            type="checkbox"
                                                            class="menu_rol"
                                                            name="menu_rol[]"
                                                            data-menuid={{$hijo2[ "id"]}}
                                                            value="{{$id}}" {{in_array($id, array_column($menusRols[$hijo2["id"]], "id"))? "checked" : ""}}>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                @foreach ($hijo2["submenu"] as $key => $hijo3)
                                                    <tr>
                                                        <td class="pl-60"><i class="fa fa-arrow-right"></i> {{$hijo3["nombre"]}}</td>
                                                        @foreach($rols as $id => $nombre)
                                                        <td class="text-center">
                                                            <input
                                                            type="checkbox"
                                                            class="menu_rol"
                                                            name="menu_rol[]"
                                                            data-menuid={{$hijo3[ "id"]}}
                                                            value="{{$id}}" {{in_array($id, array_column($menusRols[$hijo3["id"]], "id"))? "checked" : ""}}>
                                                        </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    @endforeach
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
@endsection
