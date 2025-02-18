<?php

namespace App\Http\Requests\Mail;

use Illuminate\Foundation\Http\FormRequest;

class ValidarMail extends FormRequest
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
            'server' => 'required|max:255',
            'encryption' => 'max:3',
            'puerto' => 'required|max:10',
            'email' => "required|max:150|unique:emails,email",
            'password' => 'max:150'
        ];
    }


}
