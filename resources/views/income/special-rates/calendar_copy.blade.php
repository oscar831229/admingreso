@extends('layouts.portal.principal')

@section('css_custom')
    <link rel="stylesheet" href="{{ asset('calendario/css/calendar.css') }}">
    <link href="{{ asset('calendario/css/font-awesome.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('calendario/css/bootstrap-datetimepicker.min.css') }}" /> 
    <link href="{{ asset('js/portal/entity/schedule-availabilities/calendar.css') }}" rel="stylesheet">
@endsection


@section('scripts_content')
    <script>
        var events_schedule = '{{ asset('ScheduleControl/events-schedule-availabilities') }}'
    </script>
    <script type="text/javascript" src="{{ asset('calendario/js/es-ES.js') }}"></script>
    {{--  <script src="{{ asset('calendario/js/jquery.min.js') }}"></script>  --}}
    <script src="{{ asset('calendario/js/moment.js') }}"></script>
    <script src="{{ asset('calendario/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('calendario/js/bootstrap-datetimepicker.js') }}"></script>
    <script src="{{ asset('calendario/js/underscore-min.js') }}"></script>
    <script src="{{ asset('calendario/js/calendar.js') }}"></script>
    <script src="{{ asset('js/portal/entity/schedule-availabilities/calendar.js') }}"></script>
@endsection

<?php

// Verificamos si se ha enviado el campo con name from
if (isset($_POST['from'])) 
{

    // Si se ha enviado verificamos que no vengan vacios
    if ($_POST['from']!="" AND $_POST['to']!="") 
    {

        // Recibimos el fecha de inicio y la fecha final desde el form
        $Datein                    = date('d/m/Y H:i:s', strtotime($_POST['from']));
        $Datefi                    = date('d/m/Y H:i:s', strtotime($_POST['to']));
        $inicio = _formatear($Datein);
        // y la formateamos con la funcion _formatear

        $final  = _formatear($Datefi);

        // Recibimos el fecha de inicio y la fecha final desde el form
        $orderDate                      = date('d/m/Y H:i:s', strtotime($_POST['from']));
        $inicio_normal = $orderDate;

        // y la formateamos con la funcion _formatear
        $orderDate2                      = date('d/m/Y H:i:s', strtotime($_POST['to']));
        $final_normal  = $orderDate2;

        // Recibimos los demas datos desde el form
        $titulo = evaluar($_POST['title']);

        // y con la funcion evaluar
        $body   = evaluar($_POST['event']);

        // reemplazamos los caracteres no permitidos
        $clase  = evaluar($_POST['class']);

        // insertamos el evento
        $query="INSERT INTO agenda(id, title, body, url, class, start, end, inicio_normal, final_normal) VALUES(null,'$titulo','$body','','$clase','$inicio','$final','$inicio_normal','$final_normal')";

        // Ejecutamos nuestra sentencia sql
        $conexion->query($query)or die('<script type="text/javascript">alert("Horario No Disponible ")</script>');

        header("Location:$base_url");        


        // Obtenemos el ultimo id insetado
        $im=$conexion->query("SELECT MAX(id) AS id FROM agenda");
        $row = $im->fetch_row();  
        $id = trim($row[0]);

        // para generar el link del evento
        $link = "$base_url"."descripcion_evento.php?id=$id";

        // y actualizamos su link
        $query="UPDATE agenda SET url = '$link' WHERE id = $id";

        // Ejecutamos nuestra sentencia sql
        $conexion->query($query); 

        // redireccionamos a nuestro calendario
        //header("Location:$base_url"); 
    }
}


?>
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
                <div class="row">
                    <div class="page-header" style="width: 100%;"><h4></h4></div>
                    <div class="pull-left form-inline"><br>
                        <div class="btn-group">
                            <button class="btn btn-primary" data-calendar-nav="prev"><i class="fa fa-arrow-left"></i>  </button>
                            <button class="btn" data-calendar-nav="today">Hoy</button>
                            <button class="btn btn-primary" data-calendar-nav="next"><i class="fa fa-arrow-right"></i>  </button>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-warning" data-calendar-view="year">Año</button>
                            <button class="btn btn-warning active" data-calendar-view="month">Mes</button>
                            <button class="btn btn-warning" data-calendar-view="week">Semana</button>
                            <button class="btn btn-warning" data-calendar-view="day">Dia</button>
                        </div>
                    </div>
                    <div class="pull-right form-inline"><br>
                        <button class="btn btn-info" data-toggle='modal' data-target='#add_evento'>Añadir Evento</button>
                    </div>
                </div>
                <br><br><br>
                <div class="row">
                    <div id="calendar"></div> <!-- Aqui se mostrara nuestro calendario -->  
                </div>

                <!--ventana modal para el calendario-->
                <div class="modal fade" id="events-modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                               <div class="modal-header">
                                <a href="#" data-dismiss="modal" style="float: right;"> <i class="glyphicon glyphicon-remove "></i> </a>
                                <br>
                            </div>
                            <div class="modal-body" style="height: 400px">
                                <p>One fine body&hellip;</p>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

            </div>
            
            
            
            <div class="modal fade" id="add_evento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">Agregar nuevo evento</h4>
                        </div>
                        <div class="modal-body">
                        <form action="" method="post">
                            <label for="from">Inicio</label>
                            <div class='input-group date' id='from'>
                                <input type='text' id="from" name="from" class="form-control" readonly />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                            </div>
            
                            <br>
            
                            <label for="to">Final</label>
                            <div class='input-group date' id='to'>
                                <input type='text' name="to" id="to" class="form-control" readonly />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                            </div>
            
                            <br>
            
                            <label for="tipo">Tipo de evento</label>
                            <select class="form-control" name="class" id="tipo">
                                <option value="event-info">Informacion</option>
                                <option value="event-success">Exito</option>
                                <option value="event-important">Importantante</option>
                                <option value="event-warning">Advertencia</option>
                                <option value="event-special">Especial</option>
                            </select>
            
                            <br>
            
            
                            <label for="title">Título</label>
                            <input type="text" required autocomplete="off" name="title" class="form-control" id="title" placeholder="Introduce un título">
            
                            <br>
            
            
                            <label for="body">Evento</label>
                            <textarea id="body" name="event" required class="form-control" rows="3"></textarea>
            
                            
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                          <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Agregar</button>
                      </form>
                  </div>
              </div>
            </div>
            </div>
        </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->
</div>


@endsection