<?php

namespace App;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login','password','name', 'email','active', 'document_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function setSession($roles)
    {
        Session::put([
            'usuario' => $this->login,
            'usuario_id' => $this->id,
            'nombre_usuario' => $this->name
        ]);
        if (count($roles) == 1) {
            Session::put(
                [
                    'rol_id' => $roles[0]['id'],
                    'rol_nombre' => $roles[0]['name'],
                ]
            );
        } else {
            Session::put('roles', $roles);
        }
    }


    public function units(){
        return $this->belongsToMany('App\Models\Admin\MedicalUnit','medical_units_users')
            ->withPivot('medical_unit_id');
    }

    public function getCompanies(){

        $companies = $this->select(
                        'companies.id as company_id',
                        'companies.code as company_code',
                        'companies.name as company_name',
                        'companies.state as company_state',
                        'company_user.id as user_company_id'
                    )
                    ->crossJoin('companies')
                    ->leftJoin('company_user', function($query){
                        $query->on('company_user.user_id','=','users.id');
                        $query->on('company_user.company_id','=','companies.id');
                    })
                    ->where(['users.id' => $this->id])
                    ->get();

        return $companies;

    }

    public function getpermissionsOnDonorAlerts(){

        $companies = $this->select(
                'pda_permission_users.id',
                'pda_permission_users.document_number',
                'pda_permission_users.name as user_name',
                'pda_permission_user_user.id as pda_permission_user_user_id'    
            )
            ->crossJoin('pda_permission_users')
            ->leftJoin('pda_permission_user_user', function($query){
                $query->on('pda_permission_user_user.user_id','=','users.id');
                $query->on('pda_permission_user_user.pda_permission_user_id','=','pda_permission_users.id');
            })
            ->where(['users.id' => $this->id])
            ->get();

        return $companies;

    }


    public function companies()
    {
        return $this->belongsToMany('App\Models\Officials\Company');
    }

    public function permissionuser()
    {
        return $this->belongsToMany('App\Models\PossibleDonor\PdaPermissionUser');
    }

    public function enableCompany($company_id){
        $companies = $this->companies()->where(['companies.id' => $company_id])->first();
        return $companies;
    }


}
