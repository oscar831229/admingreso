<?php 
namespace App\Clases\ElectronicWallet;

use App\Clases\ElectronicWallet\Movement\PaymentClass;
use App\Clases\ElectronicWallet\Movement\ReversePaymentClass;
use App\Clases\ElectronicWallet\Movement\ConsumeClass;
use App\Clases\ElectronicWallet\Movement\ReverseConsumeClass;

use App\Clases\Mail\MainSendMail;


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

                $this->movement_register = $movement->getMovemenRegister();
                $alltickets = $this->movement_register->tickets;
                $title = 'Nuevos tickets creados';

                break;
            case '02':
                $movement = new ReversePaymentClass($this->request);
                $movement->execute();

                $this->movement_register = $movement->getMovemenRegister();
                
                $alltickets = $this->movement_register->statetickets;
                $title = 'Tickets anulados';

                break;
            case '03':

                $movement = new ConsumeClass($this->request);
                $movement->execute();

                $this->movement_register = $movement->getMovemenRegister();
                $alltickets = $this->movement_register->statetickets;
                $title = 'Tickets redimos en consumo';

                break;
            case '04':
                $movement = new ReverseConsumeClass($this->request);
                $movement->execute();

                $alltickets = $movement->getTickets();
                $title = 'Tickets reversados por consumo';

                break;
            default:
                # code...
                break;
        }

        

        # ACTUALIZAR SALDOS BOLSILLO.
        $this->movement_register = $movement->getMovemenRegister();

        $this->updateBalancePocketUser($this->movement_register->electrical_pocket_wallet_user_id);

        # NOTIFICAR MOVIMIENTO
        $tablehtml = MainSendMail::tableTickets($alltickets, $title);
        MainSendMail::send('notify_movement', ['m.id' => $this->movement_register->id ], [$movement->wallet_user->email], [], ['numrows' => count($alltickets), 'table' => $tablehtml]);

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

    public function validation($request){

        # EJECUTA MOVIMIENTO
        $this->request = $request;

        # EJECUTA MOVIMIENTOS
        return $this->executeValidationMovement();

    }

    public function executeValidationMovement(){

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
                $movement->MovementValidations();
                break;
            case '02':
                $movement = new ReversePaymentClass($this->request);
                $movement->MovementValidations();
                break;
            case '03':
                $movement = new ConsumeClass($this->request);
                $movement->MovementValidations();
                break;
            case '04':
                $movement = new ReverseConsumeClass($this->request);
                $movement->MovementValidations();
                break;
            default:
                # code...
                break;
        }

        # ACTUALIZAR SALDOS BOLSILLO.
        
        

        

    }


}