$(document).ready(function () {
  company.init();
});

var tblcompanies = null;

company = {

  companys : [],

  data : {},

  resetForm : function(){
    $('#'+this.formmodal+' input[name!="_token"]').val('');
    $('#'+this.formmodal+' textarea').val('');
    $('#'+this.formmodal+' select').val('');
  },

  newcompany : function(){
    this.resetForm()
    $('#md-new-company').modal();
  },

  saveInfoCompany : function(){

    element = $(this);

    if(!$('#'+company.formmodal).valid()){
      Biblioteca.notificaciones('Existen datos pendientes de diligenciar.', 'Empresas convenios', 'warning');
      return false;
    }

    var formadata = $('#'+company.formmodal).serialize();

    swal({
      title: 'Empresas convenios',
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
                url: '/income/parameterization-companies',
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
                      $('#md-new-company').modal('hide');
                      company.loadcompanys();
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

  showListcompanys : function(){

    var tr = '';
    var btnedit  = '';

    $.each(company.companys, function(index, value){

      btnedit = '<a href="javaScript:void(0)" data-company_id="'+value.id+'" class="mr-2 edit-company" title="Editar paso">'
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
      +'     <td>'+ company.getLableState(value.state) +'</td>'
      +'     <td>'+btnedit+'</td>'
      +' </tr>';
    });

    $('#tbl-companys tbody').html(tr);

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

  loadcompanys : function(){

        tblcompanies= $('#tbl-companies').DataTable();
        tblcompanies.destroy();

        $('#tbl-companies thead th').each(function () {
            var title = $(this).text();
            if($(this).hasClass('search-disabled')){
                $(this).html(title);
            }else{
                $(this).html(title+' <input type="text" class="col-search-input" placeholder="" />');
            }
        });

        tblcompanies = $('#tbl-companies').DataTable({
            language: language_es,
            pagingType: "numbers",
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '/income/datatable-parameterization-companies',
                type: "POST",
                data: {
                    '_token' : $('input[name=_token]').val()
                },
                "dataSrc": function (json) {
                    return json.data;
                },
                async: true
            },
            columnDefs: [{
                targets: "_all",
                orderable: false,
            },{ "width": "200px", "targets": 2 }],

            initComplete: function () {
            },
            createdRow: function (row, data, index) {

                btnedit = '<a href="javaScript:void(0)" data-id="'+data[0]+'" class="mr-2 edit-company" title="Editar servicio de ingreso">'
                    + '<i class="fa fa-edit text-success"></i>'
                    + '</a>';

                $('td', row).eq(6).html(getLableState(data[6])).addClass('dt-center');
                $('td', row).eq(7).html(btnedit).addClass('dt-center');
                $('td', row).eq(0).html(data[8]).addClass('dt-center');
            }
        });

        tblcompanies.columns().every(function () {
            var table = this;
            $('input', this.header()).on('keyup change', function () {
                if (table.search() !== this.value && (this.value.length > 2  || this.value.length == 0)) {
                    table.search(this.value).draw();
                }
            });
        });
  },

  loadDatacompany : function(){
    company.resetForm();
    loadDataForm('form-icm-companies-agreements', company.data);
    $('#md-new-company').modal();
  },

  editcompany : function(){

    var company_id = $(this).data('id');
    var element = $(this);

    btn.loading(element);

    setTimeout(function(){
      $.ajax({
          url: 'parameterization-companies/' + company_id,
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
                company.data = response.data;
                company.loadDatacompany();
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
    this.formmodal = 'form-icm-companies-agreements';
    $('body').on('click', '#btn-new-company', this.newcompany.bind(this, $('#btn-new-company')));
    $('body').on('click', '#btn-save', this.saveInfoCompany);
    $('body').on('change', '#change_status', this.changeStatus);
    $('body').on('click', '.edit-company', this.editcompany);
    this.loadcompanys();
    Biblioteca.validacionGeneral(this.formmodal);
  }

}

function getLableState(state){

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

}

loadDataForm = function(idform, data){
    Object.keys(data).forEach(key => {
        if($(`#${idform}`).find(`[name=${key}]`).length > 0){
            $(`#${idform}`).find(`[name=${key}]`).val(data[key]);
        }
    });
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
