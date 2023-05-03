<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ValidacionConsecutivo extends FormRequest
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
        $id = $this->route('consecutivo') ?? 0;
        return [
            'prefijo' => 'required|max:10|unique:consecutivos,prefijo,'.$id,
            'consecutivo_inicial' => 'required|integer',
            'consecutivo_final' => 'required|integer',
            'estado' => 'required|max:1',
            'observacion' => 'max:255',
        ];
    }
}
