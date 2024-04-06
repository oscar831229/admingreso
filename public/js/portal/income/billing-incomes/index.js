$(document).ready(function () {
    environment.init();
    invoice.init();
});

environment = {

    id : null,

    viewEnviromentItems : function(){

        environment.id = $(this).data('environment_id');

        $("#div-environments").hide(250, function() {
            $("#div-content-enveiroment").show();
        });

        invoice.initServices(environment);

    },

    init : function(){
        $('body').on('click', '.environment', this.viewEnviromentItems);
    }

}

invoice = {

    environment : null,

    initServices : function(environment){
        this.environment = environment;
        this.loadIncomeServices();
    },

    loadIncomeServices : function(){

        $.ajax({
            url: '/income/environment-income-services/' + this.environment.id,
            async: true,
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
                if(response.success){
                    const income_services = JSON.stringify(response.data);
                    sessionStorage.setItem('envei_income' + invoice.environment.id, income_services);
                    invoice.constructIncomeServices();
                }
            },
            timeout: 30000,
            type: 'GET'

        });

    },

    constructIncomeServices : function(){
        const incomeservicesJson = sessionStorage.getItem('envei_income' + invoice.environment.id);
        const incomeservices = JSON.parse(incomeservicesJson);
        // options
        var option = `<option value="">Seleccione..</option>`;
        $.each(incomeservices.incomeservices, function(index, service){
            option += `<option value="${service.id}">${service.name}</option>`;
        });
        $('#icm_environment_income_item_id').html(option);
    },

    setIncomeServices : function(){

        $(this).prop('disabled', false);
        var icm_environment_income_item_id = $(this).find('option:selected').val();
        const incomeservicesJson = sessionStorage.getItem('envei_income' + invoice.environment.id);
        const incomeservices = JSON.parse(incomeservicesJson);
        var number_places = '';
        $.each(incomeservices.incomeservices, function(index, income_item){
            if(income_item.id == icm_environment_income_item_id){
                number_places = income_item.number_places;
                return false;
            }
        });

        $('#number_places').val(number_places);

        if(icm_environment_income_item_id.trim() != ''){
            $(this).prop('disabled', true);
        }

    },

    changeIncomeServices : function(){
        $('#icm_environment_income_item_id').val('').trigger('change');
    },

    init : function(){
        $('body').on('change', '#icm_environment_income_item_id', this.setIncomeServices);
        $('body').on('click', '#btn-change-income-service', this.changeIncomeServices);

    }

}
