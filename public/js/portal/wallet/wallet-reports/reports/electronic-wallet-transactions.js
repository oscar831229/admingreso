
$(function(){

    Biblioteca.validacionGeneral('form-report');

    $('#bth-report-generate').on('click',function(){

        if(!$('#form-report').valid()){
            Biblioteca.notificaciones('Faltan datos obligatorios por diligenciar.', $('.report-name').html(), 'error');
            return false;
        }

        swal({
            title: $('.report-name').html(),
            text: "¿Esta seguro de generar el reporte.?",
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
                btn.loading($('#bth-report-generate'));
                setTimeout(function(){ document.getElementById('form-report').submit(); btn.reset($('#bth-report-generate'));  }, 600);
                
            }
        });
    });

    //Consultar centro de costo
    $('#wallet_user_name').autocomplete({
        serviceUrl:'/wallet/find-wallet-user/',
        paramName: 'name',
        minChars: 3,
        tabDisabled:true,
        onHint: function (hint) {
            $('#wallet_user_x').val(hint);
            $('#wallet_user_id').val('');
        },
        onSelect: function(suggestion) {
            $('#wallet_user_id').val(suggestion.data.id);
        }
    });

});

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


