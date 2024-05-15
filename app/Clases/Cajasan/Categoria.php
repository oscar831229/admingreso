<?php


namespace App\Clases\Cajasan;


class Categoria
{
    public function buscarCategoria($numero_documento = '')
	{
		if($numero_documento !='')
		{
			$afiliacion     = new Afiliacion;
			$beneficiario   = [];
			$grupofamiliar = new GrupoFamiliar;

            # CONSULTAMOS LA AFILIACIÓN EN LA API
            $responseapi = $afiliacion->consultarCategoria($cedula);

            # ES AFILIADO
            if($responseapi->CODIGO == 1)
            {
                # VALIDAR SI NO EXISTE EN GRUPO FAMILIAR
                $grupo = new grupo;

                # ARMAR DATOS RESPUESTA
                foreach ($responseapi->DATOS as $key => $value)
                {
                    # CREAR EL GRUPO FAMILIAR
                    $grupo->serPersona($value);
                }

                $gruposfamiliar->setGrupo($grupo);
            }

			# ASIGNARMOS CATEGORIA DEFAULT
            $persona['categoria'] = 'D';
            $persona['api'] = 'N';
            $persona['tipo_documento'] = null;
			$persona['numero_documento'] = $cedula;
            $persona['fecha_nacimiento'] = null;
            $persona['genero'] = null;
            $persona['afiliado'] = 'N';
            $persona['tipo_vinculacion'] = '';
            $persona['principal'] = [];

            $pergrupo = $gruposfamiliar->existperson($cedula);

            if($pergrupo)
            {
                $persona['categoria'] = $pergrupo->categoria;
                $persona['api'] = 'S';
                $persona['afiliado'] = 'S';
                $persona['tipo_vinculacion'] = $pergrupo->tipo_vinculacion;
                $persona['tipo_documento'] = $pergrupo->tipo_dcto_beneficiario;
                $persona['fecha_nacimiento'] = $pergrupo->fecha_nacimiento;
				$persona['primer_nombre'] = $pergrupo->primer_nombre;
				$persona['segundo_nombre'] = isset($pergrupo->segundo_nombre) ? $pergrupo->segundo_nombre :'';
				$persona['primer_apellido'] = $pergrupo->primer_apellido;
				$persona['segundo_apellido'] = isset($pergrupo->segundo_apellido) ? $pergrupo->segundo_apellido : '';
                $persona['genero'] = $pergrupo->genero;
                $persona['principal'] = $gruposfamiliar->existperson($pergrupo->dcto_trabajador);
            }

			return $persona;
		}
	}
}
