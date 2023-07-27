$(document).ready(function () {

  step.init();
  tracking.init();
  invoice.init();

});

invoice = {

  save : function(e){
    
    e.preventDefault();
    if(!$('#form-register-invoice').valid()){
      Biblioteca.notificaciones('Existen datos pendientes de diligenciar.', 'Facturación alertas donantes', 'warning');
      return false;
    }

    var element = $("#btn-save-invoice");

    swal({
      title: 'Alerta de posible donante',
      text: "¿Esta seguro de registrar la alerta de posible donante.?",
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
          var formData = new FormData(document.getElementById("form-register-invoice"));
          formData.append("pda_possible_donor_id", step.donor_file.id);

          $.ajax({
            url: "pending-billings",
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
          }).done(function(response){
            btn.reset(element);
            if(response.success){
              Biblioteca.notificaciones('Proceso exitoso.', 'Pasos posibles donantes', 'success');
              step.loadRegistroDonante($('#notelement'))
            }else{
                Biblioteca.notificaciones(response.message, 'Pasos posibles donantes', 'error');
            }
          });
        } 
    });

  },

  init: function(){

    if($('#form-register-invoice').length > 0){
      Biblioteca.validacionGeneral('form-register-invoice');
      $('body').on('submit', '#form-register-invoice', invoice.save);
    }

  }

}


