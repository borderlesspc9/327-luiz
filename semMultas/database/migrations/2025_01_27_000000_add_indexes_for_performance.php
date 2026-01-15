<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adicionar índices na tabela clients para campos usados em buscas
        Schema::table('clients', function (Blueprint $table) {
            $table->index('slug', 'clients_slug_index');
            $table->index('cpf', 'clients_cpf_index');
            $table->index('name', 'clients_name_index');
            $table->index('phone', 'clients_phone_index');
            $table->index('state', 'clients_state_index');
            $table->index('city', 'clients_city_index');
        });

        // Adicionar índices na tabela processes para campos usados em buscas
        Schema::table('processes', function (Blueprint $table) {
            $table->index('slug', 'processes_slug_index');
            $table->index('ait', 'processes_ait_index');
            $table->index('plate', 'processes_plate_index');
            $table->index('chassis', 'processes_chassis_index');
            $table->index('renavam', 'processes_renavam_index');
            $table->index('process_number', 'processes_process_number_index');
            $table->index('deadline_date', 'processes_deadline_date_index');
            $table->index('client_id', 'processes_client_id_index');
        });

        // Adicionar índice na tabela status
        Schema::table('status', function (Blueprint $table) {
            $table->index('slug', 'status_slug_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('clients_slug_index');
            $table->dropIndex('clients_cpf_index');
            $table->dropIndex('clients_name_index');
            $table->dropIndex('clients_phone_index');
            $table->dropIndex('clients_state_index');
            $table->dropIndex('clients_city_index');
        });

        Schema::table('processes', function (Blueprint $table) {
            $table->dropIndex('processes_slug_index');
            $table->dropIndex('processes_ait_index');
            $table->dropIndex('processes_plate_index');
            $table->dropIndex('processes_chassis_index');
            $table->dropIndex('processes_renavam_index');
            $table->dropIndex('processes_process_number_index');
            $table->dropIndex('processes_deadline_date_index');
            $table->dropIndex('processes_client_id_index');
        });

        Schema::table('status', function (Blueprint $table) {
            $table->dropIndex('status_slug_index');
        });
    }
};
