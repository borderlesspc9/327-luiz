<script setup lang="ts">
import { 
    ContainerComponent,
    LoadingComponent
} from '@/utils/components';
import { ref } from 'vue';
import CardTitleComponent from '@/components/CardTitleComponent.vue';
import CardShow from '@/components/CardShow.vue';

const isLoading = ref(false);

// Dados fictícios de busca
const dataResults = ref({
    data: {
        clients: [
            {
                id: 1,
                name: 'João Silva',
                cpf: '123.456.789-00',
                phone: '(11) 98765-4321',
            },
            {
                id: 2,
                name: 'Maria Santos',
                cpf: '987.654.321-00',
                phone: '(11) 91234-5678',
            },
        ],
        processes: [
            {
                id: 1,
                ait: '123456789',
                deadline_date: '15/12/2024',
                process_number: '2024001234',
                client: {
                    name: 'João Silva',
                    cpf: '123.456.789-00',
                }
            },
            {
                id: 2,
                ait: '987654321',
                deadline_date: '20/12/2024',
                process_number: '2024005678',
                client: {
                    name: 'Maria Santos',
                    cpf: '987.654.321-00',
                }
            },
        ],
        services: [
            {
                id: 1,
                name: 'Defesa de Multa',
            },
            {
                id: 2,
                name: 'Recurso de Multa',
            },
        ],
        users: [
            {
                id: 1,
                name: 'João Silva',
                email: 'joao.silva@email.com',
            },
            {
                id: 2,
                name: 'Maria Santos',
                email: 'maria.santos@email.com',
            },
        ],
    }
});
</script>

<template>
    <LoadingComponent :show="isLoading" />

    <CardTitleComponent :title="'Resultados de: '" />

    <ContainerComponent>
        <section v-if="dataResults && dataResults.data">
            <template v-if="dataResults.data.clients && dataResults.data.clients.length > 0">
                <CardShow title="Resultado em Clientes">
                    <div v-for="client in dataResults.data.clients" :key="client.id" class="card-body flex-body result">
                        <div class="column">
                            <div class="card-text card-flex">
                                <span>Nome:</span><span>{{ client.name }}</span>
                            </div>
                            <div class="card-text card-flex">
                                <span>Cpf:</span><span>{{ client.cpf }}</span>
                            </div>
                            <div class="card-text card-flex">
                                <span>Telefone:</span><span>{{ client.phone }}</span>
                            </div>
                        </div>
                    </div>
                </CardShow>
            </template>
            
            <template v-if="dataResults.data.processes && dataResults.data.processes.length > 0">
                <CardShow title="Resultado em Processos">
                    <div v-for="process in dataResults.data.processes" :key="process.id" class="card-body flex-body result">
                        <div class="column">
                            <div class="card-text card-flex">
                                <span>AIT:</span><span>{{ process.ait }}</span>
                            </div>
                            <div class="card-text card-flex">
                                <span>Data limite:</span><span>{{ process.deadline_date }}</span>
                            </div>
                            <div class="card-text card-flex">
                                <span>Número do Processamento:</span><span>{{ process.process_number }}</span>
                            </div>
                        </div>
                    </div>
                </CardShow>
            </template>
            
            <template v-if="dataResults.data.services && dataResults.data.services.length > 0">
                <CardShow title="Resultado em Serviços">
                    <div v-for="service in dataResults.data.services" :key="service.id" class="card-body flex-body result">
                        <div class="column">
                            <div class="card-text card-flex">
                                <span>Nome:</span><span>{{ service.name }}</span>
                            </div>
                        </div>
                    </div>
                </CardShow>
            </template>
            
            <template v-if="dataResults.data.users && dataResults.data.users.length > 0">
                <CardShow title="Resultado em Usuários">
                    <div v-for="user in dataResults.data.users" :key="user.id" class="card-body flex-body result">
                        <div class="column">
                            <div class="card-text card-flex">
                                <span>Nome:</span><span>{{ user.name }}</span>
                            </div>
                            <div class="card-text card-flex">
                                <span>E-mail:</span><span>{{ user.email }}</span>
                            </div>
                        </div>
                    </div>
                </CardShow>
            </template>
        </section>
    </ContainerComponent>
</template>
