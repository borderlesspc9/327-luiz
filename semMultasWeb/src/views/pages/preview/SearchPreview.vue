<script setup lang="ts">
import { 
    ContainerComponent,
    LoadingComponent
} from '@/utils/components';
import type { RequestParams } from '@/interfaces/request-params.interface';
import { useRouter } from 'vue-router';
import type { Pagination } from '@/interfaces/pagination/pagination.interface'
import CardTitleComponent from '@/components/CardTitleComponent.vue';
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import CardShow from '@/components/CardShow.vue';

// Mock data
const mockClients = ref([
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
]);

const mockProcesses = ref([
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
]);

const mockServices = ref([
    {
        id: 1,
        name: 'Defesa de Multa',
    },
    {
        id: 2,
        name: 'Recurso de Multa',
    },
]);

const mockUsers = ref([
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
]);

// Simulate API delay
const simulateDelay = (ms: number = 500) => new Promise(resolve => setTimeout(resolve, ms));

const isLoading = ref(false);

const route = useRoute();

const params = ref<RequestParams>({
    without_pagination: 0,
    search: ''
});

const dataResults = ref<any>({ data: [] });

onMounted(async () => {
  await loadData();
});

const users = ref<Pagination>({
    current_page: 0,
    data: [],
    first_page_url: '',
    from: 0,
    last_page: 0,
    last_page_url: '',
    links: [],
    next_page_url: '',
    path: '',
    per_page: 0,
    prev_page_url: '',
    to: 0,
    total: 0,
});

const router = useRouter();

const loadData = async () => {
  if (params.value && params.value.search !== undefined) {
    params.value.search = route.query.q as string || '';
    if (params.value.search != '') {
      isLoading.value = true;
      await simulateDelay(500);
      
      // Simulate search across all entities
      const search = params.value.search.toLowerCase();
      
      const clients = mockClients.value.filter(c => 
        c.name?.toLowerCase().includes(search) ||
        c.cpf?.toLowerCase().includes(search) ||
        c.phone?.toLowerCase().includes(search)
      );
      
      const processes = mockProcesses.value.filter(p => 
        p.ait?.toLowerCase().includes(search) ||
        p.process_number?.toLowerCase().includes(search) ||
        p.client?.name?.toLowerCase().includes(search) ||
        p.client?.cpf?.toLowerCase().includes(search)
      );
      
      const services = mockServices.value.filter(s => 
        s.name?.toLowerCase().includes(search)
      );
      
      const users = mockUsers.value.filter(u => 
        u.name?.toLowerCase().includes(search) ||
        u.email?.toLowerCase().includes(search)
      );
      
      dataResults.value = {
        data: {
            clients: clients,
            processes: processes,
            services: services,
            users: users,
        }
      };
      
      isLoading.value = false;
    }
  }
}
</script>

<template>
  <LoadingComponent :show="isLoading" />

  <CardTitleComponent :title="`Resultados de: ${params.search || ''}`" />

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