step = {

  steps : [],

  donor_file : {},

  pda_possible_donor_id : null,

  data : {},

  resetForm : function(){
    $('#'+this.formmodal+' input[name!="_token"]').val('');
    $('#'+this.formmodal+' textarea').val('');
    $('#'+this.formmodal+' select').val('');
    $('#medical_evolution').summernote('reset');
  },

  newStep : function(){
    this.resetForm()
    $('#md-new-step').modal();
  },

  saveStep : function(element){

    if(!$('#'+this.formmodal).valid()){
      Biblioteca.notificaciones('Existen datos pendientes de diligenciar.', 'Pasos posibles donantes', 'warning');
      return false;
    }

    var formadata = $('#'+this.formmodal).serialize();

    swal({
      title: 'Alerta de posible donante',
      text: "¿Esta seguro de registrar la alerta de posible donante.?",
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
                url: '/PossibleDonor/donor-alerts',
                async: true,
                data: formadata,		
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
                      step.loadSteps();
                  }else{
                      Biblioteca.notificaciones(response.message, 'Pasos posibles donantes', 'error');
                  }                
                },
                timeout: 30000,
                type: 'POST'

            });
          },100)
        } 
    });
  },

  changeStatus : function(){
    $('#new_state').attr('required', false);
    $('.new-state').hide();

    if($(this).val() == 1){
      $('#new_state').attr('required', true);
      $('.new-state').show();
    }
  },

  showListSteps : function(){

    var tr = '';
    var btnedit  = '';
    
    $.each(step.steps, function(index, value){

      btnedit = '<a href="javaScript:void(0)" data-step_id="'+value.id+'" class="mr-2 edit-step" title="Editar paso">'
      + '<i class="fa fa-edit text-success"></i>'
      + '</a>';

      tr += '<tr data-index="'+index+'">'
      +'     <td>'+value.id+'</td>'
      +'     <td>'+value.name+'</td>'
      +'     <td>'+value.description+'</td>'
      +'     <td>'+value.user_created+'</td>'
      +'     <td>'+ step.getLableState(value.state) +'</td>'
      +'     <td>'+btnedit+'</td>'
      +' </tr>';
    });

    $('#tbl-steps tbody').html(tr);

  },

  getLableState : function(state){

    switch (state) {
        case 'A':
            return '<label class="badge badge-success">Activo</label>';
            break;
        case 'I':
            return '<label class="badge badge-warning">Inactivo</label>';
            break;
    
        default:
            return '';
            break;
    }

  },

  loadSteps : function(){

    tblalertdonor= $('#tbl-alert-donor').DataTable();
    tblalertdonor.destroy();

    $('#tbl-alert-donor thead th').each(function () {
      var title = $(this).text();
      if($(this).hasClass('search-disabled'))
        $(this).html(title);
      else{
        $(this).html(title+' <input type="text" class="col-search-input" placeholder="" />');
      }
    });

    var data = $("#form-transaction").serialize().split("&");
    console.log(data);
    var obj={};
    for(var key in data)
    {
        obj[data[key].split("=")[0]] = data[key].split("=")[1];
    }

    tblalertdonor = $('#tbl-alert-donor').DataTable({
      language: language_es,
      pagingType: "numbers",
      processing: true,
      serverSide: true,
      ajax: {
          url: '/wallet/detail-transactions',
          type: "POST",
          data: obj,
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
        var btnaction = '<a href="javascript:void(0)" data-id="'+data[0]+'" class="tooltipsC btn-register-process" title="Visualizar transacción"><i class="fa fa-exchange" aria-hidden="true"></i></a>'
        $('td', row).eq(10).html(btnaction).addClass('dt-center');
        $('td', row).eq(0).html(data[12]).addClass('dt-center');
      }                                                              
    });

    tblalertdonor.columns().every(function () {
      var table = this;
      $('input', this.header()).on('keyup change', function () {
          if (table.search() !== this.value && (this.value.length > 2  || this.value.length == 0)) {
            table.search(this.value).draw();
          }
      });
    });

  },

  loadDataStep : function(){
    
    step.resetForm();
    
    $.each(step.data, function(index, value){
      if($('#' + step.formmodal).find("#"+index).length > 0){
        $('#' + step.formmodal).find("#"+index).val(value);
      }
    });

    $('').modal();

    
  },

  editStep : function(){

    var step_id = $(this).data('step_id');
    var element = $(this);
    
    btn.loading(element);

    setTimeout(function(){
      $.ajax({
          url: 'donor-alerts/' + step_id,
          async: true,
          data: {},		
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
              step.data = response.data;
              step.loadDataStep();
            }else{
                Biblioteca.notificaciones(response.message, 'Pasos posibles donantes', 'error');
            }                
          },
          timeout: 30000,
          type: 'GET'
      });
    },100)

  },

  registerProcess : function(){
    
    step.pda_possible_donor_id = $(this).data('id');
    step.loadRegistroDonante($(this))
    $('#md-procces-donor').modal();
    $('#v-pills-home-tab').trigger('click');
    tracking.cancelTracking();
    
  },

  loadRegistroDonante : function(element){

    btn.loading(element);

    setTimeout(function(){

      $(".tooltip").tooltip("hide");

      $.ajax({
          url: 'donor-alerts/' + step.pda_possible_donor_id,
          async: true,
          data: {},		
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
              step.donor_file = response.data;
              step.loadDataDonorFile();
            }else{
                Biblioteca.notificaciones(response.message, 'Pasos posibles donantes', 'error');
            }                
          },
          timeout: 30000,
          type: 'GET'
      });
    },100)

  },

  loadDataDonorFile : function(){

    step.loadDataPossibleDonor();
    step.loadDataInvoice();

    // Load data tracking
    tracking.data = step.donor_file.possibledonorevolutions;
    tracking.documentations = step.donor_file.possibledonordocumentations;
    tracking.loadDataTracking();
    tracking.loadDataDocumentations();

    $('#btn-new-tracking').show();
    if(step.donor_file.state != 'T'){
      $('#btn-new-tracking').hide();
    }


  },

  loadDataInvoice : function(){

    $('#div-form-invoice').show();
    $('#div-table-invoice').hide();
    if(step.donor_file.is_invoiced == 1){
      $('#div-form-invoice').hide();
      $('#div-table-invoice').show();

      $('#table-invoice tbody').empty();
      let tr = '';
      let value = step.donor_file;

      tr += '<tr>'
      +'     <td>'+value.invoice_number+'</td>'
      +'     <td>'+value.invoice_value+'</td>'
      +'     <td>'+value.invoice_date+'</td>'
      +'     <td>'+value.user_invoice.name+'</td>'
      +' </tr>';

      $('#table-invoice tbody').html(tr);


    }


  },

  loadDataPossibleDonor : function(){
     $.each(step.donor_file, function(index, value){
        if(typeof value == 'object'){
          if($('#v-pills-home').find('[data-name="'+index+'"]').length)
            $('#v-pills-home').find('[data-name="'+index+'"]').html(value.name);
        }else{
          if($('#v-pills-home').find('[data-name="'+index+'"]').length)
            $('#v-pills-home').find('[data-name="'+index+'"]').html(value);
        }
     });
  },

  init : function(){

    this.formmodal = 'form-new-step';

    $('body').on('click', '#btn-new-step', this.newStep.bind(this, $('#btn-new-step')));
    $('body').on('click', '#btn-save-step', this.saveStep.bind(this, $('#btn-save-step')));
    $('body').on('change', '#change_status', this.changeStatus);
    $('body').on('click', '.btn-register-process', this.registerProcess);
    $('body').on('click', '#btn-refresh', this.loadSteps);
    
    $('#city_reports_alert_id').selectpicker();
    $('#pda_health_provider_unit_id').selectpicker();
    $('body').on('click', '.edit-step', this.editStep);

    $('#medical_evolution').summernote({
      tabsize: 2,
      height: 300
    });

    // Acciones formularios seguimientos
    this.loadSteps();
    Biblioteca.validacionGeneral(this.formmodal);
    
    

  }

}

