<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientsExport implements FromCollection, WithHeadings
{
    protected $clients;

    public function __construct($clients)
    {
        $this->clients = $clients;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->clients as $client) {
            if ($client) {
                foreach ($client->processes as $process) {
                    if ($process) {
                        $data[] = [
                            'Item' => $client->id,
                            'Data' => $client->created_at,
                            'Nome do Cliente' => $client->name,
                            'Link da pasta do cliente na nuvem' => $client->nuvem,
                            'Telefone' => $client->phone,
                            'CPF' => $client->cpf,
                            'Data de nascimento' => $client->birth_date,
                            'Data 1ª habilitação' => $client->license_date,
                            'Placa' => $process->plate,
                            'Chassi' => $process->chassis,
                            'Renavam' => $process->renavam,
                            'UF Placa' => $process->state_plate,
                            'Cod. Infração' => $process->infraction_code,
                            'Serviço' => $process->service ? $process->service->name : '',
                            'Status' => $process->status ? ($activeStatus = $process->status->firstWhere('pivot.is_active', 1)) ? $activeStatus->title : '' : '',
                            'Órgão' => $process->agency,
                            'AIT' => $process->ait,
                            'Data de processamento' => $process->created_at,
                            'Data limite' => $process->deadline_date,
                            'Valores' => $process->process_value,
                            'Forma de pagamento' => $process->payment_method,
                            'Vendedor' => $process->seller,
                            'CEP' => $client->zip_code,
                            'Estado' => $client->state,
                            'Cidade' => $client->city,
                            'Bairro' => $client->neighborhood,
                            'Rua/Av' => $client->address,
                            'Número' => $client->number,
                            'Complemento' => $client->complement,
                        ];
                    }
                }
            }
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Item',
            'Data',
            'Nome do Cliente',
            'Link da pasta do cliente na nuvem',
            'Telefone',
            'CPF',
            'Data de nascimento',
            'Data 1ª habilitação',
            'Placa',
            'Chassi',
            'Renavam',
            'UF Placa',
            'Cod. Infração',
            'Serviço',
            'Status',
            'Órgão',
            'AIT',
            'Data de processamento',
            'Data limite',
            'Valores',
            'Forma de pagamento',
            'Vendedor',
            'CEP',
            'Estado',
            'Cidade',
            'Bairro',
            'Rua/Av',
            'Número',
            'Complemento',
        ];
    }
}