@extends('layouts.portal.principal')

@section('titulo')
    Sistema Menús
@endsection

@section("scripts_content")
    <script src="{{asset("js/portal/admin/menu/crear.js")}}" type="text/javascript"></script>
@endsection

@section('content')
<div class="br-mainpanel">
    @include('includes/mensaje')
    @include('includes/form-error')
    <div class="br-pageheader pd-y-15 pd-l-20">
    <nav class="breadcrumb pd-0 mg-0 tx-12">
        <a class="breadcrumb-item" href="{{route('menu')}}">Menus</a>
        <span class="breadcrumb-item active">Editar Menú</span>
    </nav>
    </div><!-- br-pageheader -->

    <div class="br-pagebody">
        <div class="br-section-wrapper">
            <div class="table-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-danger">
                            <form action="{{route('actualizar_menu', ['id' => $data->id])}}" id="form-general" class="form-horizontal form--label-right" method="POST" autocomplete="off">
                                @csrf @method("put")
                                <div class="card-body">
                                    @include('admin.menu.form')
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-lg-3"></div>
                                        <div class="col-lg-6">
                                            @include('includes.boton-form-editar')
                                        </div>
                                    </div>
                                </div>
                            </form>
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
