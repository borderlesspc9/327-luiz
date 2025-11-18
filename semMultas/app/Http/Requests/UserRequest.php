<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;
    
        return [
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                $userId ? 'unique:users,email,' . $userId : 'unique:users,email',
            ],
            'password' => [
                'string',
                'min:6',
                'max:100',
                !$userId ? 'required' : 'nullable',
            ],
            'roles' => 'required',
        ];
    }
    

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.unique' => 'O e-mail já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ];
    }
    
}
