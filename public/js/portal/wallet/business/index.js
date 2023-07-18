$(document).ready(function () {
  step.init();
});


step = {

  steps : [],

  data : {},

  resetForm : function(){
    $('#'+this.formmodal+' input[name!="_token"]').val('');
    $('#'+this.formmodal+' textarea').val('');
    $('#'+this.formmodal+' select').val('');
  },

  newStep : function(){
    this.resetForm()
    $('#md-new-step').modal();
  },

  saveStep : function(element){

    if(!$('#'+this.formmodal).valid()){
      Biblioteca.notificaciones('Existen datos pendientes de diligenciar.', 'Comercios aliados', 'warning');
      return false;
    }

    var formadata = $('#'+this.formmodal).serialize();

    swal({
      title: 'Comercios',
      text: "¿Esta seguro de continuar con el proceso.?",
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
                url: '/wallet/business',
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
                      Biblioteca.notificaciones('Proceso exitoso.', 'Comercios aliados', 'success');
                      $('#md-new-step').modal('hide');
                      step.loadSteps();
                  }else{
                      Biblioteca.notificaciones(response.message, 'Comercios aliados', 'error');
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
      +'     <td>'+value.code+'</td>'
      +'     <td>'+value.name+'</td>'
      +'     <td>'+value.address+'</td>'
      +'     <td>'+value.phone+'</td>'
      +'     <td>'+value.created_at+'</td>'
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

    $.ajax({
      url: '/wallet/details-business',
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
          step.steps = response.data;
          step.showListSteps();
        }else{
        }                
      },
      timeout: 30000,
      type: 'GET'
    });
  },

  loadDataStep : function(){
    
    step.resetForm();
    
    $.each(step.data, function(index, value){
      if($('#' + step.formmodal).find("#"+index).length > 0){
        $('#' + step.formmodal).find("#"+index).val(value);
      }
    });

    $('#md-new-step').modal();

    
  },

  editStep : function(){

    var step_id = $(this).data('step_id');
    var element = $(this);
    
    btn.loading(element);

    setTimeout(function(){
      $.ajax({
          url: 'business/' + step_id,
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
                Biblioteca.notificaciones(response.message, 'Comercios aliados', 'error');
            }                
          },
          timeout: 30000,
          type: 'GET'
      });
    },100)

  },

  init : function(){

    this.formmodal = 'form-new-step';

    $('body').on('click', '#btn-new-step', this.newStep.bind(this, $('#btn-new-step')));
    $('body').on('click', '#btn-save-step', this.saveStep.bind(this, $('#btn-save-step')));
    $('body').on('change', '#change_status', this.changeStatus);



    $('body').on('click', '.edit-step', this.editStep);

    
    this.loadSteps();
    Biblioteca.validacionGeneral(this.formmodal);

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