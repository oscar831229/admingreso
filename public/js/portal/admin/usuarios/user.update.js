$(document).ready(function () {

    SelectAnidado.autocargado('unidad','sucursal', url_users + '/sucursales')

    var users = function(){
        return {
            elemento : '',
            delete:function(elemento){
                this.elemento = elemento;
                $('#confirm').modal();
                return false;
            },
            anular:function(){

            }
        }

    }()

    Biblioteca.validacionGeneral('usuario-update')
    

});
  