<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Email;

# EMAIL
use App\Clases\Mail\Cuenta;
use App\Clases\Mail\Correo;
use App\Clases\Mail\sendMail;

# REQUESTS
use App\Http\Requests\Mail\ValidarMail;
use App\Http\Requests\Mail\ValidarMailUpdate;


class EmailController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:emails-list|emails-create|emails-edit|emails-delete', ['only' => ['index','store']]);
         $this->middleware('permission:emails-create', ['only' => ['create','store']]);
         $this->middleware('permission:emails-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:emails-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		# email
        $emails = Email::orderBy('id')->get();

        return view('admin.emails.index', compact('emails'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.emails.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidarMail $request)
    {
        $input = $request->all();
        $input['user_id']  = auth()->user()->id;


        $user = Email::create($input);
    
        return redirect()->route('emails.index')
                        ->with('success','Email creado con exito.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $email = Email::findOrFail($id);

        return view('admin.emails.edit',compact('email'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ValidarMailUpdate $request, $id)
    {
        $email = Email::findOrFail($id);

        $email->update($request->all());

        return redirect()->route('emails.index')
                        ->with('success','La email fue actualizado.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Email::find($id)->delete();
        return redirect()->route('emails.index')
                        ->with('success','Email eliminado con exito');
    }


    public function testMail(Request $request)
    {

        $response = [
            'success' => true,
            'error' => ''
        ];

        # Cuenta SMTP    
        $cuenta = new Cuenta;
        $cuenta->setServer($request->input('server'))
            ->setPuerto($request->input('puerto'))
            ->setEncryption($request->input('encryption'))
            ->setEmail($request->input('email'))
            ->setPassword($request->input('password'));


        # Mensaje prueba de conexión
        $correo = new Correo;
        $correo->setAsunto('prueba conexion cuenta')
            ->setMensaje('Correo de verificación datos de autenticación email.')
            ->setPara($request->input('email'));

        # Envio correo de prubas
        $sendMail = new sendMail;
        $sendMail->setCuenta($cuenta);
        $sendMail->setCorreo($correo);

        $sendMail->send();

        if(!empty($sendMail->error)){
            $response['success'] = false;
            $response['error'] = $sendMail->error;
        }

        return $response;
    }
}
