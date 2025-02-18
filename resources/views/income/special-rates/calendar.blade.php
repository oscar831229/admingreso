@extends('layouts.portal.principal')

@section('css_custom')
	
	
	{{--  <style type="text/css">
		body {
			margin: 0px 0px;
			padding: 0;
			font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
			font-size: 14px;
		}
	</style>  --}}
	<link href="{{ asset('css/portal/style-datetable.css') }}" rel="stylesheet">
	<link href="{{ asset('js/plugins/bootstrap-select/bootstrap-select.css') }}" rel="stylesheet">
	<link href="{{ asset('js/plugins/bootstrap-fileinput/css/fileinput.min.css') }}" rel="stylesheet">
	<link href="{{ asset('js/plugins/multi-select/bootstrap-multiselect.css') }}" rel="stylesheet">
	<link href="{{ asset('js/portal/entity/schedule-availabilities/glyphicon.css') }}" rel="stylesheet">
	<link href="{{ asset('js/portal/entity/schedule-availabilities/index.css') }}" rel="stylesheet">
	<link href="{{ asset('calendario/css/fullcalendar.min.css') }}" rel="stylesheet" />
	<link href="{{ asset('calendario/css/fullcalendar.print.min.css') }}" rel="stylesheet" media="print" />
	<link href="{{ asset('calendario/css/personalizado.css') }}" rel="stylesheet" />
	<link href="{{ asset('js/portal/entity/schedule-availabilities/calendar.css') }}" rel="stylesheet">
	<link href="{{ asset('css/portal/style-datetable.css') }}" rel="stylesheet">

	<style>
		.fc-event, .fc-event:hover {
			color: #fff !important;
			text-decoration: none;
		}
		.status_schemdule {
			margin-right: 4px;
			margin-left: 4px;
			color: #ffff;
			font-size: .79em !important;
			line-height: 1.3;
			font-weight: 700;
			border-color: black;
			border: .12em solid black;
			letter-spacing: 1px;
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
@endsection


@section('scripts_content')
	<script src="{{ asset('calendario/js/moment.min.js') }}""></script>
	<script src="{{ asset('calendario/js/fullcalendar.min.js') }}""></script>
	<script src="{{ asset('calendario/locale/es-es.js') }}"></script>
	<script src="{{ asset('js/plugins/multi-select/bootstrap-multiselect.js') }}"></script>
  	<script src="{{ asset('js/portal/entity/schedule-availabilities/index.js') }}"></script>
	<script src="{{ asset('js/portal/entity/schedule-availabilities/calendar.js') }}"></script>
	<script>
		$(document).ready(function() {
			calendario.setSpecialtiesInit(<?php echo json_encode($specialties_check) ?>);
			calendario.initializeCheckSpecialties();
		});
	</script>
	<script>
		$(document).ready(function() {

			var curren_date = '<?php echo $current_date ?>';
			
			$('#calendar').fullCalendar({
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaDay'
				},
				defaultDate: curren_date,
				businessHours: [ // specify an array instead
					{
						daysOfWeek: [1, 2, 3, 4, 5, 6],
						startTime: '09:00', // 8am
						endTime: '21:00' // 6pm
					}
				],
				minTime: "06:00",
    			maxTime: "21:00",
				timeZone: 'America/Bogota',
				navLinks: true, // can click day/week names to navigate views
				editable: true,
				eventLimit: true, // allow "more" link when too many events
				lazyFetching: false,
				eventClick: function(event) {

					calendario.searchSchedule_id = event.id,
					calendario.searchSchedule_date = event.start.format('YYYY-MM-DD'),

					$('#view-agenda').hide();
					$('#view-cita').hide();

					if(event.type == 'agenda'){

						$('#label-type').html('INFORMACIÓN AGENDA')
						$('#visualizar #id').text(event.id);
						$('#visualizar #title').text(event.title);
						$('#visualizar #start').text(event.start.format('DD/MM/YYYY HH:mm:ss'));
						$('#visualizar #end').text(event.end.format('DD/MM/YYYY HH:mm:ss'));
						$('#visualizar #professional').text(event.professional);
						$('#visualizar #schedule_time').text(event.schedule_time);
						$('#visualizar #time_dating').text(event.time_dating);
						$('#visualizar #available_time').text(event.available_time);

						var div = `<div class="progress mg-b-10">
						  <div class="progress-bar ${event.contentclass} wd-${event.percentage_value}p" role="progressbar" style="width: ${event.percentage_value}%;" aria-valuenow="${event.percentage_value}" aria-valuemin="0" aria-valuemax="100">${event.percentage_value}%</div>
						</div>`
						$('#visualizar #progress').html(div);

						

						var tr = '';
						var number = 1;
						var numdis = 0;
						$.each(event.schedule_activities, function(index, value){
							
							numdis = event.available_time == 0 ? 0 : event.available_time / value.DURAACTIV;

							tr += '<tr>'
							 	+ '  <td>'+ number +'</td>'
								+ '  <td>'+ value.DESACTMED +'</td>'
								+ '  <td class="text-center">'+ value.CANTIDAD +'</td>'
								+ '  <td class="text-center">'+ value.DURAACTIV +'</td>'
								+ '  <td class="text-center">'+ parseInt(numdis) +'</td>'
								+ '</tr>'
							number++;
						});

						$('#tbl-activities tbody').html(tr);

						// Cargar actividades
						$('#view-agenda').show();

					}

					if(event.type == 'cita'){
						$('#label-type').html('INFORMACIÓN CITA MEDICA')
						$('#visualizar #c-patient').text(event.title);
						$('#visualizar #c-title').text(event.specialty);
						$('#visualizar #c-medical_activity').text(event.medical_activity);
						$('#visualizar #c-professional').text(event.professional);
						$('#visualizar #c-start').text(event.start.format('DD/MM/YYYY HH:mm:ss'));
						$('#visualizar #c-end').text(event.end.format('DD/MM/YYYY HH:mm:ss'));
						$('#visualizar #c-state_appointment').text(event.state_appointment);
						$('#visualizar #c-consulting_room').text(event.consulting_room);
						$('#view-cita').show();
					}

					$('#visualizar').modal('show');

				},
				selectable: false,
				selectHelper: false,
				select: function(start, end){
					$('#cadastrar #start').val(moment(start).format('DD/MM/YYYY HH:mm:ss'));
					$('#cadastrar #end').val(moment(end).format('DD/MM/YYYY HH:mm:ss'));
					$('#cadastrar').modal('show');						
				},
				events: function(start, end, timezone, callback) {

					var specialties_selected = calendario.getSpecialtiesSelected()
					var schedule_id = calendario.searchSchedule_id;
					calendario.searchSchedule_id = 0;

					$.ajax({
						type: "POST",
						data: {
							start: start.unix(),
							end: end.unix(),
							view: this.getView().name,
							specialties : specialties_selected,
							_token: $('input[name=_token]').val(),
							schedule_id : schedule_id
						},
						url: "/ScheduleControl/events-schedule-availabilities",
						dataType: "json",
						success: function(data) {

							var eventsList = [];
							var count = 0;

							$.each(data.result, function(key, value){

								var id = value.id;
								var start = value.start;
								var end = value.end;
								var title = value.title;
								var specialty = value.specialty;
								var color =  value.color;
								var professional =  value.professional;
								var percentage =  value.percentage;
								var contentclass =  value.contentclass;
								var type = value.type;
								var schedule_activities = value.schedule_activities != undefined ? value.schedule_activities : [];
								var state_appointment = value.state_appointment != undefined ? value.state_appointment : '';
								var schedule_time = value.schedule_time;
								var time_dating = value.time_dating;
								var available_time = value.available_time;
								var percentage_value = value.percentage_value;
								var medical_activity = value.medical_activity;
								var consulting_room = value.consulting_room;


								eventsList.push({
									id : id,
									title : title,
									specialty : specialty,
									start: start,
									end: end,
									color: color,
									professional: professional,
									percentage : percentage,
									contentclass : contentclass,
									type : type,
									schedule_activities: schedule_activities,
									state_appointment : state_appointment,
									schedule_time : schedule_time,
									time_dating : time_dating,
									available_time : available_time,
									percentage_value : percentage_value,
									medical_activity : medical_activity,
									consulting_room : consulting_room
								});

							})

							callback(eventsList);

						}
					});

				}
			});

			
		});
		
		//Mascara para o campo data e hora
		function DataHora(evento, objeto){
			var keypress=(window.event)?event.keyCode:evento.which;
			campo = eval (objeto);
			if (campo.value == '00/00/0000 00:00:00'){
				campo.value=""
			}
		 
			caracteres = '0123456789';
			separacao1 = '/';
			separacao2 = ' ';
			separacao3 = ':';
			conjunto1 = 2;
			conjunto2 = 5;
			conjunto3 = 10;
			conjunto4 = 13;
			conjunto5 = 16;
			if ((caracteres.search(String.fromCharCode (keypress))!=-1) && campo.value.length < (19)){
				if (campo.value.length == conjunto1 )
				campo.value = campo.value + separacao1;
				else if (campo.value.length == conjunto2)
				campo.value = campo.value + separacao1;
				else if (campo.value.length == conjunto3)
				campo.value = campo.value + separacao2;
				else if (campo.value.length == conjunto4)
				campo.value = campo.value + separacao3;
				else if (campo.value.length == conjunto5)
				campo.value = campo.value + separacao3;
			}else{
				event.returnValue = false;
			}
		}

		

	</script>
