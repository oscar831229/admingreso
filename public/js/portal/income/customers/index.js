var tablcustomers=null;

var customers={
    mode:'initial',

    initTable:function(data){
        if($.fn.DataTable.isDataTable('#tbl-customers')){
            $('#tbl-customers').DataTable().destroy();
            $('#tbl-customers tbody').empty();
        }

        tablcustomers=$('#tbl-customers').DataTable({
            data:data||[],
            language:$.extend({},language_es,{
                emptyTable:'No hay clientes para mostrar.',
                zeroRecords:'No se encontraron registros dentro de la información cargada.'
            }),
            pagingType:'numbers',processing:false,serverSide:false,searching:true,ordering:true,
            pageLength:25,lengthMenu:[[10,25,50,100,200],[10,25,50,100,200]],order:[],
            columns:[
                {data:6,defaultContent:''},{data:1,defaultContent:''},{data:2,defaultContent:''},
                {data:3,defaultContent:''},{data:4,defaultContent:''},{data:null,defaultContent:''}
            ],
            columnDefs:[
                {targets:5,orderable:false,searchable:false,className:'dt-center',render:function(data,type,row){
                    return '<a href="javascript:void(0)" data-id="'+row[0]+'" class="tooltipsC btn-edit-form-customers" title="Editar cliente"><i class="fa fa-pencil-square-o"></i></a>';
                }},
                {targets:0,className:'dt-center'}
            ]
        });
    },

    loadInitial:function(){
        customers.mode='initial';
        $('#filter-document-number,#filter-name,#filter-phone,#filter-email').val('');
        $('#customer-search-info').html('<i class="fa fa-spinner fa-spin"></i> Cargando últimos clientes...');

        $.ajax({
            url:'/income/datatable-customers',type:'POST',dataType:'json',
            data:{_token:$('input[name="_token"]').first().val(),mode:'initial',limit:200},
            success:function(response){
                var data=customers.responseData(response);
                customers.initTable(data);
                $('#customer-search-info').html('<i class="fa fa-info-circle"></i> Mostrando los últimos <strong>'+data.length+'</strong> clientes. Los filtros trabajan sobre estos registros. Para consultar toda la base utilice <strong>Buscar en todos</strong>.');
            },
            error:function(xhr){
                customers.initTable([]);
                Biblioteca.notificaciones(customers.errorMessage(xhr,'No fue posible cargar los clientes.'),'Clientes','error');
            }
        });
    },

    searchAll:function(){
        var filters=customers.getFilters();

        if(!customers.hasFilters(filters)){
            Biblioteca.notificaciones('Debe ingresar al menos un criterio para buscar en toda la base.','Clientes','warning');
            return;
        }

        if(!customers.validateFilters(filters)) return;

        var element=$('#btn-search-customers');
        btn.loading(element);

        $.ajax({
            url:'/income/datatable-customers',type:'POST',dataType:'json',
            data:{
                _token:$('input[name="_token"]').first().val(),mode:'search',
                document_number:filters.document_number,name:filters.name,
                phone:filters.phone,email:filters.email,limit:200
            },
            success:function(response){
                btn.reset(element);

                if(response.success===false){
                    Biblioteca.notificaciones(response.message||'No fue posible realizar la búsqueda.','Clientes','warning');
                    return;
                }

                var data=customers.responseData(response);
                customers.mode='search';
                customers.initTable(data);
                $('#customer-search-info').html('<i class="fa fa-search"></i> Resultado de búsqueda general: <strong>'+data.length+'</strong> registro(s). Los filtros continúan disponibles para trabajar localmente sobre este resultado.');
            },
            error:function(xhr){
                btn.reset(element);
                Biblioteca.notificaciones(customers.errorMessage(xhr,'Ocurrió un error realizando la búsqueda.'),'Clientes','error');
            }
        });
    },

    localFilter:function(){
        if(!tablcustomers) return;
        tablcustomers
            .column(1).search($.trim($('#filter-document-number').val()))
            .column(2).search($.trim($('#filter-name').val()))
            .column(3).search($.trim($('#filter-phone').val()))
            .column(4).search($.trim($('#filter-email').val()))
            .draw();
    },

    clear:function(){
        $('#filter-document-number,#filter-name,#filter-phone,#filter-email').val('');
        customers.loadInitial();
        $('#filter-document-number').focus();
    },

    getFilters:function(){
        return {
            document_number:$.trim($('#filter-document-number').val()),
            name:$.trim($('#filter-name').val()),
            phone:$.trim($('#filter-phone').val()),
            email:$.trim($('#filter-email').val())
        };
    },

    hasFilters:function(f){
        return f.document_number!==''||f.name!==''||f.phone!==''||f.email!=='';
    },

    validateFilters:function(f){
        if(f.name!==''&&f.name.length<3){Biblioteca.notificaciones('El nombre o apellido debe contener mínimo 3 caracteres.','Clientes','warning');$('#filter-name').focus();return false;}
        if(f.phone!==''&&f.phone.length<3){Biblioteca.notificaciones('El teléfono debe contener mínimo 3 caracteres.','Clientes','warning');$('#filter-phone').focus();return false;}
        if(f.email!==''&&f.email.length<3){Biblioteca.notificaciones('El correo debe contener mínimo 3 caracteres.','Clientes','warning');$('#filter-email').focus();return false;}
        return true;
    },

    responseData:function(response){
        if($.isArray(response)) return response;
        if(response&&$.isArray(response.data)) return response.data;
        return [];
    },

    errorMessage:function(xhr,message){
        return xhr.responseJSON&&xhr.responseJSON.message?xhr.responseJSON.message:message;
    },

    confirmSavecustomers:function(){
        if(!$('#form-customers').valid()){
            Biblioteca.notificaciones('Faltan datos obligatorios por diligenciar.','Clientes','warning');
            return;
        }

        swal({
            title:'Clientes',text:'¿Está seguro de continuar con el proceso?',icon:'warning',
            buttons:{Aceptar:{text:'Aceptar',value:'Aceptar',visible:true},cancel:true}
        }).then(function(value){if(value) customers.savecustomers();});
    },

    savecustomers:function(){
        var element=$('#btn-save');
        btn.loading(element);

        $.ajax({
            url:'/income/customers',type:'POST',data:$('#form-customers').serialize(),
            contentType:'application/x-www-form-urlencoded',dataType:'json',processData:true,timeout:30000,
            success:function(response){
                btn.reset(element);

                if(!response.success){
                    Biblioteca.notificaciones(response.message,'Clientes','error');
                    return;
                }

                document.getElementById('form-customers').reset();
                $('#form-customers').find('[name=id]').val('');
                $('#md-customer').modal('hide');
                Biblioteca.notificaciones('Cliente actualizado correctamente.','Clientes','success');

                var filters=customers.getFilters();
                if(customers.mode==='search'&&customers.hasFilters(filters)) customers.searchAll();
                else customers.loadInitial();
            },
            error:function(xhr){
                btn.reset(element);
                Biblioteca.notificaciones(customers.errorMessage(xhr,'Ocurrió un error guardando el cliente.'),'Clientes','error');
            }
        });
    },

    editcustomers:function(){
        var id=$(this).data('id'),element=$(this);
        btn.loading(element);

        $.ajax({
            url:'/income/customers/'+id,type:'GET',dataType:'json',timeout:30000,
            success:function(response){
                btn.reset(element);

                if(!response.success){
                    Biblioteca.notificaciones(response.message,'Clientes','error');
                    return;
                }

                document.getElementById('form-customers').reset();
                $('#form-customers').find('[name=id]').val('');
                loadDataForm('form-customers',response.ratetype);
                $('#md-customer').modal();
            },
            error:function(xhr){
                btn.reset(element);
                Biblioteca.notificaciones(customers.errorMessage(xhr,'Ocurrió un error consultando el cliente.'),'Clientes','error');
            }
        });
    },

    init:function(){
        $.validator.addMethod('phoneCustom',function(value,element){
            return this.optional(element)||(/^[+]?[0-9]{1,4}?[-.●]?[0-9]{1,4}?[-.●]?[0-9]{1,4}?[-.●]?[0-9]{1,4}$/.test(value)&&value.replace(/[^\d]/g,'').length<=10);
        },'Por favor, ingresa un número de teléfono válido.');

        $.validator.addMethod('validEmailDomain',function(value,element){
            if(this.optional(element)) return true;
            var p=value.split('@');
            return p.length===2&&/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(p[1]);
        },'Por favor ingresa un dominio válido.');

        Biblioteca.validacionGeneral('form-customers',{
            document_type:{required:true},document_number:{required:true},first_name:{required:true},
            first_surname:{required:true},birthday_date:{required:true},gender:{required:true},
            icm_municipality_id:{required:true},address:{required:true},type_regime_id:{required:true},
            phone:{required:true,phoneCustom:true},email:{required:true,email:true,validEmailDomain:true}
        },{
            document_type:{required:'Tipo de documento es obligatorio.'},
            document_number:{required:'Número de documento es obligatorio.'},
            first_name:{required:'Primer nombre es obligatorio.'},
            first_surname:{required:'Primer apellido es obligatorio.'},
            birthday_date:{required:'Fecha de nacimiento es obligatorio.'},
            gender:{required:'Genero es obligatorio.'},
            icm_municipality_id:{required:'Municipio es obligatorio.'},
            address:{required:'Dirección es obligatorio.'},
            type_regime_id:{required:'Regimen es obligatorio.'},
            phone:{required:'El teléfono es obligatorio.'},
            email:{required:'El correo electrónico es obligatorio.'}
        });

        $('body').on('click','#btn-search-customers',customers.searchAll);
        $('body').on('click','#btn-clear-customers',customers.clear);
        $('body').on('click','#btn-save',customers.confirmSavecustomers);
        $('body').on('click','.btn-edit-form-customers',customers.editcustomers);

        $('#filter-document-number,#filter-name,#filter-phone,#filter-email').on('keyup',function(e){
            if(e.which===13){customers.searchAll();return;}
            customers.localFilter();
        });

        customers.loadInitial();
    }
};

