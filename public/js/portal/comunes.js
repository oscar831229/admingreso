
// Libreria para trabajar con select dependientes
SelectAnidado = function() {
  return {
    autocargado: function(padre, hijo, url){

        $("#"+ padre).change(event => {

            if(`${event.target.value}` == ''){
                $("#" + hijo).empty();
                $("#"+hijo).append(`<option value=''>Seleccione...</option>`);
                return false;
            }

            $.get(`${url}/${event.target.value}`, function(res, sta){
                $("#" + hijo).empty();
                $("#"+hijo).append(`<option value=''>Seleccione...</option>`);
                $.each(res,function(key,val){
                    $("#"+hijo).append(`<option value=${key}> ${val} </option>`);
                });

                if(Object.keys(res).length == 1){
                    $(`#${hijo} option:eq(1)`).attr('selected', 'selected');
                }
                $("#"+hijo).trigger('change');
            });
        });
    }
  }
}();

// Validacion de campos requedidos
var Biblioteca = function () {
  return {
      validacionGeneral: function (id, reglas, mensajes) {
          const formulario = $('#' + id);
          formulario.validate({
              rules: reglas,
              messages: mensajes,
              errorElement: 'span', //default input error message container
              errorClass: 'help-block help-block-error invalid-feedback', // default input error message class
              focusInvalid: false, // do not focus the last invalid input
              ignore: [], // validate all fields including form hidden input
              highlight: function (element, errorClass, validClass) { // hightlight error inputs
                  $(element).closest('.form-control').addClass('is-invalid'); // set error class to the control group
              },
              unhighlight: function (element) { // revert the change done by hightlight
                  $(element).closest('.form-control').removeClass('is-invalid'); // set error class to the control group
              },
              success: function (label) {
                  label.closest('.form-control').removeClass('is-invalid'); // set success class to the control group
              },
              errorPlacement: function (error, element) {
                  if ($(element).is('select') && element.hasClass('bs-select')) {//PARA LOS SELECT BOOSTRAP
                      error.insertAfter(element);//element.next().after(error);
                  } else if ($(element).is('select') && element.hasClass('select2-hidden-accessible')) {
                      element.next().after(error);
                  } else if (element.attr("data-error-container")) {
                      error.appendTo(element.attr("data-error-container"));
                  } else {
                      error.insertAfter(element); // default placement for everything else
                  }
              },
              invalidHandler: function (event, validator) { //display error alert on form submit

              },
              submitHandler: function (form) {
                  return true;
              }
          });
      },
      notificaciones: function (mensaje, titulo, tipo) {
          toastr.options = {
              closeButton: true,
              newestOnTop: true,
              positionClass: 'toast-top-right',
              preventDuplicates: true,
              timeOut: '5000'
          };
          if (tipo == 'error') {
              toastr.error(mensaje, titulo);
          } else if (tipo == 'success') {
              toastr.success(mensaje, titulo);
          } else if (tipo == 'info') {
              toastr.info(mensaje, titulo);
          } else if (tipo == 'warning') {
              toastr.warning(mensaje, titulo);
          }
      },
  }
}();


$('.submit-eliminar').on('click',function(event){
  event.preventDefault();
  const form = $(this).parents('form:first');
  swal({
      title: '¿ Está seguro que desea eliminar el registro ?',
      text: "Esta acción no se puede deshacer!",
      icon: 'warning',
      buttons: {
          cancel: "Cancelar",
          confirm: "Aceptar"
      },
  }).then((value) => {
      if (value) {
        $(form).submit();
      }
  });
})


function call_Ajax(rutaajax,objdata,typesend = "POST"){

  var retorno;
  $.ajax({
    url: rutaajax,
    async: false,
    data: objdata,
    beforeSend: function(objeto){
          // dialogLoading('show');
    },
    complete: function(objeto, exito){
      // dialogLoading('close');
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
    success: function(data){
      retorno = data;
    },
    timeout: 30000,
    type: typesend
  });

  return retorno;

}

// Campos númericos
$(document).on("input", ".numeric", function() {
  this.value = this.value.replace(/\D/g,'');
});

// Campos en mayuscula
$(document).on("input", ".uppercase", function() {
  $(this).val($(this).val().toUpperCase());
});

function fileIcon(extension){
    var icon = '';

    switch (extension) {
      case 'xlsx':
      case 'xls':
      case 'xlsm':
        icon = '<i class="fa fa-file-excel-o text-success" aria-hidden="true"></i>';
        break;

      case 'docx':
      case 'doc':
        icon = '<i class="fa fa-file-word-o" aria-hidden="true"></i>';
        break;

      case 'pdf':
        icon = '<i class="fa fa-file-pdf-o text-danger" aria-hidden="true"></i>';
        break;

      case 'zip':
        icon = '<i class="fa fa-file-archive-o" aria-hidden="true"></i>';
        break;

      case 'img':
      case 'jpeg':
        icon = '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
        break;

      case 'ppt':
      case 'pptX':
        icon = '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>';
        break;

      default:
        icon = '<i class="fa fa-file" aria-hidden="true"></i>';
        break;
    }

  return icon;

}
