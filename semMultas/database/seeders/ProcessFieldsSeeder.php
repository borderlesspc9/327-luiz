<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProcessFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            [
                'name' => 'plate',
                'label' => 'Placa',
                'type' => 'text',
                'required' => true,
                'mask' => '',
                'description' => 'Placa do veículo',
            ],
            [
                'name' => 'chassis',
                'label' => 'Chassi',
                'type' => 'text',
                'required' => true,
                'mask' => '',
                'description' => 'Chassi do veículo',
            ],
            [
                'name' => 'renavam',
                'label' => 'renavam',
                'type' => 'text',
                'required' => true,
                'mask' => '',
                'description' => 'Renavam do veículo',
            ],
            [
                'name' => 'state_plate',
                'label' => 'Estado da placa',
                'type' => 'text',
                'required' => true,
                'mask' => '',
                'description' => 'Estado da placa do veículo',
            ],
            [
                'name' => 'infraction_code',
                'label' => 'Código da infração',
                'type' => 'text',
                'required' => true,
                'mask' => '',
                'description' => 'Código da infração',
            ],
            [
                'name' => 'agency',
                'label' => 'Orgão',
                'type' => 'text',
                'required' => true,
                'mask' => '',
                'description' => 'Órgão autuador',
            ],
            [
                'name' => 'observation',
                'label' => 'Observação',
                'type' => 'text',
                'required' => true,
                'mask' => '',
                'description' => 'Observação',
            ],
            [
                'name' => 'process_number',
                'label' => 'Número do processamento',
                'type' => 'text',
                'required' => true,
                'mask' => '',
                'description' => 'Número do processo',
            ],
        ];

        foreach ($fields as $field) {
            \App\Models\ProcessField::create($field);
        }
    }
}
