<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Wallet\WalletUser;
use App\Models\Wallet\Movement;

class ExistCusWalletUser implements Rule
{


    private $message = '';

    private $movement_type_code = null;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($movement_type_code)
    {
        $this->movement_type_code = $movement_type_code;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $request = request();
        $customer = (object) $request->customer;
        $wallet_user = WalletUser::where(['document_number' => $customer->document_number] )->first();

        if(!$wallet_user){
            $this->message = 'Usuario de la transacción a realizar no existe.';
            return false;
        }

        # CODIGO USUARI PROCESO
        $movement = Movement::where(['cus' => $value, 'wallet_user_id' => $wallet_user->id ])->first();

        # VALIDAR QUE CUS EXISTA.
        if(!$movement){
            $this->message = 'Código CUS no esta vinculadas con transacciones del usuario indicado';
            return false;
        }

        # TRANSACCION DE REVERSO DE ABONOS
        if($this->movement_type_code == '02' && $movement->movement_type->code != '01'){
            $this->message = 'Tipo de transacción a reversar invalido';
            return false;
        }

        # TRANSACCION DE REVERSO DE CONSUMOS
        if($this->movement_type_code == '04' && $movement->movement_type->code != '03'){
            $this->message = 'Tipo de transacción a reversar invalido';
            return false;
        }

        $movement_exists = Movement::where(['cus_transaction' => $value, 'wallet_user_id' => $wallet_user->id ])->first();
        if($movement_exists){
            $this->message = 'Reverso ya se encuentra aplicado.';
            return false;
        }

        return true;
        
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
