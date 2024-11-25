<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;  // O ajusta según tus necesidades de autorización
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tipo_documento' => 'required|string|in:CC,TI,PP',  // Ejemplo de tipos permitidos
            'nro_documento'  => 'required|numeric|digits_between:7,10',  // Número de documento
            'tipo'           => 'required|integer|in:1,2',  // Validación para los valores posibles de tipo
        ];
    }

    /**
     * Mensajes personalizados de validación.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'tipo_documento.in' => 'El tipo de documento debe ser uno de los siguientes: CC, TI, PP.',
            'nro_documento.required' => 'El número de documento es obligatorio.',
            'nro_documento.numeric' => 'El número de documento debe ser numérico.',
            'nro_documento.digits_between' => 'El número de documento debe tener entre 7 y 10 dígitos.',
            'tipo.required' => 'El tipo es obligatorio.',
            'tipo.integer' => 'El tipo debe ser un número entero.',
            'tipo.in' => 'El tipo debe ser 1 o 2.',
        ];
    }

}
