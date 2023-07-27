<?php 
namespace App\Clases\ElectronicWallet\Movement;

use Validator;
use App\Models\Wallet\ElectricalPocket;
use App\Models\Wallet\WalletUser;
use App\Models\Wallet\Movement;
use App\Models\Wallet\MovementType;
use App\Models\Wallet\Store;

use Illuminate\Support\Facades\Crypt;
use App\Clases\ElectronicWallet\TicketHelperClass;


class ConsumeClass
{

    private $validation_rules = [
        'customer.document_number' => 'required',
        'customer.token' => 'required',
        'transaction.electrical_pocket' => 'required|exists:electrical_pockets,code',
        'transaction.value' => 'required|numeric',
        'transaction.user_code' => 'required',
        'transaction.store' => 'required|exists:stores,code'
    ];

    private $validation_message = [
        'customer.document_number.required' => 'Número de identificación del cliente requerida',
        'customer.token.required' => 'Token es requerido para transacción',
        'transaction.electrical_pocket.required' => 'Código del bolsillo es requerido',
        'transaction.electrical_pocket.exists' => 'No existe bolsillo del código indicado',
        'transaction.value.required' => 'Valor de la transacción requerido',
        'transaction.value.numeric' => 'Valor de la transacción debe ser numérico',
        'transaction.user_code.required' => 'Código de usuario requerido para proceso',
        'transaction.store.required' => 'El código del comercio es requerido',
        'transaction.store.exists' => 'No existe el comercio con código indicado'
    ];

    /**
     *  CONTROLA TIPO DE MOVIMIENTO REALIZADO
     */
    public $movement_type_code = '03';

    private $create_wallet_user = false;

    private $create_electronic_pocket_ws = false;

    private $customer = null;

    public $transaction = null;

    public function __construct($request){
        $this->request = $request;
        $this->user = auth()->user();
    }

    public function execute(){
    
        # VALIDACIÓN PREVIAS
        $this->Validations();

        # PREPARAR USUARIO TRANSACCION
        $this->getWalletUser();

        # VALIDACION TOKEN
        $token = Crypt::decryptString($this->wallet_user->token);
        if($this->customer->token != $token){
            throw new \Exception("Error en validación de token usuario.", 1);
        }

        # CARGAR BOLSILLO ELECTRONICO USUARIO TRANSACCION
        $this->getElectronicPocketWalletUser($this->transaction->electrical_pocket);

        # BOLSILLO USUARIO 
        $electrical_pocket_wallet_user= $this->electronic_pocket->pivot;

        # VALIDAR SALDO
        if($this->transaction->value > $electrical_pocket_wallet_user->balance){
            throw new \Exception("'No existe saldo suficiente para realizar operación'", 1);
        }

        # Instanciamos el Helper Tiquetera
        $ticekhelper = new TicketHelperClass($this);
        $ticekhelper->runValidation();

        # GENERA CODIGO UNICO DE SEGUIMIENTO
        $cus_code = $this->generateCUSCode();

        # INICIAR TRANSACCIÓN
        \DB::beginTransaction();
        try {

            # CREAR MOVIMIENTO
            $this->movement_register = Movement::create([
                'electrical_pocket_wallet_user_id' => $electrical_pocket_wallet_user->id,
                'wallet_user_id' => $electrical_pocket_wallet_user->wallet_user_id,
                'electrical_pocket_id' => $this->electronic_pocket->id,
                'movement_type_id' => $this->movement_type->id,
                'nature_movement' => $this->movement_type->nature_movement,
                'value' =>  $this->transaction->value,
                'user_code' => $this->transaction->user_code,
                'store_id' => $this->store->id,
                'cus' => $cus_code,
                'electrical_pocket_operation_type' => $this->electronic_pocket->operation_type,
                'movement_date' => date('Y-m-d'),
                'user_created' => $this->user->id
            ]);

            $ticekhelper->generateTickets($this->movement_register);

            \DB::commit();

        } catch (\Exception $e){
            \DB::rollBack();
            dd($e->getMessage());
            return "Error de actulización de datos";
        }

    }

    public function getWalletUser(){
        
        $this->wallet_user = WalletUser::where(['document_number' => $this->customer->document_number] )->first();

        if(!$this->wallet_user && $this->create_wallet_user){

            # GENERAR TOKEN 
            $token_key = '123456';

            $passwordencryt = Crypt::encryptString($token_key);

            $this->wallet_user = WalletUser::create([
                'identification_document_type_id' => 1,
                'document_number' => $this->customer->document_number,
                'first_name' => $this->customer->first_name,
                'second_name' => $this->customer->second_name,
                'first_surname' => $this->customer->first_surname,
                'second_surname' => $this->customer->second_surname,
                'email' => $this->customer->email,
                'phone' => $this->customer->phone,
                'token' => $passwordencryt,
                'user_created' => auth()->user()->id
            ]);
            
        }

        # SI NO EXISTE EL USUARIO DE BILLETERA
        if(!$this->wallet_user){
            throw new Exception("Error Processing Request", 1);
        }

    }

    public function generateCUSCode(){
        return generarCodigo(8);
    }


    public function getElectronicPocketWalletUser($electrical_pocket_code){

        # VERIFICAR Y CREAR BOLSILLO
        $this->electronic_pocket = $this->wallet_user->ElectronicPockets()->where(['code' => $electrical_pocket_code ])->first();
        if(!$this->electronic_pocket && $this->create_electronic_pocket_ws){

            # CONSULTAR BOLSILLO 
            $electrical_pocket = ElectricalPocket::where(['code' => $electrical_pocket_code ])->first();
            
            # CREAMOS EL BOLSILLO
            $electronic_pocket = $this->wallet_user->ElectronicPockets()->attach($electrical_pocket, [
                'balance' => 0,
                'last_movement_date' => date('Y-m-d'),
                'user_created' => auth()->user()->id
            ]);

            # CONSULTAMOS EL BOLSILLO CREADO
            $this->electronic_pocket = $this->wallet_user->ElectronicPockets()->where(['code' => $electrical_pocket_code ])->first();

        }

        if(!$this->electronic_pocket)
            throw new \Exception("Error no fue posible obtener bolsillo para transacción. El usuario no tiene este tipo de bolsillo.", 1);
            

    }

    public function validateStorePermissions(){

        # PERMISOS CODIGO TIENE USUARIO QUE REGISTRA LA TRANSACCIÓN
        $store = auth()->user()->stores()->where(['code' => $this->store->code ])->first();
        if(!$store){
            throw new \Exception("Usuario no tiene permisos sobre el comercio.", 1);
        }

    }


    public function Validations(){

        # VALIDACIÓN GENERAL
        $validator = Validator::make($this->request->all(), $this->validation_rules, $this->validation_message);

        if ($validator->fails()) {            
            $errors = array_values($validator->errors()->toArray());
            $expection = '';
            foreach ($errors as $key => $error) {
                $separador = empty($expection) ? '':', ';
                $expection .= $separador.implode(', ', $error);
            }
            throw new \Exception($expection, 1);
        }

        # REQUEST
        $this->customer = (object) $this->request->customer;
        $this->transaction = (object) $this->request->transaction;

        # CARGA DE OBJETOS REQUERIDOS
        $this->movement_type = MovementType::where(['code' => $this->transaction->movement_type ])->first();
        $this->store = Store::where(['code' => $this->transaction->store])->first();

        $this->validateStorePermissions();

    }

    public function getMovemenRegister(){
        return $this->movement_register;
    }

}