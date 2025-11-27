<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\Process;
use App\Models\Service;
use App\Models\Status;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Exceptions\customException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProcessImport implements ToModel, WithHeadingRow
{

    /**
     * Mapeia os nomes das colunas para os índices correspondentes.
     */
    private function clientColumns($row)
    {
        return [
            'name' => $row['nome_do_cliente'] ?? null,
            'nuvem' => $row['link_da_pasta_do_cliente_na_nuvem'] ?? null,
            'phone' => $row['telefone '] ?? null,
            'birth_date ' => $row['data_de_nascimento'] ?? null,
            'license_date ' => $row['data_1a_habilitacao'] ?? null,
            'cpf' => $row['cpf'] ?? null,
            'state' => $row['estado'] ?? null,
            'city' => $row['cidade'] ?? null,
            'address' => $row['rua_av'] ?? null,
            'number' => $row['numero'] ?? null,
            'complement' => $row['complemento'] ?? null,
            'slug' => Client::uniqueSlug($row['nome_do_cliente']),
        ];
    }

    private function serviceColumns($row)
    {
        return [
            'name' => $row['servico'] ?? null,
            'slug' => Service::uniqueSlug($row['servico']),
        ];
    }

    private function processColumns($row)
    {
        $deadlineDate = $row['data_limite'] ?? null;
        $date = null;

        if ($deadlineDate) {
            try {
                // Verifica se a data está no formato YYYY-MM-DD
                $carbonDate = Carbon::createFromFormat('Y-m-d', $deadlineDate);
                $date = $carbonDate->format('Y-m-d');
            } catch (\Exception $e) {
                try {
                    // Tenta converter a data do formato d/m/Y para YYYY-MM-DD
                    $carbonDate = Carbon::createFromFormat('d/m/Y', $deadlineDate);
                    $date = $carbonDate->format('Y-m-d');
                } catch (\Exception $e) {
                    try {
                        // Tenta converter a data do formato Y-m-d H:i:s (formato datetime do Excel)
                        $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $deadlineDate);
                        $date = $carbonDate->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Lança uma exceção se a data não estiver em nenhum dos formatos esperados
                        throw new customException("Invalid date format for deadline_date: " . $deadlineDate, 400);
                    }
                }
            }
        }
        
        return [
            'plate' => $row['placa'] ?? null,
            'chassis' => $row['chassi'] ?? null,
            'renavam' => $row['renavam'] ?? null,
            'state_plate' => $row['uf_placa'] ?? null,
            'infraction_code' => $row['cod_infracao'] ?? null,
            'agency' => $row['orgao'] ?? null,
            'ait' => $row['ait'] ?? null,
            'process_value' => $row['valores'] ?? null,
            'payment_method' => $row['forma_de_pagamento'] ?? null,
            'observation' => $row['observacao'] ?? null,
            'process_number' => $row['numero_do_processo'] ?? null,
            'deadline_date' => $date,
            'seller' => $row['vendedor'] ?? null,
            'slug' => Process::uniqueSlug('process-' . $row['nome_do_cliente']),
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(empty($row['cpf'])) {
            return null;
        }
        $clientPayload = $this->clientColumns($row);
        $servicePayload = $this->serviceColumns($row);
        $processPayload = $this->processColumns($row);
        $statusTitle = $row['status'] ?? null;
        
        try {
            // Busca ou cria o cliente baseado no CPF
            // Se o cliente já existir, atualiza os dados; se não, cria um novo
            $client = Client::updateOrCreate(
                ['cpf' => $clientPayload['cpf']],
                $clientPayload
            );
            
            // Busca ou cria o serviço baseado no nome
            $service = null;
            if (!empty($servicePayload['name'])) {
                $service = Service::firstOrCreate(
                    ['name' => $servicePayload['name']],
                    $servicePayload
                );
            }
            
            // Cria o processo
            $processData = [
                'client_id' => $client->id,
            ];
            
            if ($service) {
                $processData['service_id'] = $service->id;
            }
            
            $process = Process::create(array_merge($processPayload, $processData));
            
            // Busca e vincula o status se fornecido
            if (!empty($statusTitle)) {
                $status = Status::where('title', $statusTitle)->first();
                if ($status) {
                    // Desativa todos os status anteriores do processo
                    $existingStatuses = $process->status()->wherePivot('is_active', true)->get();
                    foreach ($existingStatuses as $existingStatus) {
                        $process->status()->updateExistingPivot($existingStatus->id, ['is_active' => false]);
                    }
                    
                    // Vincula o novo status como ativo
                    $userId = Auth::check() ? Auth::user()->id : 1; // Fallback para user_id 1 se não houver autenticação
                    $process->status()->attach($status->id, [
                        'user_id' => $userId,
                        'is_active' => true,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }

            return [
                'client' => $client,
                'service' => $service,
                'process' => $process,
            ];

        } catch (\Exception $e) {
            throw new customException($e->getMessage(), 500);
        }
    }
}