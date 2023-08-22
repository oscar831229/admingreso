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
use App\Clases\Mail\MainSendMail;
use Illuminate\Support\Str;


class PaymentClass
{

    private $validation_rules = [
        'customer.document_number' => 'required',
        'customer.first_name' => 'required',
        'customer.first_surname' => 'required',
        'customer.email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
        'customer.phone' => 'required|min:11|numeric',
        'transaction.electrical_pocket' => 'required|exists:electrical_pockets,code',
        'transaction.value' => 'required|numeric',
        'transaction.user_code' => 'required',
        'transaction.store' => 'required|exists:stores,code'
    ];

    private $validation_message = [
        'customer.document_number.required' => 'Número de identificación del cliente requerida',
        'customer.first_name.required' => 'Primer nombre del cliente requerido',
        'customer.first_surname.required' => 'Primer apellido del cliente requerido',
        'customer.email.required' => 'Email cliente requerido',
        'customer.email.regex' => 'Correo electrónico invalido',
        'customer.phone.required' => 'Teléfono del cliente requerido',
        'customer.phone.min' => 'Teléfono minimo 11 numeros',
        'customer.phone.numeric' => 'Teléfono cliente debe ser numero',
        'transaction.electrical_pocket.required' => 'Código del bolsillo es requerido',
        'transaction.electrical_pocket.exists' => 'No existe bolsillo del código indicado',
        'transaction.value.required' => 'Valor de la transacción requerido',
        'transaction.value.numeric' => 'Valor de la transacción debe ser numérico',
        'transaction.user_code.required' => 'El código del usuario que genera el proceso es requerido',
        'transaction.store.required' => 'Código del comercio requerido',
        'transaction.store.exists' => 'No existe el comercio indicado'
    ];

    /**
     *  CONTROLA TIPO DE MOVIMIENTO REALIZADO
     */
    public $movement_type_code = '01';

    /**
     *  Controla la creación de usuarios sobre el proceso
     *  Solo aplica en movimientos de Abono (true)
     */
    private $create_wallet_user = true;

    /**
     * Controla la creación de bolsillo al usuario en el proceso
     * Solo aplica en movimientos de Abono (true)
     */
    private $create_electronic_pocket_ws = true;

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

        # CARGAR BOLSILLO ELECTRONICO USUARIO TRANSACCION
        $this->getElectronicPocketWalletUser($this->transaction->electrical_pocket);
        
        # Instanciamos el Helper Tiquetera
        $ticekhelper = new TicketHelperClass($this);
        $ticekhelper->runValidation();

        # GENERA CODIGO UNICO DE SEGUIMIENTO
        $cus_code = $this->generateCUSCode();
        
        # BOLSILLO USUARIO 
        $electrical_pocket_wallet_user= $this->electronic_pocket->pivot;

        \DB::beginTransaction();

        try {

            $this->movement_register = Movement::create([
                'electrical_pocket_wallet_user_id' => $electrical_pocket_wallet_user->id,
                'wallet_user_id' => $electrical_pocket_wallet_user->wallet_user_id,
                'electrical_pocket_id' => $this->electronic_pocket->id,
                'movement_type_id' => $this->movement_type->id,
                'nature_movement' => $this->movement_type->nature_movement,
                'value' =>  $this->transaction->value,
                'user_code' => $this->transaction->user_code,
                'transaction_document_number' => $this->transaction->transaction_document_number,
                'store_id' => $this->store->id,
                'cus' => $cus_code,
                'movement_date' => date('Y-m-d'),
                'electrical_pocket_operation_type' => $this->electronic_pocket->operation_type,
                'user_created' => $this->user->id
            ]);

            $ticekhelper->generateTickets($this->movement_register);

            \DB::commit();

        } catch (\Exception $e){
            \DB::rollBack();
            dd($e->getMessage());
            return "Error de actulización de datos";
        }
        
        # CREAR MOVIMIENTO
        

    }

    public function getWalletUser(){
        
        $this->wallet_user = WalletUser::where(['document_number' => $this->customer->document_number] )->first();

        if(!$this->wallet_user && $this->create_wallet_user){

            # GENERAR TOKEN 
            $token_key = Str::random(15);
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
                'user_code_create' => $this->transaction->user_code,
                'user_created' => auth()->user()->id
            ]);
            
        }

        # SI NO EXISTE EL USUARIO DE TIQUETERA ELECTRÓNICA
        if(!$this->wallet_user){
            throw new Exception("Error Processing Request", 1);
        }

        MainSendMail::send('send_new_token', ['id' => $this->wallet_user->id ], [$this->wallet_user->email]);

    }

    public function generateCUSCode(){
        return generarCodigo(8);
    }


    public function getElectronicPocketWalletUser($electrical_pocket_code){

        # VERIFICAR Y CREAR BOLSILLO
        $this->electronic_pocket = $this->wallet_user->ElectronicPockets()->where(['code' => $electrical_pocket_code ])->first();
        if(!$this->electronic_pocket  && $this->create_electronic_pocket_ws){

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
            throw new \Exception("Error no fue posible obtener bolsillo para transacción.", 1);
            

    }

    public function validateStorePermissions(){

        # PERMISOS CODIGO TIENE USUARIO QUE REGISTRA LA TRANSACCIÓN
        $store = auth()->user()->stores()->where(['code' => $this->store->code ])->first();
        if(!$store){
            throw new \Exception("Usuario no tiene permisos sobre el comercio.", 1);
        }

    }


    public function Validations(){

        # VALOR - CODIGO USUARIO PROCESO.
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

        # CARGAR DATOS TRANSACCIÓN
        $this->customer = (object) $this->request->customer;
        $this->transaction = (object) $this->request->transaction;
        $this->movement_type = MovementType::where(['code' => $this->movement_type_code ])->first();
        $this->store = Store::where(['code' => $this->transaction->store])->first();

        # VERIFICA PERSMISOS DEL USUARIO AUTENTICADO TENGA PERMISOS CON EL COMERCIO
        $this->validateStorePermissions();

    }


    public function MovementValidations(){

        $validation_rules = [
            'transaction.electrical_pocket' => 'required|exists:electrical_pockets,code',
            'transaction.value' => 'required|numeric',
            'transaction.store' => 'required|exists:stores,code'
        ];

        # VALOR - CODIGO USUARIO PROCESO.
        $validator = Validator::make($this->request->all(), $validation_rules, $this->validation_message);

        if ($validator->fails()) {            
            $errors = array_values($validator->errors()->toArray());
            $expection = '';
            foreach ($errors as $key => $error) {
                $separador = empty($expection) ? '':', ';
                $expection .= $separador.implode(', ', $error);
            }
            throw new \Exception($expection, 1);
        }

        # CARGAR DATOS TRANSACCIÓN
        $this->customer = (object) $this->request->customer;
        $this->transaction = (object) $this->request->transaction;
        $this->movement_type = MovementType::where(['code' => $this->movement_type_code ])->first();
        $this->store = Store::where(['code' => $this->transaction->store])->first();

        # VERIFICA PERSMISOS DEL USUARIO AUTENTICADO TENGA PERMISOS CON EL COMERCIO
        $this->validateStorePermissions();

        # CARGAR BOLSILLO ELECTRONICO USUARIO TRANSACCION
        $this->electronic_pocket = ElectricalPocket::where(['code' => $this->transaction->electrical_pocket ])->first();

        # VALIDACION TIQUETERA
        $ticekhelper = new TicketHelperClass($this);
        $ticekhelper->runValidation();
        

    }




    public function getMovemenRegister(){
        return $this->movement_register;
    }

}