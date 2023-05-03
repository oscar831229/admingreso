$(document).ready(function () {

    Biblioteca.validacionGeneral('form-general');

    $('#icono').on('blur', function () {
        $('#mostrar-icono').removeClass().addClass('fa fa-fw ' + $(this).val());
    });

    $('#fecha_inicial').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 2,
        dateFormat: 'yy-mm-dd',
        minDate: new Date(),
        onSelect:function(fecha,elemento){
            var lockDate = new Date($('#fecha_inicio').datepicker('getDate'));
            $("#fecha_final").datepicker('setDate', lockDate);
            $("#fecha_final").datepicker('option', 'minDate', lockDate);
        } 
    });
        
    $('#fecha_final').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: 'yy-mm-dd',
    });

});


