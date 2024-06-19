
$(function(){

    $('body').on('click', '.view-form-report', function(){
        event.preventDefault();

        var reportname = $(this).closest('tr').children('td:eq(2)');
        $('.report-name').html(reportname.html());


        $("#modal-body").load('/income/income-reports/'+$(this).data('code'), function(response, status, xhr) {
            if (status == "error") {
              var msg = "Error!, algo ha sucedido: ";
              $("#modal-body").html(msg + xhr.status + " " + xhr.statusText);
            }

            $('#modal').appendTo("body").modal('show')

        });

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


