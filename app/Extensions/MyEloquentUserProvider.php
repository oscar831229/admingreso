<?php
namespace App\Extensions;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use App\Models\Admin\AuthenticationLog;

class MyEloquentUserProvider extends EloquentUserProvider
{
    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];
        $hashed_value = $user->getAuthPassword();
        
        $match = $hashed_value == md5($plain);

        if(!$match){
            AuthenticationLog::create([
                'user_id' => $user->id,
                'ipaddress' => getUserIpAddr(),
                'observation' => 'Autenticación fallida'
            ]);
        }

        return $match;
    }
}