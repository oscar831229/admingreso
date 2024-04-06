@extends('layouts.belectronica.principal')

@section('css_custom')
    <link href="{{ asset('css/portal/style-datetable.css') }}" rel="stylesheet">
    <link href="{{ asset('js/portal/income/special-rates/glyphicon.css') }}" rel="stylesheet">
    <link href="{{ asset('js/portal/income/special-rates/index.css') }}" rel="stylesheet">
    <link href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection

@section('scripts_content')
    <script src= "{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/portal/income/special-rates/index.js') }}"></script>
@endsection


@section('content')
    <div class="br-mainpanel">

        <div class="br-pageheader pd-y-15 pd-l-20">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
            <a class="breadcrumb-item" href="#">Ingreso a sedes</a>
            <span class="breadcrumb-item active">Calendario temporada</span>
        </nav>
        </div><!-- br-pageheader -->

        <div class="br-pagebody" style="padding: 0 10px;">
            <div class="br-section-wrapper">

                <table width='100%' style="margin-bottom: 40px;">
                <tr>
                    <td width='50' align="center" valign="top" class="pr-4">
                        <h1 class="text-primary"><i class="fa fa-calendar" aria-hidden="true"></i></h1>
                    </td>
                    <td>
                        <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Calendario días temporada alta</h4>
                        <span class='titulos'><?php echo date('Y-m-d h:m'); ?></span>
                    </td>
                </tr>
                </table>

                <div class="row">
                    <center>
                        <div class="col-sm-12 mb-2">
                            <div class="input-group">
                                {!! Form::text('year', null,['class'=>'form-control form-control-sm input-date', 'placeholder'=>'Año', 'id'=>'year']) !!}
                            </div>
                          </div>
                    </center>
                </div>

                <hr class="mb-4">

                <div class="row">
                    @foreach ($months as $index => $month)
                    <div class="col-sm-4 col-lg-4">
                        <div class="demo" data-month="{{ $month['code'] }}" style="margin-bottom:20px;">
                            <div class="month {{ $month['name_class'] }}">
                                <ul>
                                    <li>{{ $month['name_title'] }}<br>
                                        <span style="font-size:14px" class="current_year"></span>
                                    </li>
                                </ul>
                            </div>
                            <ul class="weekdays">
                                <li>Do</li>
                                <li>Lu</li>
                                <li>Ma</li>
                                <li>Mi</li>
                                <li>Ju</li>
                                <li>Vi</li>
                                <li>Sa</li>
                            </ul>
                            <ul class="days">
                            </ul>
                        </div><!-- End of demo code -->
                    </div> <!-- cols-sm-4-->
                    @endforeach
                </div>
            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->
    </div>

    <div class="card menu-opciones" style="width: 18rem;">
        <div class="card-body" style="padding: 0.25rem; font-size: 12px;">
          <h6 class="card-title">Opciones:</h6>
          <ul class="list-group">
            <li class="delete list-group-item">Eliminar</li>
            <li class="new list-group-item">Marca temporada alta</li>
          </ul>
        </div>
    </div>

@endsection
