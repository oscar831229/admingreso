<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Coberturas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # Coberturas procesadas
        Schema::create('icm_coverages', function (Blueprint $table) {
            $table->id();
            $table->date('coverage_date');
            $table->string('step', 10)->nullable();
            $table->string('step_name', 100)->nullable();
            $table->tinyInteger('events')->default(0);
            $table->text('errors')->nullable();
            $table->tinyInteger('is_deleted')->default(0);
            $table->string('state', 1)->default('P')->comment('Estado del proceso de coberturas P: Pendiente de ejecutar, F cobertura terminada, E error proceso');
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
        });

        # Detallado de registro de cobertura uno a uno de los facturados
        // Schema::create('icm_coverage_details', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('icm_coverage_id');
        //     $table->unsignedBigInteger('icm_liquidation_service_id');
        //     $table->foreign('icm_liquidation_service_id','fk_icmcoverage_icmliquidationdetail')
        //         ->references('id')
        //         ->on('icm_liquidation_services')
        //         ->onDelete('restrict')
        //         ->onUpdate('restrict');
        //     $table->unsignedBigInteger('document_type');
        //     $table->unsignedBigInteger('document_number');
        //     $table->string('first_name', 150);
        //     $table->string('second_name', 150)->nullable();
        //     $table->string('first_surname', 150);
        //     $table->string('second_surname', 150)->nullable();
        //     $table->tinyInteger('is_processed_affiliate')->default(0)->comment('Identifica si los datos se completaron con sisafi, aplica solo para afilaidos coberturas');
        //     $table->unsignedBigInteger('icm_types_income_id');
        //     $table->unsignedBigInteger('icm_affiliate_category_id');
        //     $table->string('category_presented_code');
        //     $table->unsignedBigInteger('icm_family_compensation_fund_id')->nullable();
        //     $table->string('nit_company_affiliates', 25)->nullable();
        //     $table->string('name_company_affiliates', 150)->nullable();
        //     $table->string('type_register', 2)->nullable();
        //     $table->string('relationship', 4)->nullable();
        //     $table->string('type_link', 4)->nullable();
        //     $table->string('affiliated_document', 45)->nullable();
        //     $table->string('affiliated_name', 255)->nullable();
        //     $table->unsignedBigInteger('icm_liquidation_id');
        //     $table->tinyInteger('is_deleted')->default(0);
        //     $table->date('admission_date')->nullable();
        // });

        // Schema::create('icm_coverage_details', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('icm_coverage_id');
        //     $table->unsignedBigInteger('icm_coverage_detail_id');
        //     $table->string('MDEPER_COT_TIPOID', 2)->nullable()->comment('Tipo identificación del cotizante');
        //     $table->string('MDEPER_COT_IDENTIF', 15)->nullable()->comment('Número de identificación del cotizante');
        //     $table->string('MDEPER_BEN_TIPOID', 2)->comment('Tipo identificación usuario del servicio');
        //     $table->string('MDEPER_BEN_IDENTIF', 15)->comment('identificación del beneficiario');
        //     $table->string('MDEPER_PRIAPE', 30);
        //     $table->string('MDEPER_SEGAPE', 30)->nullable();
        //     $table->string('MDEPER_PRINOM', 160);
        //     $table->string('MDEPER_SEGNOM', 30)->nullable();
        //     $table->string('MDEPER_RAZSOC', 100)->comment('Solo se llena si la persona es jurídica');
        //     $table->string('MDEPER_NACIMIENTO', 10);
        //     $table->string('MDEPER_GENERO', 1);
        //     $table->string('MDEPER_DIRECCION_RES', 100)->nullable();
        //     $table->decimal('MDEPER_CODDPTO_RES', 2, 0)->nullable();
        //     $table->decimal('MDEPER_CODMUN_RES', 5, 0)->nullable();
        //     $table->string('MDEPER_BARRIO_RES', 60)->nullable();
        //     $table->decimal('MDEPER_CELULAR', 10, 0)->nullable();
        //     $table->string('MDEPER_TEL_FIJO_RES', 15)->nullable();
        //     $table->string('MDEPER_HABEAS_DATA', 2)->nullable();
        //     $table->string('MDEPER_TIPO_PERSONA', 2)->comment('PN (persona natural), PJ (Persona Juridica)');
        //     $table->string('MDEPER_CORREO', 100);
        //     $table->decimal('MDECOB_PRODUCTO_SEAC', 12, 0)->comment('Codigo de producto del Portafolio de servicios');
        //     $table->string('MDECOB_PRODUCTO_ORIGEN', 40)->comment('Producto del sistema de Ingresos codigo"-"descripcion producto');
        //     $table->string('MDECOB_INFRAESTRUCTURA', 100);
        //     $table->date('MDECOB_FECHA_SERVICIO');
        //     $table->string('MDECOB_NITEMP', 15)->comment('Nit de la empresa asociada al cotizante al momento del servicio');
        //     $table->string('MDECOB_ROL_CLIENTE', 2)->comment('Rol de la Identificación al momento del servicio: PX como persona EM como empresa');
        //     $table->string('MDECOB_VINCULACION', 3)->comment('Tipo de vinculacion del cotizante : EMP (Empresa) TRA (trabajador afiliado) IND (independiente) PEN (Pensionado) CNV (CONVENIO)');
        //     $table->decimal('MDECOB_SUBVIN', 3, 0)->comment('Codigo de Subtipo de vinculación enviado en la vista de clientes afiliados');
        //     $table->string('MDECOB_RELACION', 4)->comment('Relación de parentesco TR CY PC HI HJ HE PA');
        //     $table->string('MDECOB_CATEGORIA', 2)->comment('Categoria del sistema de afiliado');
        //     $table->decimal('MDECOB_VALOR_VENTA', 15, 2);
        //     $table->string('MDECOB_TIPO_SUB', 3)->comment('SER (Sub. Servicio) ESP (Subsidio en Especie)');
        //     $table->decimal('MDECOB_SUBSIDIO', 15, 2);
        //     $table->decimal('MDECOB_USOS', 5, 0);
        //     $table->decimal('MDECOB_PARTICIPANTES', 6,0);
        //     $table->string('MDECOB_POLITICA', 6)->comment('VNT: fue a quien si hizo la factura, ACO (Acompañante del que factura); POL (si se adiciono por POLITICA) PDA (Ingreso por dispositivo)');
        //     $table->string('MDECOB_CAJA', 100);
        //     $table->string('MDECOB_SISTEMAFUENTE', 30);
        //     $table->date('MDEPRO_PROCESO');
        //     $table->string('MDECOB_TARIFA_PROMO', 1);
        //     $table->decimal('MDECOB_FOLIO', 11, 0);
        //     $table->string('MDECOB_FACTURA', 200);
        //     $table->decimal('MDECOB_CATEGORIA_SSF', 2, 0);
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('icm_coverage_detail');
        Schema::dropIfExists('icm_coverage');
    }
}
