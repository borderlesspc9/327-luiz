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
const showModal = ref(false);
const showDeleteModal = ref(false);
const currentItem = ref(null);

const formData = (data: any = {}) => {
    return {
        slug: data.slug || '',
        name: data.name || '',
        permissions: data.permissions || []
    }
}

const form = ref(formData());

const params = ref<RequestParams>({
    without_pagination: 0,
    search: '',
});

// Dados fictícios
const rolesData = ref([
    {
        id: 1,
        slug: 'administrador',
        Nome: 'Administrador',
    },
    {
        id: 2,
        slug: 'operador',
        Nome: 'Operador',
    },
    {
        id: 3,
        slug: 'vendedor',
        Nome: 'Vendedor',
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

const permissionsSelectData = ref([
    { value: 1, text: 'Read client' },
    { value: 2, text: 'Create client' },
    { value: 3, text: 'Update client' },
    { value: 4, text: 'Delete client' },
    { value: 5, text: 'Read process' },
    { value: 6, text: 'Create process' },
    { value: 7, text: 'Update process' },
    { value: 8, text: 'Delete process' },
    { value: 9, text: 'Read service' },
    { value: 10, text: 'Create service' },
    { value: 11, text: 'Update service' },
    { value: 12, text: 'Delete service' },
]);

const getItemById = (id: number) => rolesData.value.find((item) => item.id === id);

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
                form.value = formData({
                    name: currentItem.value.Nome,
                    slug: currentItem.value.slug,
                    permissions: [1, 2, 3, 4],
                });
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
    return form.value.slug ? 'Editar Cargo' : 'Adicionar Cargo';
};

const handleCloseModal = () => {
    showModal.value = false;
};

const handleSubmit = () => {
    console.log('Salvar cargo', form.value);
    clearForm();
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    currentItem.value = null;
};

const confirmDelete = () => {
    if (currentItem.value) {
        rolesData.value = rolesData.value.filter(r => r.id !== currentItem.value.id);
    }
    closeDeleteModal();
};

const clearForm = () => {
    form.value = formData();
};
</script>

<template>
    <LoadingComponent :show="isLoading" />

    <ConfirmDeleteModalComponent 
        :show="showDeleteModal" 
        @closeModal="closeDeleteModal" 
        @confirmDelete="confirmDelete"
    />

    <ModalComponent :show="showModal" :titleHeader="handleModalTitle()" @closeModal="handleCloseModal" @submit="handleSubmit()">
        <FormGroupComponent>
            <LabelComponent text="Nome" />
            <InputComponent v-model="form.name" placeholder="Nome" type="text" />
        </FormGroupComponent>
        <FormGroupComponent>
            <LabelComponent text="Permissões" />
            <MultipleSelectComponent v-model="form.permissions" :options="permissionsSelectData" placeholder="Pesquisar e adicionar permissões" />
        </FormGroupComponent>
    </ModalComponent>

    <ContainerComponent>
        <CardComponent titleCard="Cargos">
            <template #search>
                <SearchComponent @update-search="handleSearch" :value="params.search" />
            </template>
            <template #button>
                <ButtonComponent buttonClass="btn-secondary" text="Adicionar" icon="plus" light @click="showModal = true" />
            </template>        
            <TableComponent 
                :items="rolesData" 
                :actions="actions" 
            />

            <PaginationComponent
                :items="rolesData" 
                :pagination="pagination" 
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange"
            />
        </CardComponent>
    </ContainerComponent>
</template>