tracking = {

  documentations : [],

  saveTracking : function(){

    if(!$('#form-record-tracking').valid()){
      Biblioteca.notificaciones('Existen datos pendientes de diligenciar.', 'Seguimiento posible donante', 'warning');
      return false;
    }

    var element = $(this);
    var formadata = $('#form-record-tracking').serialize();
    formadata += '&pda_possible_donor_id='+step.donor_file.id; 

    swal({
      title: 'Seguimiento posible donante',
      text: "¿Esta seguro de registrar seguimiento del posible donante.?",
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
                url: '/PossibleDonor/tracking-donor-alerts',
                async: true,
                data: formadata,		
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
                      Biblioteca.notificaciones('Proceso exitoso.', 'Pasos posibles donantes', 'success');
                      tracking.cancelTracking();
                      step.loadRegistroDonante($('#notelement'))
                  }else{
                      Biblioteca.notificaciones(response.message, 'Pasos posibles donantes', 'error');
                  }                
                },
                timeout: 30000,
                type: 'POST'

            });
          },100)
        } 
    });
  },

  loadDataTracking : function(){
    var tr = '';
    $('#tbl-tracking tbody').empty();
    $.each(tracking.data, function(index, value){
      tr += '<tr>'
         +'   <th scope="row">'+ value.id + '</th>'
         +'   <td>'+ value.pda_step_name + '</td>'
         +'   <td>'+ value.user_create_name + '</td>'
         +'   <td>'+ value.created_at + '</td>'
         +'   <td s>'+ value.description + '</td>'
         +'</tr>'
    }); 
    $('#tbl-tracking tbody').html(tr);

  },

  loadDataDocumentations : function(){
    var tr = '';
    $('#tbl-donor-documentations tbody').empty();
    $.each(tracking.documentations, function(index, value){

      var btndownload ='<a href="javascript:void(0);" data-pda_possible_donor_documentation_id="'+value.id+'" class="btn-accion-tabla tooltipsC download-document" title="Descargar documento.">'
      +'    <i class="fa fa-download" aria-hidden="true"></i>'
      +' </a>';

      tr += '<tr>'
         +'   <th scope="row">'+ value.id + '</th>'
         +'   <td>' + fileIcon(value.extension) + ' ' + value.original_name +'</td>'
         +'   <td>'+ value.user_created_name + '</td>'
         +'   <td>'+ value.created_at + '</td>'
         +'   <td>'+ btndownload +'</td>'
         +'</tr>'
    }); 
    $('#tbl-donor-documentations tbody').html(tr);

  },

  newTracking : function(){
    $('#div-detail-trackin').hide();
    $('#div-form-trackin').show();
    tracking.resetForm();
    $('#description').summernote('reset');
    $('#step_id').trigger('cnahge');
  },

  cancelTracking : function(){
    $('#div-detail-trackin').show();
    $('#div-form-trackin').hide();
  },

  resetForm : function(){
    $('#form-record-tracking input[name!="_token"]').val('');
    $('#form-record-tracking textarea').val('');
    $('#form-record-tracking select').val('');
  },

  viewCauseNonDonor : function(){
    var newstate = $(this).find('option:selected').data('newstate');
    $('#div-cause-non-donation').hide();
    $('#div-cause-non-donation').find('#pda_cause_non_donation_id').attr('required', false);
    if(newstate == 'D'){
      $('#div-cause-non-donation').show();
      $('#div-cause-non-donation').find('#pda_cause_non_donation_id').attr('required', true);
    }

  },

  downloadDocumentTracing : function(){
    var pda_possible_donor_documentation_id = $(this).data('pda_possible_donor_documentation_id');
    window.open('/PossibleDonor/download-document/' + pda_possible_donor_documentation_id, '_blank');
  },

  init : function(){

    $('body').on('click', '#btn-save-tracking', this.saveTracking);
    $('body').on('click', '#btn-new-tracking', this.newTracking);
    $('body').on('click', '#btn-cancel-tracking', this.cancelTracking);
    $('body').on('change', '#step_id', this.viewCauseNonDonor);
    $('body').on('click', '.download-document', this.downloadDocumentTracing);

    $('#description').summernote({
      tabsize: 2,
      height: 300
    });
    
    Biblioteca.validacionGeneral('form-record-tracking');

  }

}

btn = {

  loading : function(element){

    if($(element.length) == 0){
      return false;
    }

    var loadingText = '<i class="fa fa-spinner fa-spin"></i> Procesando...';
    if ($(element).html() !== loadingText) {
        $(element).data('original-text', $(element).html());
        $(element).html(loadingText);
        $(element).prop( "disabled", true );
    }

  },

  reset : function(element){

    if($(element.length) == 0){
      return false;
    }

    $(element).html($(element).data('original-text'));
    $(element).prop( "disabled", false );

  }

}