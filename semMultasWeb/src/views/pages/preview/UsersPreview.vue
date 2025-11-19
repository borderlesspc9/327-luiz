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
        name: data.name || '',
        email: data.email || '',
        password: data.password || '',
        id: data.id || '',
        roles: data.roles || []
    }
}

const form = ref(formData());

const params = ref<RequestParams>({
    without_pagination: 0,
    search: ''
});

// Dados fictícios
const usersData = ref([
    {
        id: 1,
        slug: 'joao-silva',
        Nome: 'João Silva',
        Email: 'joao.silva@email.com',
        Cargo: 'Administrador',
    },
    {
        id: 2,
        slug: 'maria-santos',
        Nome: 'Maria Santos',
        Email: 'maria.santos@email.com',
        Cargo: 'Operador',
    },
    {
        id: 3,
        slug: 'pedro-oliveira',
        Nome: 'Pedro Oliveira',
        Email: 'pedro.oliveira@email.com',
        Cargo: 'Vendedor',
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

const rolesSelectData = ref([
    { value: 1, text: 'Administrador' },
    { value: 2, text: 'Operador' },
    { value: 3, text: 'Vendedor' },
]);

const getItemById = (id: number) => usersData.value.find((item) => item.id === id);

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
                    email: currentItem.value.Email,
                    id: currentItem.value.id,
                    roles: [1],
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
    return form.value.id ? 'Editar Usuário' : 'Adicionar Usuário';
};

const handleCloseModal = () => {
    showModal.value = false;
    clearForm();
};

const handleSubmit = () => {
    console.log('Salvar usuário', form.value);
    clearForm();
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    currentItem.value = null;
};

const confirmDelete = () => {
    if (currentItem.value) {
        usersData.value = usersData.value.filter(u => u.id !== currentItem.value.id);
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
            <LabelComponent text="E-mail" />
            <InputComponent v-model="form.email" placeholder="E-mail" type="text" />
        </FormGroupComponent>
        <FormGroupComponent>
            <LabelComponent text="Senha" />
            <InputComponent v-model="form.password" placeholder="Password" type="password" />
        </FormGroupComponent>
        <FormGroupComponent>
            <LabelComponent text="Cargo" />
            <MultipleSelectComponent v-model="form.roles" :options="rolesSelectData" placeholder="Pesquisar e adicionar cargo" />
        </FormGroupComponent>
    </ModalComponent>

    <ContainerComponent>
        <CardComponent titleCard="Usuários">
            <template #search>
                <SearchComponent @update-search="handleSearch" :value="params.search" />
            </template>
            <template #button>
                <ButtonComponent buttonClass="btn-secondary" text="Adicionar" icon="plus" light @click="showModal = true" />
            </template>        
            <TableComponent 
                :items="usersData" 
                :actions="actions" 
            />

            <PaginationComponent
                :items="usersData" 
                :pagination="pagination" 
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange"
            />
        </CardComponent>
    </ContainerComponent>
</template>
