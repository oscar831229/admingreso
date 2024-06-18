

var tablliquidations = null;


liquidations = {

    data : [],

    viewliquidations : function(){

        tablliquidations= $('#tbl-liquidations').DataTable();
        tablliquidations.destroy();

        $('#tbl-liquidations thead th').each(function () {
            var title = $(this).text();
            if($(this).hasClass('search-disabled')){
                $(this).html(title);
            }else{
                $(this).html(title+' <input type="text" class="col-search-input" placeholder="" />');
            }
        });

        tablliquidations = $('#tbl-liquidations').DataTable({
            language: language_es,
            pagingType: "numbers",
            processing: true,
            serverSide: true,
            ajax: {
                url: '/income/datatable-liquidations',
                type: "POST",
                data: {
                    '_token'    : $('input[name=_token]').val(),
                    'state'     : $('#state_liquidation').val(),
                    'date_from' : $('#date_from').val(),
                    'date_to'   : $('#date_to').val(),
                },
                "dataSrc": function (json) {
                    return json.data;
                },
                async: true
            },
            columnDefs: [{
                targets: "_all",
                orderable: false
            }],

            initComplete : function(settings, json){
            },
            createdRow: function (row, data, index) {
                var btnaction = data[5] == 'F' ? '<a href="javascript:void(0)" data-id="'+data[0]+'" data-environment="'+data[4]+'" class="tooltipsC btn-print-invoice" title="Imprimir factura"><i class="fa fa-print" aria-hidden="true"></i></a>' : '';
                var btnstart  = data[5] == 'P' ? '<a href="javascript:void(0)" data-id="'+data[0]+'" data-environment="'+data[4]+'" class="tooltipsC btn-reload-liquidation" title="Cargar liquidación"><i class="fa fa-archive" aria-hidden="true"></i></a>' : '';
                $('td', row).eq(7).html(btnaction + btnstart).addClass('dt-center');
                $('td', row).eq(0).html(data[8]).addClass('dt-center');
                $('td', row).eq(5).html(setLabelState(data[5])).addClass('dt-center');
            }
        });

        tablliquidations.columns().every(function () {
            var table = this;
            $('input', this.header()).on('keyup change', function () {
                if (table.search() !== this.value && (this.value.length > 2  || this.value.length == 0)) {
                    table.search(this.value).draw();
                }
            });
        });

    },

    printInvoice : function(){
        var icm_liquidation_id = $(this).data('id');
        window.open(`/income/billing-incomes-print/${icm_liquidation_id}`, null, 'width=300, height=700, toolbar=no, statusbar=no');
    },

    reloadLiquidation : function(){
        var icm_liquidation_id = $(this).data('id');
        var environment = $(this).data('environment');
        var nuevaUrl = '/income/billing-incomes' +
               '?environment=' + encodeURIComponent(environment) +
               '&icm_liquidation_id=' + encodeURIComponent(icm_liquidation_id);
        window.location.href = nuevaUrl;
    },

    init : function(){

        $('body').on('click', '#btn-refresh-liquidation', liquidations.viewliquidations);
        $('body').on('click', '.btn-print-invoice', liquidations.printInvoice);
        $('body').on('click', '.btn-reload-liquidation', liquidations.reloadLiquidation);

        // Obtener la fecha actual
        var fechaActual = new Date();

        // Formatear las fechas según tus necesidades (opcional)
        var primerDiaFormateado = fechaActual.toISOString().split('T')[0];
        var ultimoDiaFormateado = fechaActual.toISOString().split('T')[0];

        $('#date_from').val(primerDiaFormateado);
        $('#date_to').val(ultimoDiaFormateado);

        this.viewliquidations();
    }
}



setLabelState = function(state){
    switch (state) {
        case 'P':
            return '<label class="badge badge-warning" style="width: 75px;">En proceso</label>';
            break;
        case 'F':
            return '<label class="badge badge-success" style="width: 75px;">Facturada</label>';
            break;

        default:
            return '';
            break;
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


$(function(){
    liquidations.init();
});