var customerExport={
    exportId:null,
    timer:null,

    start:function(){
        swal({
            title:'Exportar clientes',
            text:'Se generará un archivo CSV con todos los registros y campos de clientes. ¿Desea continuar?',
            icon:'warning',
            buttons:{cancel:true,confirm:{text:'Exportar',value:true,visible:true}}
        }).then(function(value){
            if(!value) return;

            var element=$('#btn-export-customers');
            btn.loading(element);

            $.ajax({
                url:'/income/customer-export/start',type:'POST',dataType:'json',
                data:{_token:$('input[name="_token"]').first().val()},
                success:function(response){
                    btn.reset(element);

                    if(!response.success){
                        Biblioteca.notificaciones(response.message,'Exportación clientes','error');
                        return;
                    }

                    customerExport.exportId=response.export_id;
                    $('#customer-export-card').show();
                    $('#btn-download-customers-export').hide();
                    $('#customer-export-error').hide();
                    customerExport.startPolling();
                },
                error:function(xhr){
                    btn.reset(element);
                    Biblioteca.notificaciones(customers.errorMessage(xhr,'No fue posible iniciar la exportación.'),'Exportación clientes','error');
                }
            });
        });
    },

    latest:function(){
        $.ajax({
            url:'/income/customer-export/latest',type:'GET',dataType:'json',
            success:function(response){
                if(!response.success||!response.data) return;

                customerExport.exportId=response.data.id;
                $('#customer-export-card').show();
                customerExport.render(response.data,false);

                if(response.data.status==='PENDING'||response.data.status==='PROCESSING') customerExport.startPolling();
            }
        });
    },

    startPolling:function(){
        customerExport.stopPolling();
        customerExport.status();

        customerExport.timer=setInterval(function(){
            customerExport.status();
        },3000);
    },

    stopPolling:function(){
        if(customerExport.timer){
            clearInterval(customerExport.timer);
            customerExport.timer=null;
        }
    },

    status:function(){
        if(!customerExport.exportId) return;

        $.ajax({
            url:'/income/customer-export/'+customerExport.exportId+'/status',
            type:'GET',dataType:'json',
            success:function(response){
                if(response.success) customerExport.render(response.data,true);
            }
        });
    },

    render:function(data,notify){
        var labels={
            PENDING:'Pendiente',
            PROCESSING:'Generando archivo CSV',
            COMPLETED:'Exportación finalizada',
            FAILED:'Error en la exportación'
        };

        var progress=parseInt(data.progress||0,10);

        $('#customer-export-status').text(labels[data.status]||data.status);
        $('#customer-export-total').text(customerExport.number(data.total_rows));
        $('#customer-export-processed').text(customerExport.number(data.processed_rows));
        $('#customer-export-progress').css('width',progress+'%').text(progress+'%');
        $('#customer-export-error').hide();

        if(data.status==='PENDING'||data.status==='PROCESSING'){
            $('#btn-export-customers').prop('disabled',true);
            $('#btn-download-customers-export').hide();
        }

        if(data.status==='COMPLETED'){
            customerExport.stopPolling();
            $('#btn-export-customers').prop('disabled',false);
            $('#btn-download-customers-export')
                .attr('href','/income/customer-export/'+data.id+'/download')
                .show();

            if(notify) Biblioteca.notificaciones('El archivo CSV de clientes fue generado correctamente.','Exportación clientes','success');
        }

        if(data.status==='FAILED'){
            customerExport.stopPolling();
            $('#btn-export-customers').prop('disabled',false);
            $('#btn-download-customers-export').hide();
            $('#customer-export-error').text(data.error_message||'Ocurrió un error generando el archivo.').show();
        }
    },

    number:function(value){
        return parseInt(value||0,10).toLocaleString('es-CO');
    },

    init:function(){
        $('body').on('click','#btn-export-customers',customerExport.start);
        customerExport.latest();
    }
};

function loadDataForm(idform,data){
    Object.keys(data).forEach(function(key){
        var element=$('#'+idform).find('[name="'+key+'"]');
        if(element.length) element.val(data[key]);
    });
}

var btn={
    loading:function(element){
        var text='<i class="fa fa-spinner fa-spin"></i> Procesando...';
        if($(element).html()!==text){
            $(element).data('original-text',$(element).html());
            $(element).html(text).prop('disabled',true);
        }
    },
    reset:function(element){
        var original=$(element).data('original-text');
        if(original) $(element).html(original);
        $(element).prop('disabled',false);
    }
};

$(function(){
    customers.init();
    customerExport.init();
});
