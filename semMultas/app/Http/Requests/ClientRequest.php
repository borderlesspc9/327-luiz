<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
    public function rules(): array
    {
        $client = $this->route('client');
        
        return [
            'name' => 'required',
            'nuvem' => 'nullable',
            'phone' => 'nullable',
            'birth_date' => 'nullable',
            'marital_status' => 'nullable',
            'license_date' => 'nullable|date',
            'cpf' => 'required|unique:clients,cpf,' . ($client ? $client->id : 'NULL'),
            'profession' => 'nullable',
            'cep' => 'nullable',
            'rg' => 'nullable',
            'state' => 'nullable',
            'city' => 'nullable',
            'neighborhood' => 'nullable',
            'address' => 'nullable',
            'number' => 'nullable',
            'complement' => 'nullable',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function messages(): array
    {   
        return [
            'name.required' => 'Nome do cliente é obrigatório',
            'cpf.required' => 'CPF do cliente é obrigatório',
            'cpf.unique' => 'CPF já cadastrado',
        ];
    }
}