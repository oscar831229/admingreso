<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Models\Income\IcmEnvironment;

class UsersEnvironmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('income.users-environments.index');
    }

    public function findUsersEnvironment(){

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
            $entity_user = $user->icm_environments()->where(['icm_environment_id' => $request->post('icm_environment_id')])->first();

            if(!$entity_user){
                $icmentity = IcmEnvironment::findOrFail($request->post('icm_environment_id'));
                $user->icm_environments()->attach($icmentity, ['user_created' => $request->user()->id]);
            }else{
                $user->icm_environments()->detach($request->post('icm_environment_id'));
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => [
                'user' => $user,
                'environments' => $user->getUsersEnvironments()
            ]
        ]);

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
