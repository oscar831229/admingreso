$(document).ready(function () {

  $.ajaxSetup({
    headers: {'X-CSRF-Token': $('meta[name=_token]').attr('content')}
  });

  mail = function(){
    return {
      server : $('#server'),
      encryption: $('#encryption'),
      puerto : $('#puerto'),
      email : $('#email'),
      password : $('#password'),
      form : 'form-email',
      init : function(button){
        $("#"+button).click(mail.sendmail);
        Biblioteca.validacionGeneral(mail.form)
      },
      sendmail :  function(){
        if($('#'+mail.form).valid()){
          mail.post();
        }
      },
      post : function(){

        var data = {
          'server': this.server.val(),
          'encryption': this.encryption.val(),
          'puerto': this.puerto.val(),
          'email': this.email.val(),
          'password' : this.password.val()
        }

        sendAjax(`${route_mail}/testMail`,data,'POST',mail.proccess);
        
      },
      proccess :function(response){
        $("#loading").css("display", "none");
        if(response.success){
          Biblioteca.notificaciones('Correo enviado exitosamente','Confirmacion','success');
          $("#saveMail").removeClass('d-none');
        }else{
          Biblioteca.notificaciones(response.error,'Confirmacion','error');
        }
      }
    }
  }()
   
  mail.init('testConexion');


  function sendAjax(url,data,method = 'POST', callback){

    data._token = token;
    $("#loading").css("display", "block");
    

    $.ajax({
      method: method,
      url: url,
      data: data
    }).done(callback);

  }


  

  

});
