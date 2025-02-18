$(function () {


	// // MONTHS
	// $('.list-group.checked-list-box.list-months .list-group-item').each(function () {

	// 	let parent = $(this).parent();
	// 	let alias = $(parent).data('alias');

	// 	// Settings
	// 	let month = $(this).data('month');

	// 	var $widget = $(this),
	// 		$checkbox = $('<input type="checkbox" class="hidden" value="'+month+'" data-check="'+alias+'" />'),
	// 		color = ($widget.data('color') ? $widget.data('color') : "primary"),
	// 		style = ($widget.data('style') == "button" ? "btn-" : "list-group-item-"),
	// 		settings = {
	// 			on: {
	// 				icon: 'glyphicon glyphicon-check'
	// 			},
	// 			off: {
	// 				icon: 'glyphicon glyphicon-unchecked'
	// 			}
	// 		};

	// 	$widget.css('cursor', 'pointer')
	// 	$widget.append($checkbox);

	// 	// Event Handlers
	// 	$widget.on('click', function () {
	// 		$checkbox.prop('checked', !$checkbox.is(':checked'));
	// 		$checkbox.triggerHandler('change');
	// 		updateDisplay();
	// 	});

	// 	$checkbox.on('change', function () {
	// 		updateDisplay();
	// 	});

	// 	// Actions
	// 	function updateDisplay() {

	// 		var isChecked = $checkbox.is(':checked');
	// 		var mes = $widget.data('month');

	// 		$widget.data('state', (isChecked) ? "on" : "off");

	// 		$widget.find('.state-icon')
	// 			.removeClass()
	// 			.addClass('state-icon ' + settings[$widget.data('state')].icon);

	// 		$(`.demo[data-month=${mes}]`).parent().show();
	// 		if (isChecked) {
	// 			$widget.addClass(style + color + ' active');
	// 		} else {
	// 			$widget.removeClass(style + color + ' active');
	// 			$(`.demo[data-month=${mes}]`).parent().hide();
	// 		}

	// 	}

	// 	function init() {

	// 		if ($widget.data('checked') == true) {
	// 			$checkbox.prop('checked', !$checkbox.is(':checked'));
	// 		}

	// 		updateDisplay();

	// 		if ($widget.find('.state-icon').length == 0) {
	// 			$widget.prepend('<span class="state-icon ' + settings[$widget.data('state')].icon + '"></span>');
	// 		}

	// 	}

	// 	init();

	// });


	// // SPECIALITIES
	// $('.list-group.checked-list-box.list-speciality .list-group-item').each(function () {

	// 	let parent = $(this).parent();
	// 	let alias = $(parent).data('alias');

	// 	// Settings
	// 	let value = $(this).data('value');

	// 	var $widget = $(this),
	// 		$checkbox = $('<input type="checkbox" class="hidden" value="'+value+'" data-check="'+alias+'" />'),
	// 		color = ($widget.data('color') ? $widget.data('color') : "primary"),
	// 		style = ($widget.data('style') == "button" ? "btn-" : "list-group-item-"),
	// 		settings = {
	// 			on: {
	// 				icon: 'glyphicon glyphicon-check'
	// 			},
	// 			off: {
	// 				icon: 'glyphicon glyphicon-unchecked'
	// 			}
	// 		};

	// 	$widget.css('cursor', 'pointer')
	// 	$widget.append($checkbox);

	// 	// Event Handlers
	// 	$widget.on('click', function () {
	// 		$checkbox.prop('checked', !$checkbox.is(':checked'));
	// 		$checkbox.triggerHandler('change');
	// 		updateDisplay();
	// 	});

	// 	$checkbox.on('change', function () {
	// 		updateDisplay();
	// 	});

	// 	// Actions
	// 	function updateDisplay() {

	// 		var isChecked = $checkbox.is(':checked');
	// 		var mes = $widget.data('month');

	// 		$widget.data('state', (isChecked) ? "on" : "off");

	// 		$widget.find('.state-icon')
	// 			.removeClass()
	// 			.addClass('state-icon ' + settings[$widget.data('state')].icon);

	// 		$(`.demo[data-month=${mes}]`).parent().show();
	// 		if (isChecked) {
	// 			$widget.addClass(style + color + ' active');
	// 		} else {
	// 			$widget.removeClass(style + color + ' active');
	// 			$(`.demo[data-month=${mes}]`).parent().hide();
	// 		}

	// 	}

	// 	function init() {

	// 		if ($widget.data('checked') == true) {
	// 			$checkbox.prop('checked', !$checkbox.is(':checked'));
	// 		}

	// 		updateDisplay();

	// 		if ($widget.find('.state-icon').length == 0) {
	// 			$widget.prepend('<span class="state-icon ' + settings[$widget.data('state')].icon + '"></span>');
	// 		}

	// 	}

	// 	init();

	// });

	calendario.init();


});

