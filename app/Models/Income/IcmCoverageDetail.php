<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmCoverageDetail extends Model
{
    protected $fillable = [
        'icm_coverage_id',
        'MDEPER_COT_TIPOID',
        'MDEPER_COT_IDENTIF',
        'MDEPER_BEN_TIPOID',
        'MDEPER_BEN_IDENTIF',
        'MDEPER_PRIAPE',
        'MDEPER_SEGAPE',
        'MDEPER_PRINOM',
        'MDEPER_SEGNOM',
        'MDEPER_RAZSOC',
        'MDEPER_NACIMIENTO',
        'MDEPER_GENERO',
        'MDEPER_DIRECCION_RES',
        'MDEPER_CODDPTO_RES',
        'MDEPER_CODMUN_RES',
        'MDEPER_BARRIO_RES',
        'MDEPER_CELULAR',
        'MDEPER_TEL_FIJO_RES',
        'MDEPER_HABEAS_DATA',
        'MDEPER_TIPO_PERSONA',
        'MDEPER_CORREO',
        'MDECOB_PRODUCTO_SEAC',
        'MDECOB_PRODUCTO_ORIGEN',
        'MDECOB_INFRAESTRUCTURA',
        'MDECOB_FECHA_SERVICIO',
        'MDECOB_NITEMP',
        'MDECOB_ROL_CLIENTE',
        'MDECOB_VINCULACION',
        'MDECOB_SUBVIN',
        'MDECOB_RELACION',
        'MDECOB_CATEGORIA',
        'MDECOB_VALOR_VENTA',
        'MDECOB_TIPO_SUB',
        'MDECOB_SUBSIDIO',
        'MDECOB_USOS',
        'MDECOB_PARTICIPANTES',
        'MDECOB_POLITICA',
        'MDECOB_CAJA',
        'MDECOB_SISTEMAFUENTE',
        'MDEPRO_PROCESO',
        'MDECOB_TARIFA_PROMO',
        'MDECOB_FOLIO',
        'MDECOB_FACTURA',
        'MDECOB_CATEGORIA_SSF'
    ];
}
