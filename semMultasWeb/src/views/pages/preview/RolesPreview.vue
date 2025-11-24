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
import type { Permission } from '@/interfaces/permissions.interface';
import { useAcl } from '@/utils/acl';
import SearchComponent from '@/components/SearchComponent.vue';
import PaginationComponent from '@/components/PaginationComponent.vue';

// Mock data
const mockRolesData = ref([
    {
        id: 1,
        slug: 'administrador',
        name: 'Administrador',
        permissions: [
            { id: 1, name: 'Read client' },
            { id: 2, name: 'Create client' },
            { id: 3, name: 'Update client' },
            { id: 4, name: 'Delete client' },
            { id: 5, name: 'Read process' },
            { id: 6, name: 'Create process' },
            { id: 7, name: 'Update process' },
            { id: 8, name: 'Delete process' },
        ]
    },
    {
        id: 2,
        slug: 'operador',
        name: 'Operador',
        permissions: [
            { id: 1, name: 'Read client' },
            { id: 5, name: 'Read process' },
            { id: 6, name: 'Create process' },
        ]
    },
    {
        id: 3,
        slug: 'vendedor',
        name: 'Vendedor',
        permissions: [
            { id: 1, name: 'Read client' },
            { id: 2, name: 'Create client' },
            { id: 5, name: 'Read process' },
            { id: 6, name: 'Create process' },
        ]
    }
]);

const mockPermissions = ref([
    { id: 1, name: 'Read client' },
    { id: 2, name: 'Create client' },
    { id: 3, name: 'Update client' },
    { id: 4, name: 'Delete client' },
    { id: 5, name: 'Read process' },
    { id: 6, name: 'Create process' },
    { id: 7, name: 'Update process' },
    { id: 8, name: 'Delete process' },
    { id: 9, name: 'Read service' },
    { id: 10, name: 'Create service' },
    { id: 11, name: 'Update service' },
    { id: 12, name: 'Delete service' },
    { id: 13, name: 'Read roles' },
    { id: 14, name: 'Create roles' },
    { id: 15, name: 'Update roles' },
    { id: 16, name: 'Delete roles' },
    { id: 17, name: 'Read user' },
    { id: 18, name: 'Create user' },
    { id: 19, name: 'Update user' },
    { id: 20, name: 'Delete user' },
]);

// Simulate API delay
const simulateDelay = (ms: number = 500) => new Promise(resolve => setTimeout(resolve, ms));

const { hasPermissionTo } = useAcl();
const isLoading = ref(false);
const showModal = ref(false);
const showDeleteModal = ref(false);
const currentItem = ref(null);
const rolesData = ref([]);

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

const roles = ref<Pagination>({
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
const permissions = ref<Permission[]>([]);

onMounted(async () => {
    isLoading.value = true;
    await loadRoles();
    await loadPermissions();

    isLoading.value = false;
});

const loadRoles = async () => {
    await simulateDelay(300);
    
    // Apply search
    let filtered = [...mockRolesData.value];
    if (params.value.search) {
        const search = params.value.search.toLowerCase();
        filtered = filtered.filter(r => 
            r.name?.toLowerCase().includes(search) ||
            r.slug?.toLowerCase().includes(search)
        );
    }
    
    // Pagination
    const perPage = params.value.per_page || 10;
    const page = params.value.page || 1;
    const start = (page - 1) * perPage;
    const end = start + perPage;
    const paginated = filtered.slice(start, end);
    
    roles.value = {
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
};

const prepareDataToTable = () => {
    rolesData.value = roles.value.data.map((roles) => {
        return {
            id: roles.id,
            slug: roles.slug,
            Nome: roles.name,
        }
    })
}

const getItemById = (id: number) => roles.value.data.find((item) => item.id === id);

const handlePageChange = async (pageUrl: string) => {
    if (pageUrl) {
        isLoading.value = true;

        const urlParams = new URL(pageUrl);
        const page = parseInt(urlParams.searchParams.get('page') || '1', 10);

        params.value.page = page;

        await loadRoles();
        isLoading.value = false;

    }
};

const handleSearch = (searchTerm: string) => {
    params.value.search = searchTerm;
    loadRoles();
};

const handlePerPageChange = (newPerPage) => {
    params.value.per_page = newPerPage;
    params.value.page = 1;
    loadRoles();
};

const permissionsSelectData = ref({});

const loadPermissions = async () => {
    await simulateDelay(200);
    permissions.value = mockPermissions.value;
    permissionsSelectData.value = permissions.value.map((permission) => {
        return {
            value: permission.id,
            text: permission.name
        }
    });
}

const actions: Action[] = [
    {
        name: 'edit',
        hasPermission: hasPermissionTo('Update roles'),
        action: (item) => {
            currentItem.value = getItemById(item.id);

            form.value = formData(currentItem.value);
            form.value.permissions = currentItem.value.permissions.map((permission) => permission.id);
            showModal.value = true;
        },
        icon: 'edit',
        class: 'light blue',
    },
    {
        name: 'delete',
        hasPermission: hasPermissionTo('Delete roles'),
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
    
    const index = mockRolesData.value.findIndex(r => r.slug === currentItem.value.slug);
    if (index !== -1) {
        mockRolesData.value.splice(index, 1);
    }
    
    await loadRoles();
    isLoading.value = false;
}

const handleModalTitle = () => {
    return form.value.slug ? 'Editar Cargo' : 'Adicionar Cargo';
}

const handleCloseModal = () => {
  showModal.value = false;
};

const handleSubmit = async() => {
    isLoading.value = true;
    await simulateDelay(500);
    
    if(form.value.slug) {
        // Update existing
        const index = mockRolesData.value.findIndex(r => r.slug === form.value.slug);
        if (index !== -1) {
            mockRolesData.value[index] = {
                ...mockRolesData.value[index],
                name: form.value.name,
                permissions: form.value.permissions.map((id: number) => 
                    mockPermissions.value.find(p => p.id === id)
                ).filter(Boolean),
            };
        }
    } else {
        // Create new
        const newRole = {
            id: Math.max(...mockRolesData.value.map(r => r.id)) + 1,
            slug: form.value.name.toLowerCase().replace(/\s+/g, '-'),
            name: form.value.name,
            permissions: form.value.permissions.map((id: number) => 
                mockPermissions.value.find(p => p.id === id)
            ).filter(Boolean),
        };
        mockRolesData.value.push(newRole);
    }
    
    await loadRoles();
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
                :pagination="roles" 
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange" 
            />
        </CardComponent>
    </ContainerComponent>

</template>
