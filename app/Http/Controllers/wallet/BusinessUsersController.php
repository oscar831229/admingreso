<?php

namespace App\Http\Controllers\wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Models\Wallet\Store;

class BusinessUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('wallet.business-users.index');
    }

    public function loadUserPermissions($user_id){

        $user = User::findOrFail($user_id);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => [
                'user' => $user,
                'companies' => $user->getStores()
            ]
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $response = [
            'success' => true,
            'message' => '',
            'data' => []
        ];

        try {

            $user = User::findOrFail($request->post('user_id'));

            # CONSULTAR PERMISO USUARIOS
            $store_user = $user->stores()
                ->where([
                    'store_id' => $request->post('store_id')
                ])->first();

            if(!$store_user){
                $store = Store::findOrFail($request->post('store_id'));
                $user->stores()->attach($store, ['user_created' => $request->user()->id]);
            }else{
                $user->stores()->detach($request->post('store_id'));
            }

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => []
            ]);

        } catch (\Exception $e) {
            $response['success'] = true;
            $response['message'] = $e->getMessage();
        }

        return response()->json($response);

    }

    public function findUser(){

        $username = $_GET['username'];
        $users = findUser($username);

        $arr = array('suggestions'=>array());

        foreach($users as $key=>$user){
            
            $arr['suggestions'][]= array(
                'value'=> $user->login.' -- '.trim($user->name),
                'data'=>array(
                    'userid'=>$user->id
                )
            );
         }

        return response()->json($arr);

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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