days = {

    weekdays : ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],

    highSeasonDays: ['Viernes', 'Sábado', 'Domingo'],

    getDayToDate: function(year, month, day){
        let fecha = new Date(year, month, day);
        let numeroDiaSemana = fecha.getDay();
        return numeroDiaSemana;
    }

}

calendario = {

	current_year : null,

	current_month : null,

	selected_year : null,

	specialties_init : [],

	setSpecialtiesInit : function(specialties_init){
		this.specialties_init = specialties_init;
	},

	initializeCheckSpecialties : function(){
		$.each(this.specialties_init, function(index, specialty){
			$('input[data-check=chk-especiality][value='+specialty+']').prop('checked', true).trigger('change');;
		})
	},

	getSpecialtiesSelected : function(){

		var data = [];
		$('input[data-check=chk-especiality]:checked').each(function(key, value){
			specialty_code = $(value).val();
			data.push(specialty_code);
		});

		return data;

	},

    generarCalendario : function(anio, mes){

        var diasMes = new Date(anio, mes, 0).getDate();
        var diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        var days_month = [];
        to_index = new Date(anio, mes - 1, 1).getDay();
        num_days_week = 7;
        for (var inicial = 0; inicial < to_index; inicial++) {
            days_month.push('');
        }

        for (var dia = 1; dia <= diasMes; dia++) {
            days_month[inicial] = dia;
            inicial++;
        }

        return days_month;

    },

    buildCalendar : function(anio){

		$('.current_year').html(anio);

		for (var mes = 1; mes <= 12; mes++) {
			this.buildCalendarMonth(anio, mes);
		}

    },

    buildCalendarMonth : function(anio, mes){

      	var days_month = this.generarCalendario(anio, mes);
      	var li = '';
		var monthaux = zfill(mes, 2);

      	$.each(days_month, function(key, value){
			var dayaux = zfill(value, 2);
        	li += `<li data-day="${anio}${monthaux}${dayaux}" class="day-calendar">${value}</li>`
      	});

		$(`.demo[data-month=${mes}] > .days`).html(li)

	},

    heckCalendarDays : function(year){

        $.ajax({
            url: `/income/special-rates/${year}`,
            async: false,
            data: {},
            beforeSend: function(objeto){

            },
            complete: function(objeto, exito){
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
                    Biblioteca.notificaciones(response.message, 'Calendario tarifa altas.', 'error');
                    return false;
                }

                $('.li-check').each(function(key, element){
                    $(element).attr('class', '');
                });

                $.each(response.data, function(key, value){
                    var dateaux = value.date;
                    if($('li[data-day="'+dateaux+'"]').length > 0){
                        $('li[data-day="'+dateaux+'"]').addClass(value.class);
                        $('li[data-day="'+dateaux+'"]').attr('title', value.name);
                        $('li[data-day="'+dateaux+'"]').data('id', value.id);
                        $('li[data-day="'+dateaux+'"]').addClass('li-check');
                    }
                });

                $(".tooltip").tooltip("hide");


            },
            timeout: 30000,
            type: 'GET'
        });
    },

    element_day_option : null,

    deleteDayCalendar : function(){

        element = $(this);
        var day = calendario.element_day_option.data('day');
        var id  = calendario.element_day_option.data('id');

        swal({
            title: 'Calendario temporada alta',
            text: `Desea eliminar el día ${day}`,
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
            btn.loading(element);
            setTimeout(function(){
                $.ajax({
                    url: `/income/special-rates/${id}`,
                    async: true,
                    data: {
                        _token: $('input[name=_token]').val()
                    },
                    beforeSend: function(objeto){

                    },
                    complete: function(objeto, exito){
                        btn.reset(element);
                        if(exito != "success"){
                            alert("No se completo el proceso!");
                        }
                    },
                    contentType: "application/x-www-form-urlencoded",
                    dataType: "json",
                    error: function(objeto, quepaso, otroobj){
                        alert("Ocurrio el siguiente error: "+quepaso);
                        btn.reset(element);
                    },
                    global: true,
                    ifModified: false,
                    processData:true,
                    success: function(response){
                    btn.reset(element);
                    if(response.success){
                        Biblioteca.notificaciones('Proceso exitoso.', 'Calendario temporada alta', 'success');
                        $('#year').trigger('change');
                    }else{
                        Biblioteca.notificaciones(response.message, 'Calendario temporada alta', 'error');
                    }
                    },
                    timeout: 30000,
                    type: 'DELETE'

                });
            },100)
            }
        });
    },

    newDayCalendar: function(){

        element = $(this);
        var day = calendario.element_day_option.data('day');
        var id  = calendario.element_day_option.data('id');

        swal({
            title: 'Calendario temporada alta',
            text: `Desea marcar el siguiente día ${day} como temporada alta`,
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
            btn.loading(element);
            setTimeout(function(){
                $.ajax({
                    url: `/income/special-rates`,
                    async: true,
                    data: {
                        date  : day,
                        year : $('#year').val(),
                        _token: $('input[name=_token]').val()
                    },
                    beforeSend: function(objeto){

                    },
                    complete: function(objeto, exito){
                        btn.reset(element);
                        if(exito != "success"){
                            alert("No se completo el proceso!");
                        }
                    },
                    contentType: "application/x-www-form-urlencoded",
                    dataType: "json",
                    error: function(objeto, quepaso, otroobj){
                        alert("Ocurrio el siguiente error: "+quepaso);
                        btn.reset(element);
                    },
                    global: true,
                    ifModified: false,
                    processData:true,
                    success: function(response){
                    btn.reset(element);
                    if(response.success){
                        Biblioteca.notificaciones('Proceso exitoso.', 'Calendario temporada alta', 'success');
                        $('#year').trigger('change');
                    }else{
                        Biblioteca.notificaciones(response.message, 'Calendario temporada alta', 'error');
                    }
                    },
                    timeout: 30000,
                    type: 'POST'

                });
            },100)
            }
        });
    },

	init : function (){

        if($('#year').val().trim() == ''){
            let fechaActual = new Date();
            let year = fechaActual.getFullYear();
            $('#year').val(year)
        }

        this.current_year  = $('#year').data('year');
        this.selected_year = $('#year').val();
        this.buildCalendar(this.selected_year);
        this.heckCalendarDays(this.selected_year);

		$('#year').on('change', function(){
			calendario.selected_year = $(this).val();
			calendario.buildCalendar(calendario.selected_year);
            calendario.heckCalendarDays(calendario.selected_year);
		});

        $("#year").datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });

        $('body').on('contextmenu', '.day-calendar', function(e) {
            e.preventDefault();
            calendario.element_day_option = $(this);

            $('.menu-opciones').find('.delete').hide();
            $('.menu-opciones').find('.new').hide();
            if(calendario.element_day_option.hasClass('day-primary')){
                $('.menu-opciones').find('.delete').show();
            }
            if(!calendario.element_day_option.hasClass('day-primary')  && !calendario.element_day_option.hasClass('day-success')){
                $('.menu-opciones').find('.new').show();
            }

            $('.menu-opciones').css({
                top: e.pageY + 'px',
                left: e.pageX + 'px'
            }).show();
        });

        $(document).on('click', function() {
            $('.menu-opciones').hide();
        });

        $('body').on('click', '.delete', this.deleteDayCalendar);
        $('body').on('click', '.new', this.newDayCalendar);

    }
}

// Completar con ceros a la izquierda
function zfill(number, width) {
    var numberOutput = Math.abs(number);
    var length = number.toString().length;
    var zero = "0";

    if (width <= length) {
        if (number < 0) {
             return ("-" + numberOutput.toString());
        } else {
             return numberOutput.toString();
        }
    } else {
        if (number < 0) {
            return ("-" + (zero.repeat(width - length)) + numberOutput.toString());
        } else {
            return ((zero.repeat(width - length)) + numberOutput.toString());
        }
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
