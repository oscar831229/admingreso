$(document).ready(function () {

  Biblioteca.validacionGeneral('servicio-crear')

  SelectAnidado.autocargado('unidad','sucursal_id', '/Admin/users/sucursales')

  SelectAnidado.autocargado('sucursal_id','user_id', '/Admin/users/sucursal')
  
});
