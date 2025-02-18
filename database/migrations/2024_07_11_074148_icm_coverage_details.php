<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IcmCoverageDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icm_coverage_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icm_coverage_id');
            $table->string('MDEPER_COT_TIPOID', 2)->nullable()->comment('Tipo identificación del cotizante');
            $table->string('MDEPER_COT_IDENTIF', 15)->nullable()->comment('Número de identificación del cotizante');
            $table->string('MDEPER_BEN_TIPOID', 2)->comment('Tipo identificación usuario del servicio');
            $table->string('MDEPER_BEN_IDENTIF', 15)->comment('identificación del beneficiario');
            $table->string('MDEPER_PRIAPE', 30);
            $table->string('MDEPER_SEGAPE', 30)->nullable();
            $table->string('MDEPER_PRINOM', 160);
            $table->string('MDEPER_SEGNOM', 30)->nullable();
            $table->string('MDEPER_RAZSOC', 100)->nullable()->comment('Solo se llena si la persona es jurídica');
            $table->string('MDEPER_NACIMIENTO', 10);
            $table->string('MDEPER_GENERO', 1);
            $table->string('MDEPER_DIRECCION_RES', 100)->nullable();
            $table->decimal('MDEPER_CODDPTO_RES', 2, 0)->nullable();
            $table->decimal('MDEPER_CODMUN_RES', 5, 0)->nullable();
            $table->string('MDEPER_BARRIO_RES', 60)->nullable();
            $table->decimal('MDEPER_CELULAR', 10, 0)->nullable();
            $table->string('MDEPER_TEL_FIJO_RES', 15)->nullable();
            $table->string('MDEPER_HABEAS_DATA', 2)->nullable();
            $table->string('MDEPER_TIPO_PERSONA', 2)->comment('PN (persona natural), PJ (Persona Juridica)');
            $table->string('MDEPER_CORREO', 100)->nullable();
            $table->decimal('MDECOB_PRODUCTO_SEAC', 12, 0)->comment('Codigo de producto del Portafolio de servicios');
            $table->string('MDECOB_PRODUCTO_ORIGEN', 40)->comment('Producto del sistema de Ingresos codigo"-"descripcion producto');
            $table->string('MDECOB_INFRAESTRUCTURA', 100);
            $table->date('MDECOB_FECHA_SERVICIO');
            $table->string('MDECOB_NITEMP', 15)->nullable()->comment('Nit de la empresa asociada al cotizante al momento del servicio');
            $table->string('MDECOB_ROL_CLIENTE', 2)->comment('Rol de la Identificación al momento del servicio: PX como persona EM como empresa');
            $table->string('MDECOB_VINCULACION', 3)->nullable()->comment('Tipo de vinculacion del cotizante : EMP (Empresa) TRA (trabajador afiliado) IND (independiente) PEN (Pensionado) CNV (CONVENIO)');
            $table->decimal('MDECOB_SUBVIN', 3, 0)->nullable()->comment('Codigo de Subtipo de vinculación enviado en la vista de clientes afiliados');
            $table->string('MDECOB_RELACION', 4)->nullable()->comment('Relación de parentesco TR CY PC HI HJ HE PA');
            $table->string('MDECOB_CATEGORIA', 2)->comment('Categoria del sistema de afiliado');
            $table->decimal('MDECOB_VALOR_VENTA', 15, 2)->nullable();
            $table->string('MDECOB_TIPO_SUB', 3)->nullable()->comment('SER (Sub. Servicio) ESP (Subsidio en Especie)');
            $table->decimal('MDECOB_SUBSIDIO', 15, 2)->nullable();
            $table->decimal('MDECOB_USOS', 5, 0)->nullable();
            $table->decimal('MDECOB_PARTICIPANTES', 6,0)->nullable();
            $table->string('MDECOB_POLITICA', 6)->nullable()->comment('VNT: fue a quien si hizo la factura, ACO (Acompañante del que factura); POL (si se adiciono por POLITICA) PDA (Ingreso por dispositivo)');
            $table->string('MDECOB_CAJA', 100)->nullable();
            $table->string('MDECOB_SISTEMAFUENTE', 30)->nullable();
            $table->date('MDEPRO_PROCESO')->nullable();
            $table->string('MDECOB_TARIFA_PROMO', 1)->nullable();
            $table->decimal('MDECOB_FOLIO', 11, 0)->nullable();
            $table->string('MDECOB_FACTURA', 200)->nullable();
            $table->decimal('MDECOB_CATEGORIA_SSF', 2, 0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('icm_coverage_details');
    }
}
