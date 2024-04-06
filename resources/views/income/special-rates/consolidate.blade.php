@extends('layouts.portal.principal')

@section('css_custom')
  
  <link href="{{ asset('js/plugins/bootstrap-select/bootstrap-select.css') }}" rel="stylesheet">
  <link href="{{ asset('js/plugins/bootstrap-fileinput/css/fileinput.min.css') }}" rel="stylesheet">
  <link href="{{ asset('js/plugins/multi-select/bootstrap-multiselect.css') }}" rel="stylesheet">
  <style>
    @font-face {
      font-family: "Glyphicons Halflings";
      src: url(../fonts/glyphicons-halflings-regular.eot);
      src: url(../fonts/glyphicons-halflings-regular.eot?#iefix) format("embedded-opentype"), url(../fonts/glyphicons-halflings-regular.woff) format("woff"), url(../fonts/glyphicons-halflings-regular.ttf) format("truetype"),
      url(../fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular) format("svg");
    }
    .li-check{
      cursor: pointer;
    }
    .li-view-statistics {
      cursor: pointer;
    }
  </style>
  <link href="{{ asset('js/portal/entity/schedule-availabilities/glyphicon.css') }}" rel="stylesheet">
  <link href="{{ asset('js/portal/entity/schedule-availabilities/index.css') }}" rel="stylesheet">
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
  <script src="{{ asset('js/plugins/multi-select/bootstrap-multiselect.js') }}"></script>
  <script src="{{ asset('js/portal/entity/schedule-availabilities/index.js') }}"></script>
  <script>
    $(function () {
      
      $('#md-specialty').on('hidden.bs.modal', function () {

        var specialties = calendario.getSpecialtiesSelected();
        if(specialties.length == 0){
          specialties.push('000')
        }

        var validity = $('#validity').val();
        var current_year = new Date().getFullYear();

        if(validity > current_year){
          var current_day_stamp = Math.floor(new Date(validity, 0, 1).getTime() / 1000);
          var last_day = new Date(nvalidity, 11, 31);
          var last_day_stamp = Math.floor(last_day.getTime() / 1000);
        }else{
          var current_day_stamp = Math.floor(new Date().getTime() / 1000);
          var last_day = new Date(new Date().getFullYear(), 11, 31);
          var last_day_stamp = Math.floor(last_day.getTime() / 1000);
        }

        btn.loading($('#btn-especiality'));

        setTimeout(function(){
          $.ajax({
            url: 'consolidate-events-schedule-availabilities',
            async: false,
            data: {
              start : current_day_stamp,
              end : last_day_stamp,
              view: 'month_consolidated',
              specialties : specialties,
							_token: $('input[name=_token]').val(),
              schedule_id : 0
            },		
            beforeSend: function(objeto){
                
            },        
            complete: function(objeto, exito){
              btn.reset($('#btn-especiality'));
                if(exito != "success"){
                    alert("No se completo el proceso!");
                }            
            },
            contentType: "application/x-www-form-urlencoded",
            dataType: "json",
            error: function(objeto, quepaso, otroobj){
                alert("Ocurrio el siguiente error: "+quepaso);
            },
            global: true,
            ifModified: false,
            processData:true,
            success: function(response){
                
                if(!response.success){
                    Biblioteca.notificaciones(response.message, 'Disponibilidad por especialidades', 'error');
                    return false;
                }

                consolidado.setData(response.result);
                consolidado.generarTable();
                $(".tooltip").tooltip("hide");

            },
            timeout: 30000,
            type: 'POST'
        });
        },80)

        
      });

      $('body').on('click', '.li-check', function(){
         var date = $(this).data('day').toString();
         var year  = date.substr(0,4);
         var month = date.substr(4,2);
         var day   = date.substr(6,2);
         var date_complete = year + '-' + month + '-' + day;
         var specialties_selected = $(this).data('specialty_code').toString();
         window.open("calendar-schedule-availabilities/" + date_complete + "/" + specialties_selected, "_blank");
      })

      $('body').on('click', '.li-view-statistics', function(){

        var date = $(this).data('day');
        var specialty_code = $(this).data('specialty_code');
        var btnaction = $(this);
        consolidado.specialty_name = $(this).data('name');
        consolidado.specialty_text = $(this).data('text');
        consolidado.specialty_available = parseInt($(this).data('disponible'));
        

        swal({
          title: 'Consultar estadistica de especialidad',
          text: "¿Esta seguro de continuar con el proceso?",
          icon: 'warning',
          showConfirmButton:false,
          buttons: {
              Aceptar: {
                  text: "Aceptar",
                  value: 'Aceptar',
                  visible: true
              },
              cancel: true
          },
        }).then((value) => {
            if (value) {

              btn.loading(btnaction);

                setTimeout(function(){
                  $.ajax({
                    url: 'statistics-specialty-period/' + date + '/'  + specialty_code,
                    async: false,
                    data: {},		
                    beforeSend: function(objeto){
                        
                    },        
                    complete: function(objeto, exito){
                      btn.reset(btnaction);
                        if(exito != "success"){
                            alert("No se completo el proceso!");
                        }            
                    },
                    contentType: "application/x-www-form-urlencoded",
                    dataType: "json",
                    error: function(objeto, quepaso, otroobj){
                        alert("Ocurrio el siguiente error: "+quepaso);
                    },
                    global: true,
                    ifModified: false,
                    processData:true,
                    success: function(response){
                        
                        if(!response.success){
                            Biblioteca.notificaciones(response.message, 'Estadisticas especialidad', 'error');
                            return false;
                        }

                        consolidado.setStatistics(response.data);
                        consolidado.viewStatisticsTable();

                        $(".tooltip").tooltip("hide");

                    },
                    timeout: 30000,
                    type: 'GET'
                });
              },80)

            } 
        });
     })
      


    });

    consolidado = {
      
      periods_name : ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'],

      data : [],

      generarTable : function(){
        this.generateHead();
        this.generateBody();
      },

      datastatistics : [],

      specialty_name : '',

      specialty_available : 0,

      specialty_text : '',

      setStatistics : function(data){
        this.datastatistics = data;
      },

      viewStatisticsTable : function(){

        // Cargar profesionales tbl-professional
        var tr = '';
        var number = 1;
        var porcentage = '';

        $('#text-value').html(consolidado.specialty_text);

        $.each(this.datastatistics.professional, function(index, value){

          porcentage = `<div class="progress">
            <div class="progress-bar ${value.class_state} wd-${value.OCUPACION_PORCE}p" role="progressbar" style="width: ${value.OCUPACION_PORCE}%;" aria-valuenow="${value.OCUPACION_PORCE}" aria-valuemin="0" aria-valuemax="100">${value.OCUPACION_PORCE}%</div>
          </div>`

          tr += `<tr>
            <td>${number}</td>
            <td>${value.NOMMEDICO}</td>
            <td class="text-right">${value.DISPO_MINUT}</td>
            <td class="text-right">${value.AGEND_MINUT}</td>
            <td class="text-right">${value.SALDO_DISPONIBLE}</td>
            <td class="text-right">${porcentage}</td>
          </tr>`;

        });

        $('#tbl-professional tbody').html(tr);
        tr = '';
        number = 1;

        // Cargar actividades tbl-activities
        var available_number = 0;
        $.each(this.datastatistics.activities, function(index, value){

          available_number = parseInt(consolidado.specialty_available / value.DURAACTIV);
          tr += `<tr>
            <td>${number}</td>
            <td>${value.CODACTMED}</td>
            <td>${value.DESACTMED}</td>
            <td class="text-right">${value.CANTIDAD}</td>
            <td class="text-right">${value.DURAACTIV}</td>
            <td class="text-right">${available_number}</td>
          </tr>`;
        });

        $('#tbl-activities tbody').html(tr);

        $('#md-specialty-detail').modal()

      },

      generateHead(){

        var head = [];
        head.push('#')
        head.push('ESPECIALIDAD')
        if(this.data.length > 0){
          var periods = this.data[0].periods
          $.each(periods, function(key, value){
            var number_period = value.period;
            var index = parseInt(number_period.substr(4,2)) - 1;
            var name_periodo = consolidado.periods_name[index];
            head.push(name_periodo);
          })
        }

        var thead = '<tr>';
        $.each(head, function(key, value){
          thead += '<th>'+value+'</th>';
        });
        thead += '</tr>';

        $('#tbl-specialties thead').empty();
        $('#tbl-specialties thead').html(thead);

      },

      generateBody(){
        
        if(this.data.length > 0){
          var number = 1;
          var tr = '';
          $.each(this.data, function(key, spec){
            tr +='<tr>'
               + '<th scope="row">'+number+'</th>'
               + '<td>'+spec.specialty_name+'</td>';
              $.each(spec.periods, function(kaysub, subspec){

                var disponibilidad = parseInt(subspec.minutes_available) - parseInt(subspec.scheduled_minutes);

                html_sub = '&nbsp;';
                
                if(subspec.minutes_available != 0){

                  var text = `${spec.specialty_name} - ${subspec.period}  Disponibilidad[ ${disponibilidad} ] (${subspec.minutes_available} Agenda - ${subspec.scheduled_minutes} Citas)`;
                  
                  html_sub = `<label class="tx-12 mg-b-10">Disponibilidad (${subspec.minutes_available} Agen - ${subspec.scheduled_minutes} Cita)
                    <span class="li-check text-primary" data-day="${subspec.period}01" data-specialty_code="${spec.specialty_code}">
                      <i class="fa fa-calendar ml-2" aria-hidden="true" title="Consultar detalle especialidad"></i>
                    </span>
                    <span class="li-view-statistics text-primary" data-day="${subspec.period}" data-specialty_code="${spec.specialty_code}" data-name="${spec.specialty_name}" data-disponible="${disponibilidad}" data-text="${text}">
                      <i class="fa fa-line-chart ml-2" aria-hidden="true" title="Consultar estadistica especialidad"></i>
                    </span>
                  </label>
                  <div class="progress">
                    <div class="progress-bar ${subspec.class_state} wd-${subspec.occupancy_percentage}p" role="progressbar" style="width: ${subspec.occupancy_percentage}%;" aria-valuenow="${subspec.occupancy_percentage}" aria-valuemin="0" aria-valuemax="100">${subspec.occupancy_percentage}%</div>
                  </div>`;
                }
                tr += '<td>' + html_sub + '</td>'
              });
              tr += '</tr>';
              number++;
          })

          $('#tbl-specialties tbody').empty();
          $('#tbl-specialties tbody').html(tr);

        }

      },

      setData : function(data){
        this.data = data;
      }
    }


    btn = {

      loading : function(element){
          var loadingText = '<i class="fa fa-spinner fa-spin"></i> Procesando...';
          if ($(element).html() !== loadingText) {
              $(element).data('original-text', $(element).html());
              $(element).html(loadingText);
              $(element).prop( "disabled", true );
          }
      },
  
      reset : function(element){
          $(element).html($(element).data('original-text'));
          $(element).prop( "disabled", false );
      }
  
    }
  </script>
@endsection


@section('content')
  <div class="br-mainpanel">

    @include('includes/mensaje')
    @include('includes/form-error')

    <div class="br-pageheader pd-y-15 pd-l-20">
      <nav class="breadcrumb pd-0 mg-0 tx-12">
        <a class="breadcrumb-item" href="#">Tablero entidades</a>
        <span class="breadcrumb-item active">Consolidado disponibilidad de agendas</span>
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
                  <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">Consolidado disponibilidad agendas</h4>
                  <span class='titulos'><?php echo date('Y-m-d h:m'); ?></span>
              </td>
          </tr>
        </table>

        <div class="card-body table-responsive p-0">
          <div class="mb-4">
            <div class="row">
              <div class="col-sm-2">
                <div class="form-group">
                  <label for="validity"><i class="fa fa-binoculars mr-2" aria-hidden="true"></i>Vigencia</label>
                  {!! Form::select('validity', $validities, $year_end, array('class' => 'form-control form-control-sm','data-year' => $year_end, 'id'=>'validity', 'placeholder' => 'Seleccione..')) !!}
                </div>
              </div>
              <div class="col-sm-2">
                <div class="form-group">
                  <label for="specialties"><i class="fa fa-modx mr-2" aria-hidden="true"></i>Especialidades</label> 
                  <a href="javascript:void(0)" id="btn-especiality" class="btn btn-block btn-outline-primary btn-sm"><i class="fa fa-plus-square-o mr-2" aria-hidden="true"></i>Seleccionar..</a>
                </div>
              </div>
              <div class="col-sm-2" style="display: none;">
                <div class="form-group">
                  <label for="months"><i class="fa fa-calendar mr-2" aria-hidden="true"></i>Meses</label>
                  <a href="javascript:void(0)" id="btn-months" class="btn btn-block btn-outline-primary btn-sm"><i class="fa fa-plus-square-o mr-2" aria-hidden="true"></i>Seleccionar..</a>
                </div>
              </div>
              <div class="col-sm-2" style="display: none;">
                <div class="form-group">
                  <label for="cost_center_erp_id">&nbsp;</label> 
                  <a href="javascript:void(0)" id="btn-refresh" class="btn btn-block btn-outline-secondary btn-sm"><i class="fa fa-refresh mr-2" aria-hidden="true"></i>refrescar</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12"><h5><i class="fa fa-calendar text-primary mr-2" aria-hidden="true"></i> Calendario <span class="current_year"></span></h5><hr class="mb-4"></div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="bd bd-gray-300 rounded table-responsive">
              <table class="table table-striped mg-b-0 table-bordered" id="tbl-specialties">
                <thead class="">
                  <tr>
                    <th>#</th>
                    <th>Especialidad</th>
                    <th>Enero</th>
                    <th>Febrero</th>
                    <th>Marzo</th>
                    <th>Abril</th>
                    <th>Mayo</th>
                    <th>Junio</th>
                    <th>Julio</th>
                    <th>Agosto</th>
                    <th>Septiembre</th>
                    <th>Octubre</th>
                    <th>Noviembre</th>
                    <th>Diciembre</th>
                  </tr>
                </thead>
                <tbody>
                  {{--  <tr>
                    <th scope="row">1</th>
                    <td>CARDIOLOGIA PEDIATRICA</td>
                    <td>
                      <label class="tx-12 mg-b-10">Disponibildia (360 Agen - 200 Cita)</label>
                      <div class="progress">
                        <div class="progress-bar wd-25p" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                      </div>
                    </td>
                    <td>
                      <label class="tx-12 mg-b-10">Disponibildia (360 Agen - 200 Cita)</label>
                      <div class="progress">
                        <div class="progress-bar wd-25p" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                      </div>
                    </td>
                    <td>
                     <label class="tx-12 mg-b-10">Disponibildia (360 Agen - 200 Cita)</label>
                      <div class="progress">
                        <div class="progress-bar wd-25p" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                      </div>
                    </td>
                    <td>
                     <label class="tx-12 mg-b-10">Disponibildia (360 Agen - 200 Cita)</label>
                      <div class="progress">
                        <div class="progress-bar wd-25p" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                      </div>
                    </td>
                    <td>
                     <label class="tx-12 mg-b-10">Disponibildia (360 Agen - 200 Cita)</label>
                      <div class="progress">
                        <div class="progress-bar wd-25p" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                      </div>
                    </td>
                    <td>
                     <label class="tx-12 mg-b-10">Disponibildia (360 Agen - 200 Cita)</label>
                      <div class="progress">
                        <div class="progress-bar wd-25p" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                      </div>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                     <label class="tx-12 mg-b-10">Disponibildia (360 Agen - 200 Cita)</label>
                      <div class="progress">
                        <div class="progress-bar wd-25p" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                      </div>
                    </td>
                    <td>
                     <label class="tx-12 mg-b-10">Disponibildia (360 Agen - 200 Cita)</label>
                      <div class="progress">
                        <div class="progress-bar wd-25p" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                      </div>
                    </td>
                    <td>
                     <label class="tx-12 mg-b-10">Disponibildia (360 Agen - 200 Cita)</label>
                      <div class="progress">
                        <div class="progress-bar wd-25p" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                      </div>
                    </td>
                    <td>
                     <label class="tx-12 mg-b-10">Disponibildia (360 Agen - 200 Cita)</label>
                      <div class="progress">
                        <div class="progress-bar wd-25p" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                      </div>
                    </td>
                    <td>
                     <label class="tx-12 mg-b-10">Disponibildia (360 Agen - 200 Cita)</label>
                      <div class="progress">
                        <div class="progress-bar wd-25p" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                      </div>
                    </td>
                  </tr>  --}}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->
  </div>

  {{--  Selector de meses  --}}
  <div class="modal fullscreen-modal modal-months fade" id="md-months" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-white bg-primary">
          <h6 class="modal-title ml-4 mr-4" id="exampleModalLabel"><i class="fa fa-calendar mr-2" aria-hidden="true"></i>MESES VISUALIZADOS</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" >
          <div class="row">
            <div class="col-sm-12">
              <div class="well" style="max-height: 300px;overflow: auto;">
                <ul class="list-group checked-list-box list-months" data-alias="chk-months">
                  <li class="list-group-item" data-checked="false" data-month="1">Enero</li>
                  <li class="list-group-item" data-checked="false" data-month="2">Febrero</li>
                  <li class="list-group-item" data-checked="false" data-month="3">Marzo</li>
                  <li class="list-group-item" data-checked="false" data-month="4">Abril</li>
                  <li class="list-group-item" data-checked="false" data-month="5">Mayo</li>
                  <li class="list-group-item" data-checked="false" data-month="6">Junio</li>
                  <li class="list-group-item" data-checked="false" data-month="7">Julio</li>
                  <li class="list-group-item" data-checked="false" data-month="8">Agosto</li>
                  <li class="list-group-item" data-checked="false" data-month="9">Septiembre</li>
                  <li class="list-group-item" data-checked="false" data-month="10">Octubre</li>
                  <li class="list-group-item" data-checked="false" data-month="11">Noviembre</li>
                  <li class="list-group-item" data-checked="false" data-month="12">Diciembre</li>
                </ul>
              </div>
            </div>
          </div> 
        </div>
      </div>
    </div>
  </div>
  {{--  Fin selector de meses --}}

  {{--  Detalle agenda especialidad  --}}
  <div class="modal fade" id="md-specialty-detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header text-white bg-primary">
          <h6 class="modal-title ml-4 mr-4" id="exampleModalLabel"><i class="fa fa-calendar mr-2" aria-hidden="true"></i>DETALLE DE AGENDA ESPECIALIDAD</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" >
          <h6 class="tx-inverse"><strong><span id="text-value"></span></strong></h6>
          <br>
          <h6 class="tx-inverse text-primary"><i class="fa fa-user-md mr-2" aria-hidden="true"></i>Profesionales</h6>
          <div class="bd rounded table-responsive">
            <table class="table table-bordered mg-b-0" id="tbl-professional">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nombre</th>
                  <th>Tiempo agenda</th>
                  <th>Tiempo programado</th>
                  <th>Tiempo disponible</th>
                  <th>% agendamiento</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <br>
          <h6 class="tx-inverse text-primary"><i class="fa fa-star mr-2" aria-hidden="true"></i>Actividades</h6>
          <div class="bd rounded table-responsive">
            <table class="table table-bordered mg-b-0" id="tbl-activities">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Cod</th>
                  <th>Actividad</th>
                  <th class="text-center">Citas</th>
                  <th class="text-center">Tiempo</th>
                  <th class="text-center">Posibles</th>
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
  {{--  Fin detalle agenda especialidad --}}


  {{--  Modal selección especialidades  --}}
  @include('entity.schedule-availabilities._modal_specialty', ['specialties' => $specialties])

@endsection