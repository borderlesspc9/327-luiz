<script setup lang="ts">
import { 
    TableComponent,
    ModalComponent,
    ButtonComponent,
    CardTitleComponent,
    ContainerComponent,
    CardComponent,
    FormGroupComponent,
    InputComponent,
    MultipleSelectComponent,
    LoadingComponent,
    LabelComponent
} from '@/utils/components';
import { ref } from 'vue';
import type { Action } from '@/interfaces/actions.interface';
import type { Pagination } from '@/interfaces/pagination/pagination.interface';
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';
import SearchComponent from '@/components/SearchComponent.vue';
import PaginationComponent from '@/components/PaginationComponent.vue';
import type { RequestParams } from '@/interfaces/request-params.interface';

const isLoading = ref(false);
const showDeleteModal = ref(false);
const showModal = ref(false);
const currentItem = ref(null);

const form = ref({
    slug: '',
    name: '',
    process_fields: [],
});

const params = ref<RequestParams>({
    without_pagination: 0,
    page: 1
});

// Dados fictícios
const servicesData = ref([
    {
        id: 1,
        slug: 'defesa-multa',
        Nome: 'Defesa de Multa',
    },
    {
        id: 2,
        slug: 'recurso-multa',
        Nome: 'Recurso de Multa',
    },
    {
        id: 3,
        slug: 'consulta-veiculo',
        Nome: 'Consulta de Veículo',
    },
]);

const pagination = ref<Pagination>({
    current_page: 1,
    data: [],
    first_page_url: '',
    from: 1,
    last_page: 1,
    last_page_url: '',
    links: [],
    next_page_url: '',
    path: '',
    per_page: 10,
    prev_page_url: '',
    to: 10,
    total: 3,
});

const processSelectData = ref([
    { value: 1, text: 'Código da Infração' },
    { value: 2, text: 'Órgão' },
    { value: 3, text: 'Número do Processamento' },
    { value: 4, text: 'Observação' },
]);

const getItemById = (id: number) => servicesData.value.find((item) => item.id === id);

const handlePageChange = async (pageUrl: string) => {
    if (pageUrl) {
        isLoading.value = true;
        const urlParams = new URL(pageUrl);
        const page = parseInt(urlParams.searchParams.get('page') || '1', 10);
        params.value.page = page;
        isLoading.value = false;
    }
};

const handlePerPageChange = (newPerPage: any) => {
    params.value.per_page = newPerPage;
    params.value.page = 1;
};

const handleSearch = (searchTerm: string) => {
    params.value.search = searchTerm;
};

const actions: Action[] = [
    {
        name: 'edit',
        hasPermission: true,
        action: (item) => {
            currentItem.value = getItemById(item.id);
            if (currentItem.value) {
                form.value.name = currentItem.value.Nome;
                form.value.slug = currentItem.value.slug;
                form.value.process_fields = [1, 2];
            }
            showModal.value = true;
        },
        icon: 'edit',
        class: 'light blue',
    },
    {
        name: 'delete',
        hasPermission: true,
        action: (item) => {
            currentItem.value = getItemById(item.id);
            showDeleteModal.value = true;
        },
        icon: 'trash',
        class: 'light red',
    },
];

const handleModalTitle = () => {
    return form.value.name != '' ? 'Editar Serviço' : 'Adicionar Serviço';
};

const handleCloseModal = () => {
    showModal.value = false;
    clearForm();
};

const handleSubmit = () => {
    console.log('Salvar serviço', form.value);
    clearForm();
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    currentItem.value = null;
};

const confirmDelete = () => {
    if (currentItem.value) {
        servicesData.value = servicesData.value.filter(s => s.id !== currentItem.value.id);
    }
    closeDeleteModal();
};

const clearForm = () => {
    form.value.name = '';
    form.value.process_fields = [];
    form.value.slug = '';
};
</script>

<template>
    <LoadingComponent :show="isLoading" />

    <ConfirmDeleteModalComponent 
        :show="showDeleteModal" 
        @closeModal="closeDeleteModal" 
        @confirmDelete="confirmDelete"
    />

    <ModalComponent sizeClass="modal-service" :show="showModal" @closeModal="handleCloseModal" :titleHeader="handleModalTitle()" @submit="handleSubmit">
        <FormGroupComponent>
            <LabelComponent text="Nome" />
            <InputComponent v-model="form.name" placeholder="Nome" type="text" />
        </FormGroupComponent>
        <FormGroupComponent>
            <LabelComponent text="Campos de processo" />
            <MultipleSelectComponent v-model="form.process_fields" :options="processSelectData" placeholder="Pesquisar e adicionar processos" />
        </FormGroupComponent>
    </ModalComponent>

    <CardTitleComponent title="Serviços" />

    <ContainerComponent>
        <CardComponent titleCard="Lista de serviços">
            <template #search>
                <SearchComponent @update-search="handleSearch" :value="params.search" />
            </template>

            <template #button>
                <ButtonComponent buttonClass="btn-secondary" text="Adicionar" icon="plus" light @click="showModal = true" />
            </template>        
            <TableComponent 
                :items="servicesData" 
                :actions="actions" 
            />

            <PaginationComponent
                :items="servicesData" 
                :pagination="pagination" 
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange"
            />
        </CardComponent>
    </ContainerComponent>
</template>
