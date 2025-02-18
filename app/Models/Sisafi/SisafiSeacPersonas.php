<?php

namespace App\Models\Sisafi;

use Illuminate\Database\Eloquent\Model;

class SisafiSeacPersonas extends Model
{
    protected $table = 'sisafi_seac_personas';

    protected $fillable = [
        "relacion",
        "tipo_reg",
        "consecutivo_dep",
        "tipo_id",
        "identificacion",
        "primer_apellido",
        "segundo_apellido",
        "primer_nombre",
        "segundo_nombre",
        "fecha_nacimiento",
        "genero",
        "direccion",
        "barrio",
        "cod_municipio",
        "cod_depto",
        "celular",
        "tel_fijo",
        "tipo_persona",
        "correo",
        "vinculacion",
        "subvinculacion",
        "categoria",
        "fuente_creacion",
        "fecha_creacion",
        "fuente_actualizacion",
        "fecha_actualizacion",
        "consecutivo_ppal",
        "tipoid_ppal",
        "id_principal",
        "primer_apellido_ppal",
        "segundo_apellido_ppal",
        "primer_nombre_ppal",
        "segundo_nombre_ppal",
        "nombre_ppal",
        "tipo_id_empresa",
        "nit_empresa",
        "razon_social",
        "estado_afi",
        "fecha_ret",
        "sisafi_sync_tracer_id"
    ];
}
