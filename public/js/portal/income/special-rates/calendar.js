$(function () {
	calendar_custom.init();
});


calendar_custom = {

	// current_year : null,

	// current_month : null,

	// selected_year : null,

    // generarCalendario : function(anio, mes){

    //   var diasMes = new Date(anio, mes, 0).getDate();
    //   var diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

    //   var days_month = [];
    //   to_index = new Date(anio, mes - 1, 1).getDay();
    //   num_days_week = 7;
    //   for (var inicial = 0; inicial < to_index; inicial++) {
    //     days_month.push('');
    //   }

    //   for (var dia = 1; dia <= diasMes; dia++) {
    //     days_month[inicial] = dia;
    //     inicial++;
    //   }

    //   return days_month;

    // },

    // buildCalendar : function(anio){

	// 	$('.current_year').html(anio);

	// 	for (var mes = 1; mes <= 12; mes++) {
	// 		this.buildCalendarMonth(anio, mes);
	// 	}
		
    // },

    // buildCalendarMonth : function(anio, mes){

    //   	var days_month = this.generarCalendario(anio, mes);
    //   	var li = '';

    //   	$.each(days_month, function(key, value){
    //     	li += `<li>${value}</li>`
    //   	});

	// 	$(`.demo[data-month=${mes}] > .days`).html(li)

	// 	$(`.demo[data-month=${mes}]`).parent().show();
	// 	$(`li.list-group-item[data-month=${mes}]`).find('input[type=checkbox]').prop('checked', true).trigger('change');;
	// 	if(this.current_year == this.selected_year  && mes < this.current_month ){
	// 		$(`li.list-group-item[data-month=${mes}]`).find('input[type=checkbox]').prop('checked', false).trigger('change');;
	// 		$(`.demo[data-month=${mes}]`).parent().hide();
	// 	}

	// },

	// showPeriod(period, show = true){

	// },

	// openModalMonths : function (){
	// 	$('#md-months').modal();
	// },

	// openModalEspecialities : function(){
	// 	$('#md-specialty').modal();
	// },

	// checkAllSpecialities : function(){
	// 	var checked = $(this).find('input[type=checkbox]').is(':checked');
	// 	$('.list-group.checked-list-box.list-speciality .list-group-item').each(function () {
	// 		$(this).find('input[type=checkbox]').prop('checked', checked).trigger('change');
	// 	})
	// },

	// searchSpecialities : function(){

	// 	var value = $(this).val().toUpperCase();
	
	// 	$('.list-group.checked-list-box.list-speciality .list-group-item').each(function () {

	// 		$row = $(this);

	// 		var text = $row.text().toUpperCase();;

	// 		if (!text.includes(value)) {
	// 			$row.hide();
	// 		}else {
	// 			$row.show();
	// 		}
	// 	});

	// },

	init : function (){

		// alert('cargnado');
		
		// $('#btn-especiality').on('click', calendario.openModalEspecialities);
		// $('.check-todos').on('click', calendario.checkAllSpecialities);
		// $('#search-input').on('keyup', calendario.searchSpecialities);

		$('#md-specialty').on('hidden.bs.modal', function () {
			$('#calendar').fullCalendar('refetchEvents');
		});

		
	}

}

