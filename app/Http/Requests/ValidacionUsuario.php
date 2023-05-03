<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidacionUsuario extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'login' => 'required|unique:App\User,login',
            'name' => 'required|max:150',
            'email' => 'unique:App\User,email|max:150|email:rfc,dns|unique:App\User,email',
            'password' => 'required|max:255|confirmed'
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'El password es requerido',
            'id_u.required'  => 'Es necesario indicar la unidad',
            'id_sucursal.required'  => 'Es necesario indicar la sucursal',
        ];
    }
}
