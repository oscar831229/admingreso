<?php 
namespace App\Clases\ElectronicWallet\Movement;

use Validator;
use App\Models\Wallet\ElectricalPocket;
use App\Models\Wallet\WalletUser;
use App\Models\Wallet\Movement;
use App\Models\Wallet\MovementType;
use App\Models\Wallet\Store;

use App\Rules\ExistCusWalletUser;
use App\Clases\ElectronicWallet\TicketHelperClass;

class ReversePaymentClass
{

    private $validation_rules = null;

    private $validation_message = null;

    private $create_wallet_user = false;

    private $create_electronic_pocket_ws = false;

    private $customer = null;

    private $transaction = null;

    /**
     *  CONTROLA TIPO DE MOVIMIENTO REALIZADO
     */
    public $movement_type_code = '02';

    public function __construct($request){

        $this->request = $request;

        $this->user = auth()->user();

        # REGLAS DE VALIDACIÓN
        $this->validation_rules = [
            'customer.document_number' => 'required|exists:wallet_users,document_number',
            'transaction.user_code' => 'required',
            'transaction.cus_transaction' => [
                'required',
                new ExistCusWalletUser($this->movement_type_code),
                'exists:movements,cus'
            ]
        ];

        # MENSAJES DE VALIDACION
        $this->validation_message = [
            'customer.document_number.required' => 'Número de identificación del cliente requerida',
            'customer.document_number.exists' => 'No existe el usuario sobre el cual va a realizar la transacción',
            'transaction.user_code.required' => 'Código de usuario requerido para proceso',
            'transaction.cus_transaction.required' => 'Número CUS requerido',
            'transaction.cus_transaction.exists' => 'No existe transacción con el CUS indicado'
        ];

    }

    public function execute(){
    
        # VALIDACIÓN PREVIAS
        $this->Validations();

        // # VALIDACION TOKEN
        // $token = Crypt::decryptString($this->wallet_user->token);
        // if($this->customer->token != $token){
        //     throw new \Exception("Error en validación de token usuario.", 1);
        // }

        # CARGAR BOLSILLO ELECTRONICO USUARIO TRANSACCION
        // $this->getElectronicPocketWalletUser($this->transaction->electrical_pocket);

        # BOLSILLO USUARIO 
        // $electrical_pocket_wallet_user= $this->electronic_pocket->pivot;

        # VALIDAR SALDO OJOOOOO
        // if($this->transaction->value > $electrical_pocket_wallet_user->balance){
        //     throw new \Exception("'No existe saldo suficiente para realizar operación'", 1);
        // }

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
                'electrical_pocket_wallet_user_id' => $this->movement_parent->electrical_pocket_wallet_user_id,
                'wallet_user_id' => $this->movement_parent->wallet_user_id,
                'electrical_pocket_id' => $this->movement_parent->electrical_pocket_id,
                'movement_type_id' => $this->movement_type->id,
                'nature_movement' => $this->movement_type->nature_movement,
                'value' =>  $this->movement_parent->value,
                'user_code' => $this->transaction->user_code,
                'store_id' => $this->store->id,
                'cus' => $cus_code,
                'movement_date' => date('Y-m-d'),
                'user_created' => $this->user->id,
                'cus_transaction' => $this->movement_parent->cus
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

    public function loadComponent(){

        # REQUEST
        $this->customer = (object) $this->request->customer;
        $this->transaction = (object) $this->request->transaction;

        # CARGA DE OBJETOS REQUERIDOS
        $this->getWalletUser();

        # CARGA DE COMPONENTES EXTRAS
        $this->movement_type = MovementType::where(['code' => $this->movement_type_code ])->first();

        # MOVIENTO REVERSADO
        $this->movement_parent = Movement::where(['cus' => $this->transaction->cus_transaction, 'wallet_user_id' => $this->wallet_user->id ])->first();

        # CARGAR STORE
        $this->store = Store::find($this->movement_parent->store_id);
        
    }


    public function Validations(){

        # VALOR - CODIGO USUARI PROCESO.
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

        # CARGAMOS COMPONENTES NECESARIAS
        $this->loadComponent();

        # VERIFICAR SI EL USUARIO TIENE PERMISOS SOBRE EL COMERCIO
        $this->validateStorePermissions();

    }

    public function getMovemenRegister(){
        return $this->movement_register;
    }

    

}