<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoriaRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $categoria = $this->route('categoria');//Recupera lo que tiene en la ruta en la variable categoría
        $caracteristicaId = $categoria->caracteristica->id;
        return [
            'nombre' => 'required|max:60|min:3|unique:caracteristicas,nombre,'.$caracteristicaId,
            'descripcion' => 'min:3|nullable|max:255'
        ];
    }
}