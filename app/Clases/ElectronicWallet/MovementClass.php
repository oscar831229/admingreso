<?php 
namespace App\Clases\ElectronicWallet;

use App\Clases\ElectronicWallet\Movement\PaymentClass;
use App\Clases\ElectronicWallet\Movement\ReversePaymentClass;
use App\Clases\ElectronicWallet\Movement\ConsumeClass;
use App\Clases\ElectronicWallet\Movement\ReverseConsumeClass;


class MovementClass
{

    # TIPOS MOVIMIENTO
    private $movement_type = '';
    private $transaction = '';

    public function __construct($movement_type){
        $this->movement_type = $movement_type;
    }

    public function setMovementType($movement_type){
        $this->movement_type = $movement_type;
    }

    public function execute($request){

        # EJECUTA MOVIMIENTO
        $this->request = $request;

        # EJECUTA MOVIMIENTOS
        return $this->executeMovement();
    }

    public function executeMovement(){

        # VALIDAR TRANSACCION TIEMPO Y EVITA QUE SOLO PERMITA GENERA NUEVA TRANSACCION DEL MISMO MOVIMIENTO 5 SEGUNDOS



        /**
         *  MOVIMIENTOS
         * 01 => ABONOS
         * 02 => REVERSO ABONO
         * 03 => CONSUMO
         * 04 => REVERSO CONSUMO
         */
        switch ($this->movement_type) {

            case '01':
                $movement = new PaymentClass($this->request);
                $movement->execute();
                break;
            case '02':
                $movement = new ReversePaymentClass($this->request);
                $movement->execute();
                break;
            case '03':
                $movement = new ConsumeClass($this->request);
                $movement->execute();
                break;
            case '04':
                $movement = new ReverseConsumeClass($this->request);
                $movement->execute();
                break;
            default:
                # code...
                break;
        }

        # ACTUALIZAR SALDOS BOLSILLO.
        $this->movement_register = $movement->getMovemenRegister();
        $this->updateBalancePocketUser($this->movement_register->electrical_pocket_wallet_user_id);

        return $this->movement_register;

    }

    public function updateBalancePocketUser($electrical_pocket_wallet_user_id){

        $queryUpdate = "UPDATE electrical_pocket_wallet_user AS epwu 
        SET balance = IFNULL(
        (SELECT SUM( 
             CASE
                 WHEN m.nature_movement = 'C' THEN m.value
                 ELSE -m.value
             END
            ) AS saldo FROM movements AS m WHERE m.electrical_pocket_wallet_user_id = epwu.id), 0
        )
        WHERE id = ?";

        \DB::update($queryUpdate, [$electrical_pocket_wallet_user_id]);

    }


}