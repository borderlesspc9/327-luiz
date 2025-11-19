<script setup lang="ts">
import { 
    ContainerComponent,
    LoadingComponent
} from '@/utils/components';
import type { RequestParams } from '@/interfaces/request-params.interface';
import { useRouter } from 'vue-router';
import type { Pagination } from '@/interfaces/pagination/pagination.interface'
import CardTitleComponent from '@/components/CardTitleComponent.vue';
import { useSearchStore } from '@/stores/search.store';
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import CardShow from '@/components/CardShow.vue';

const isLoading = ref(false);

const searchStore = useSearchStore();
const route = useRoute();

const params = ref<RequestParams>({
    without_pagination: 0,
    search: ''
});

const dataResults = ref<any>({ data: [] }); // Inicialize com uma estrutura vazia

onMounted(async () => {
  await loadData();
  dataResults.value = searchStore.getSearchResults; // Corrigido: sem os parênteses
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

const searchResult = computed(() => searchStore.result);
const router = useRouter();

const loadData = async () => {
  if (params.value && params.value.search !== undefined) {
    params.value.search = route.query.q as string || '';
    if (params.value.search != '') {
      isLoading.value = true;
      await searchStore.search(params.value);
      dataResults.value = searchStore.getSearchResults; // Certifique-se de que os dados são atribuídos aqui
      isLoading.value = false;
    }
  }
}
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
                            <span>Número do processo:</span><span>{{ process.process_number }}</span>
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
            <CardShow title="Resultado em Serviços">
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