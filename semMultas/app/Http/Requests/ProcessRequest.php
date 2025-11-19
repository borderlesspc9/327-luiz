<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessRequest extends FormRequest
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
        return [
            'user_id' => 'nullable|exists:users,id',
            'client_id' => 'nullable|exists:clients,id',
            'service_id' => 'nullable|exists:services,id',
            'seller_id' => 'nullable|exists:users,id',
            'plate' => 'nullable|string',
            'chassis' => 'nullable|string',
            'renavam' => 'nullable|string',
            'state_plate' => 'nullable|string',
            'infraction_code' => 'nullable|string',
            'agency' => 'nullable|string',
            'ait' => 'nullable|string',
            'process_value' => 'nullable|numeric',
            'payment_method' => 'nullable|string',
            'observation' => 'nullable|string',
            'process_number' => 'nullable|string',
            'deadline_date' => 'nullable|date',
            'status_id' => 'nullable',
            
            // Parâmetros de busca
            'params.search' => 'nullable|string',
            'params.limit' => 'nullable|integer',
            'params.order_by' => 'nullable|string',
            'params.order_direction' => 'nullable|string|in:asc,desc',
            'params.per_page' => 'nullable|integer',
            'params.without_pagination' => 'nullable|boolean',
            'params.filter' => 'nullable|array',
            
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */

    public function messages(): array
    {
        return [
            'user_id.exists' => 'O usuário informado não existe.',
            'seller_id.exists' => 'O vendedor informado não existe.',
            'client_id.exists' => 'O cliente informado não existe.',
            'service_id.exists' => 'O serviço informado não existe.',
            'plate.string' => 'A placa deve ser uma string.',
            'chassis.string' => 'O chassi deve ser uma string.',
            'renavam.string' => 'O renavam deve ser uma string.',
            'state_plate.string' => 'O estado da placa deve ser uma string.',
            'infraction_code.string' => 'O código da infração deve ser uma string.',
            'agency.string' => 'O órgão deve ser uma string.',
            'ait.string' => 'O AIT deve ser uma string.',
            'process_value.numeric' => 'O valor do processo deve ser um número.',
            'payment_method.string' => 'O método de pagamento deve ser uma string.',
            'observation.string' => 'A observação deve ser uma string.',
            'process_number.string' => 'O número do processamento deve ser uma string.',
            'deadline_date.date' => 'A data de vencimento deve ser uma data válida.',
        ];
    }
}
