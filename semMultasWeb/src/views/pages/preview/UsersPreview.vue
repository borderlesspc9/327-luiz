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
import type { Role } from '@/interfaces/roles.interface';
import { useAcl } from '@/utils/acl';
import SearchComponent from '@/components/SearchComponent.vue';
import PaginationComponent from '@/components/PaginationComponent.vue';

// Mock data
const mockUsersData = ref([
    {
        id: 1,
        slug: 'joao-silva',
        name: 'João Silva',
        email: 'joao.silva@email.com',
        roles: [{ id: 1, name: 'Administrador' }]
    },
    {
        id: 2,
        slug: 'maria-santos',
        name: 'Maria Santos',
        email: 'maria.santos@email.com',
        roles: [{ id: 2, name: 'Operador' }]
    },
    {
        id: 3,
        slug: 'pedro-oliveira',
        name: 'Pedro Oliveira',
        email: 'pedro.oliveira@email.com',
        roles: [{ id: 3, name: 'Vendedor' }]
    }
]);

const mockRoles = ref([
    { id: 1, name: 'Administrador', slug: 'administrador' },
    { id: 2, name: 'Operador', slug: 'operador' },
    { id: 3, name: 'Vendedor', slug: 'vendedor' }
]);

// Simulate API delay
const simulateDelay = (ms: number = 500) => new Promise(resolve => setTimeout(resolve, ms));

const { hasPermissionTo } = useAcl();
const isLoading = ref(false);
const showModal = ref(false);
const showDeleteModal = ref(false);
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
    await simulateDelay(300);
    
    // Apply search
    let filtered = [...mockUsersData.value];
    if (params.value.search) {
        const search = params.value.search.toLowerCase();
        filtered = filtered.filter(u => 
            u.name?.toLowerCase().includes(search) ||
            u.email?.toLowerCase().includes(search)
        );
    }
    
    // Pagination
    const perPage = params.value.per_page || 10;
    const page = params.value.page || 1;
    const start = (page - 1) * perPage;
    const end = start + perPage;
    const paginated = filtered.slice(start, end);
    
    users.value = {
        current_page: page,
        data: paginated,
        first_page_url: '',
        from: start + 1,
        last_page: Math.ceil(filtered.length / perPage),
        last_page_url: '',
        links: [],
        next_page_url: page < Math.ceil(filtered.length / perPage) ? `?page=${page + 1}` : '',
        path: '',
        per_page: perPage,
        prev_page_url: page > 1 ? `?page=${page - 1}` : '',
        to: Math.min(end, filtered.length),
        total: filtered.length,
    };
    
    prepareDataToTable();
}

const prepareDataToTable = () => {
    usersData.value = users.value.data.map((user) => {
        return {
            id: user.id,
            slug: user.slug,
            Nome: user.name,
            Email: user.email,
            Cargo: user.roles[0]?.name || 'Sem cargo',
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
    await simulateDelay(200);
    roles.value = mockRoles.value;
    
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
    await simulateDelay(500);
    
    const index = mockUsersData.value.findIndex(u => u.id === currentItem.value.id);
    if (index !== -1) {
        mockUsersData.value.splice(index, 1);
    }
    
    await loadUsers();
    isLoading.value = false;
}

const handleModalTitle = () => {
    return form.value.id ? 'Editar Usuário' : 'Adicionar Usuário';
}

const handleCloseModal = () => {
  showModal.value = false;
  clearForm();
};

const handleSubmit = async() => {
    isLoading.value = true;
    await simulateDelay(500);
    
    if(form.value.id) {
        // Update existing
        const index = mockUsersData.value.findIndex(u => u.id === form.value.id);
        if (index !== -1) {
            mockUsersData.value[index] = {
                ...mockUsersData.value[index],
                name: form.value.name,
                email: form.value.email,
                roles: form.value.roles.map((id: number) => 
                    mockRoles.value.find(r => r.id === id)
                ).filter(Boolean),
            };
        }
    } else {
        // Create new
        const newUser = {
            id: Math.max(...mockUsersData.value.map(u => u.id)) + 1,
            slug: form.value.name.toLowerCase().replace(/\s+/g, '-'),
            name: form.value.name,
            email: form.value.email,
            roles: form.value.roles.map((id: number) => 
                mockRoles.value.find(r => r.id === id)
            ).filter(Boolean),
        };
        mockUsersData.value.push(newUser);
    }
    
    await loadUsers();
    isLoading.value = false;
    clearForm();
    showModal.value = false;
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