@endsection


@section('content')
<div class="br-mainpanel">

    <div class="br-pageheader pd-y-15 pd-l-20">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
            <a class="breadcrumb-item" href="#">Calendario</a>
            <span class="breadcrumb-item active">Agendamiento de citas</span>
        </nav>
    </div><!-- br-pageheader -->
		
    <div class="br-pagebody" style="padding: 0 10px;">
        <div class="br-section-wrapper" style="padding: 0px;">
			<div class="container">
				<div class="card-body table-responsive p-0">
					<div class="pt-4">
					  <div class="row">
						<div class="col-sm-2">
						  <div class="form-group">
							<label for="specialties"><i class="fa fa-modx mr-2" aria-hidden="true"></i>Especialidades</label> 
							<a href="javascript:void(0)" id="btn-especiality" class="btn btn-block btn-outline-primary btn-sm"><i class="fa fa-plus-square-o mr-2" aria-hidden="true"></i>Seleccionar..</a>
						  </div>
						</div>
					  </div>
					</div>
				  </div>
				<div class="row">
					<div class="col-md-12">

						<div class="panel-body">
							<!--Inicio elementos contenedor-->
							<div class="page-header">
								<h4><i class="fa fa-calendar-plus-o mr-2 text-primary" aria-hidden="true"></i> Agendamiento especialidades</h4>
							</div>
							<?php
								if(isset($_SESSION['mensaje'])){
									echo $_SESSION['mensaje'];
									unset($_SESSION['mensaje']);
								}
							?>
							<div id='calendar' style="margin-bottom: 50px;"></div>
						</div>	

						
		
						<div class="modal fade" id="cadastrar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title text-center">Registrar Evento</h4>
									</div>
									<div class="modal-body">
										<form class="form-horizontal" method="POST" action="proceso.php">
											<div class="form-group">
												<label for="inputEmail3" class="col-sm-2 control-label">Titulo</label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="titulo" placeholder="Titulo do Evento">
												</div>
											</div>
											<div class="form-group">
												<label for="inputEmail3" class="col-sm-2 control-label">Color</label>
												<div class="col-sm-10">
													<select name="color" class="form-control" id="color">
														<option value="">Selecione</option>			
														<option style="color:#FFD700;" value="#FFD700">Amarillo</option>
														<option style="color:#0071c5;" value="#0071c5">Azul Turquesa</option>
														<option style="color:#FF4500;" value="#FF4500">Naranja</option>
														<option style="color:#8B4513;" value="#8B4513">Marron</option>	
														<option style="color:#1C1C1C;" value="#1C1C1C">Negro</option>
														<option style="color:#436EEE;" value="#436EEE">Azul Real</option>
														<option style="color:#A020F0;" value="#A020F0">Purpura</option>
														<option style="color:#40E0D0;" value="#40E0D0">Turquesa</option>										
														<option style="color:#228B22;" value="#228B22">Verde</option>
														<option style="color:#8B0000;" value="#8B0000">Rojo</option>
													</select>
												</div>	
											</div>
											<div class="form-group">
												<label for="inputEmail3" class="col-sm-2 control-label">Data Inicial</label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="inicio" id="start" onKeyPress="DataHora(event, this)">
												</div>
											</div>
											<div class="form-group">
												<label for="inputEmail3" class="col-sm-2 control-label">Data Final</label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="fin" id="end" onKeyPress="DataHora(event, this)">
												</div>
											</div>
											<div class="form-group">
												<div class="col-sm-offset-2 col-sm-10">
													<button type="submit" class="btn btn-success">Registrar</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						<!--Fin elementos contenedor-->
					</div>
				</div>
			</div>
        </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->

	{{--  Modal detalle Agenda --}}
	<div class="modal fullscreen-modal modal-months fade" id="visualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header text-white bg-primary">
					<h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-calendar mr-2" aria-hidden="true"></i><span id="label-type"></span></h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" >
					<div id="view-agenda" style="display: none;">
						<dl class="dl-horizontal">
							<dt>Especialidad</dt>
							<dd id="title"></dd>
							<dt>Profesional</dt>
							<dd id="professional"></dd>
							<dt>Inicio de agenda</dt>
							<dd id="start"></dd>
							<dt>Fin de agenda</dt>
							<dd id="end"></dd>
							<dt>Minutos agenda</dt>
							<dd id="schedule_time"></dd>
							<dt>Minutos citas</dt>
							<dd id="time_dating"></dd>
							<dt>Minutos disponibles</dt>
							<dd id="available_time"></dd>
							<dt>Ocupación</dt>
							<dd id="progress">
								
							</dd>
						</dl>
						<h6 class="tx-inverse mg-b-0 mb-2"><strong>Actividades disponibles</strong></h6>
						<div class="bd rounded table-responsive">
							<table class="table table-bordered mg-b-0" id="tbl-activities">
							  <thead>
								<tr>
								  	<td class="font-weight-bold">#</th>
								  	<td class="font-weight-bold">Actividades</th>
									<td class="font-weight-bold">Citas Agendadas</th>
								  	<td class="font-weight-bold text-center">Tiempo(Min)</th>
										<td class="font-weight-bold">Citas Disponibles</th>
								</tr>
							  </thead>
							  <tbody>
							  </tbody>
							</table>
						</div>
					</div>
					<div id="view-cita" style="display: none;">
						<dl class="dl-horizontal">
							<dt>Paciente</dt>
							<dd id="c-patient"></dd>
							<dt>Especialidad</dt>
							<dd id="c-title"></dd>
							<dt>Actividad</dt>
							<dd id="c-medical_activity"></dd>
							<dt>Profesional</dt>
							<dd id="c-professional"></dd>
							<dt>Inicio de agenda</dt>
							<dd id="c-start"></dd>
							<dt>Fin de agenda</dt>
							<dd id="c-end"></dd>
							<dt>Estado cita</dt>
							<dd id="c-state_appointment"></dd>
							<dt>Nombre consultorio</dt>
							<dd id="c-consulting_room"></dd>
						</dl>
					</div>
					
				</div>
				<div class="modal-footer">
					<button type="button" id='search-schedule' class="btn btn-primary btn-sm tx-size-xs"><i class="fa fa-search mr-2" aria-hidden="true"></i>Consultar agenda</button>
				</div>
			</div>
		</div>
	</div>
	{{--  Fin modal detalle agenda  --}}

	

	{{--  Modal selección especialidades  --}}
  	@include('entity.schedule-availabilities._modal_specialty', ['specialties' => $specialties])


</div>


@endsection