<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
        $roleId = $this->route('roles') ? $this->route('roles')->id : null;

        return [
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            
            'name' => [
                'required',
                'string',
                'max:255',
                $roleId ? 'unique:roles,name,' . $roleId : 'unique:roles,name',
            ],

        ];
    }

    /**
     * Get the custom attributes for the validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'nome',
            'permissions' => 'permissões',
            'permissions.*' => 'permissão',
        ];
    }

    /**
     * Get the custom messages for the validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'O campo :attribute é obrigatório.',
            'name.string' => 'O campo :attribute deve ser uma string.',
            'name.max' => 'O campo :attribute não pode ter mais de :max caracteres.',
            'name.unique' => 'Já existe um role com o nome :input.',
            'permissions.array' => 'O campo :attribute deve ser um array.',
            'permissions.*.exists' => 'A permissão selecionada não é válida.',
        ];
    }
}