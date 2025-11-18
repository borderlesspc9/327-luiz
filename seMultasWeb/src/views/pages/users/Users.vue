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
import type { RequestParams } from '@/interfaces/request-params.interface';
import type { Pagination } from '@/interfaces/pagination/pagination.interface'
import { onMounted, ref } from 'vue'
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';
import type { Action } from '@/interfaces/actions.interface';
import type { User } from '@/interfaces/user.interface';
import { useUserStore } from '@/stores/users.store';
import { useRoleStore } from '@/stores/roles.store';
import type { Role } from '@/interfaces/roles.interface';
import { useAcl } from '@/utils/acl';
import SearchComponent from '@/components/SearchComponent.vue';
import PaginationComponent from '@/components/PaginationComponent.vue';

const { hasPermissionTo } = useAcl();
const isLoading = ref(false);
const showModal = ref(false);
const showDeleteModal = ref(false);
const userStore = useUserStore();
const roleStore = useRoleStore();
const currentItem = ref(null);
const usersData = ref([]);

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

const roles = ref<Role[]>([]);

const loadUsers = async () => {
    await userStore.index(params.value);
    users.value = userStore.getUsers;
    prepareDataToTable();
}

const prepareDataToTable = () => {
    usersData.value = users.value.data.map((user) => {
        return {
            id: user.id,
            slug: user.slug,
            Nome: user.name,
            Email: user.email,
            Cargo: user.roles[0].name,
        }
    })
}

const getItemById = (id: number) => users.value.data.find((item) => item.id === id);

const handlePageChange = async (pageUrl: string) => {
    if (pageUrl) {
        isLoading.value = true;

        const urlParams = new URL(pageUrl);
        const page = parseInt(urlParams.searchParams.get('page') || '1', 10);

        params.value.page = page;

        await loadUsers();
        isLoading.value = false;

    }
};

const handlePerPageChange = (newPerPage) => {
    params.value.per_page = newPerPage;
    params.value.page = 1;
    loadUsers();
};

const rolesSelectData = ref([]);
const loadRoles = async () => {
    await roleStore.index({ without_pagination: 1 });
    roles.value = roleStore.getRoles;
    
    rolesSelectData.value = roles.value.map((role) => {
        return {
            value: role.id,
            text: role.name
        }
    });
}

const handleSearch = (searchTerm: string) => {
    params.value.search = searchTerm;
    loadUsers();
};

onMounted(async () => {
    isLoading.value = true;
    await loadUsers();
    await loadRoles();
    
    isLoading.value = false;
});

const actions: Action[] = [
    {
        name: 'edit',
        hasPermission: hasPermissionTo('Update user'),
        action: (item) => {
            currentItem.value = getItemById(item.id);

            form.value = formData(currentItem.value);
            form.value.roles = currentItem.value.roles.map((role) => role.id);
            showModal.value = true;
        },
        icon: 'edit',
        class: 'light blue',
    },
    {
        name: 'delete',
        hasPermission: hasPermissionTo('Delete user'),
        action: (item) => {
            currentItem.value = getItemById(item.id);
            showDeleteModal.value = true;
        },
        icon: 'trash',
        class: 'light red',
    },
]

const closeDeleteModal = () => {
    showDeleteModal.value = false;
};

const confirmDelete = () => {
    if (currentItem.value) {
        handleDelete();
    }
    closeDeleteModal();
};

const handleDelete = async () => {
    isLoading.value = true;
    await userStore.destroy(currentItem.value.id)
    await loadUsers();
    isLoading.value = false;
}

const handleModalTitle = () => {
    return form.value.id ? 'Editar Cargo' : 'Adicionar Cargo';
}

const handleCloseModal = () => {
  showModal.value = false;
  clearForm();
};

const handleSubmit = async() => {
    isLoading.value = true;
    if(form.value.id) {
        await userStore.update(form.value.id, form.value);
    }else{
        await userStore.store(form.value);
    }
    await loadUsers();
    isLoading.value = false;
    clearForm();
}

const clearForm = () => {
    form.value = formData();
}

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
                :pagination="users" 
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange"
            />
        </CardComponent>
    </ContainerComponent>

</template>