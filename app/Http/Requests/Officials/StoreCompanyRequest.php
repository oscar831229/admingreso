<?php

namespace App\Http\Requests\Officials;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Officials\Company;

class StoreCompanyRequest extends FormRequest
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

        $company = Company::find($this->segment(3));

        switch($this->method())
        {
            case 'GET':

            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'code' => 'required|unique:companies|max:50',
                    'name' => 'required|max:150',
                    'type_identification_representative' => 'required|max:1',
                    'identificacion_representative' => 'required|max:15',
                    'name_representative' => 'required|max:150',
                    'phone' => 'required|max:15',
                    'address' => 'required|max:150'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'code' => 'required|unique:companies,code,'.$company->id.'|max:50',
                    'name' => 'required|max:150',
                    'type_identification_representative' => 'required|max:1',
                    'identificacion_representative' => 'required|max:15',
                    'name_representative' => 'required|max:150',
                    'phone' => 'required|max:15',
                    'address' => 'required|max:150',
                    'state' => 'required|max:150'
                ];

                // return [
                //     'user.name.first' => 'required',
                //     'user.name.last'  => 'required',
                //     'user.email'      => 'required|email|unique:users,email,'.$user->id,
                //     'user.password'   => 'required|confirmed',
                // ];
            }
            default:break;
        }

    }

    public function messages()
    {
        return [
            'code.required' => 'Nit empresa',
            'code.unique' => 'existe una empresa grabada con el mismo nit',
            'name.required' => 'Nombre empresa',
            'type_identification_representative.required' => 'Tipo documento representante',
            'identificacion_representative.required' => 'Identificación representante',
            'name_representative.required' => 'Nombre representante',
            'phone.required' => 'Teléfono representante',
            'address.required' => 'Dirección representante',
        ];
    }

}
