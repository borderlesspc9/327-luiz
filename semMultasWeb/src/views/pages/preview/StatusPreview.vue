<script setup lang="ts">
import { 
    TableComponent,
    ModalComponent,
    ButtonComponent,
    CardTitleComponent,
    ContainerComponent,
    CardComponent,
    LoadingComponent
} from '@/utils/components';
import type { Action } from '@/interfaces/actions.interface';
import type { Pagination } from '@/interfaces/pagination/pagination.interface'
import InputComponent from '@/components/form/InputComponent.vue';
import FormGroupComponent from '@/components/form/FormGroupComponent.vue';
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';
import LabelComponent from '@/components/form/LabelComponent.vue';
import { RequestParams } from '@/interfaces/request-params.interface';
import SearchComponent from '@/components/SearchComponent.vue';
import PaginationComponent from '@/components/PaginationComponent.vue';
import { ref } from 'vue';

const isLoading = ref(false);
const showDeleteModal = ref(false);
const showModal = ref(false);
const currentItem = ref(null);

const status = ref<Pagination>({
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
    total: 5,
});

const formData = (data: any = {}) => {
    return {
        title: data.title || '',
        color: data.color || '#000000',
        color_text: data.color_text || '#ffffff',
        slug: data.slug || '',
    }
}

const form = ref(formData());

const params = ref<RequestParams>({
    without_pagination: 0,
    page: 1,
    search: '',
});

// Dados fictícios
const statusData = ref([
    {
        id: 1,
        slug: 'em-andamento',
        'Título': 'Em Andamento',
        'Cor': '#4CAF50',
        'Cor do texto': '#FFFFFF',
    },
    {
        id: 2,
        slug: 'concluido',
        'Título': 'Concluído',
        'Cor': '#2196F3',
        'Cor do texto': '#FFFFFF',
    },
    {
        id: 3,
        slug: 'pendente',
        'Título': 'Pendente',
        'Cor': '#F44336',
        'Cor do texto': '#FFFFFF',
    },
    {
        id: 4,
        slug: 'aguardando',
        'Título': 'Aguardando',
        'Cor': '#FF9800',
        'Cor do texto': '#FFFFFF',
    },
    {
        id: 5,
        slug: 'cancelado',
        'Título': 'Cancelado',
        'Cor': '#9E9E9E',
        'Cor do texto': '#FFFFFF',
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
    total: 5,
});

const getItemById = (id: any) => statusData.value.find((item) => item.id === id);

const handlePageChange = async (pageUrl: string) => {
    if (pageUrl) {
        const urlParams = new URL(pageUrl);
        const page = parseInt(urlParams.searchParams.get('page') || '1', 10);
        params.value.page = page;
    }
};

const handleSearch = (searchTerm: string) => {
    params.value.search = searchTerm;
};

const handlePerPageChange = (newPerPage: any) => {
    params.value.per_page = newPerPage;
    params.value.page = 1;
};

const actions: Action[] = [
    {
        name: 'edit',
        action: (item) => {
            currentItem.value = getItemById(item.id);
            if (currentItem.value) {
                form.value = formData({
                    title: currentItem.value['Título'],
                    color: currentItem.value['Cor'],
                    color_text: currentItem.value['Cor do texto'],
                    slug: currentItem.value.slug,
                });
            }
            showModal.value = true;
        },
        icon: 'edit',
        class: 'light blue',
    },
    {
        name: 'delete',
        action: (item) => {
            currentItem.value = getItemById(item.id);
            showDeleteModal.value = true;
        },
        icon: 'trash',
        class: 'light red',
    },
];

const handleModalTitle = () => {
    return form.value.title != '' ? 'Editar Status' : 'Adicionar Status';
};

const handleCloseModal = () => {
    showModal.value = false;
    clearForm();
};

const handleSubmit = () => {
    console.log('Salvar status', form.value);
    showModal.value = false;
    clearForm();
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    currentItem.value = null;
};

const confirmDelete = () => {
    if (currentItem.value) {
        statusData.value = statusData.value.filter(s => s.id !== currentItem.value.id);
    }
    closeDeleteModal();
};

const clearForm = () => form.value = formData();
</script>

<template>
    <LoadingComponent :show="isLoading" />

    <ModalComponent sizeClass="modal-status" :show="showModal" @closeModal="handleCloseModal" :titleHeader="handleModalTitle()" @submit="handleSubmit">
        <FormGroupComponent>
            <InputComponent v-model="form.title" placeholder="Título" type="text" />
        </FormGroupComponent>

        <FormGroupComponent>
            <LabelComponent text="Cor do status" />
            <input type="color" v-model="form.color" id="colorPicker" />
        </FormGroupComponent>

        <FormGroupComponent>
            <LabelComponent text="Cor do texto" />
            <input type="color" v-model="form.color_text" id="colorPicker" />
        </FormGroupComponent>
    </ModalComponent>

    <ConfirmDeleteModalComponent 
        :show="showDeleteModal" 
        @closeModal="closeDeleteModal" 
        @confirmDelete="confirmDelete"
    />

    <CardTitleComponent title="Status"/>

    <ContainerComponent>
        <CardComponent titleCard="Lista de Status">
            <template #search>
                <SearchComponent @update-search="handleSearch" :value="params.search" />
            </template>

            <template #button>
                <ButtonComponent buttonClass="btn-secondary" text="Adicionar" icon="plus" light @click="showModal = true" />
            </template> 
       
            <TableComponent 
                :items="statusData" 
                :actions="actions" 
            />

            <PaginationComponent
                :items="statusData" 
                :pagination="pagination"
                @page-change="handlePageChange" 
                @per-page-change="handlePerPageChange" 
            />

        </CardComponent>
    </ContainerComponent>
</template>
