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
import { onMounted, ref } from 'vue'
import type { Action } from '@/interfaces/actions.interface';
import type { Service } from '@/interfaces/service.interface';
import type { Pagination } from '@/interfaces/pagination/pagination.interface'
import type { RequestParams } from '@/interfaces/request-params.interface';
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';
import { useAcl } from '@/utils/acl';
import SearchComponent from '@/components/SearchComponent.vue';
import PaginationComponent from '@/components/PaginationComponent.vue';

// Mock data
const mockServicesData = ref([
    {
        id: 1,
        slug: 'defesa-multa',
        name: 'Defesa de Multa',
        process_fields: [
            { id: 1, name: 'Código da Infração' },
            { id: 2, name: 'Órgão' },
            { id: 3, name: 'Placa' },
            { id: 4, name: 'Chassi' },
            { id: 5, name: 'Renavam' },
            { id: 6, name: 'Estado da placa' },
        ]
    },
    {
        id: 2,
        slug: 'recurso-multa',
        name: 'Recurso de Multa',
        process_fields: [
            { id: 7, name: 'Número do Processamento' },
            { id: 2, name: 'Órgão' },
            { id: 3, name: 'Placa' },
            { id: 8, name: 'Observação' },
        ]
    },
    {
        id: 3,
        slug: 'consulta-veiculo',
        name: 'Consulta de Veículo',
        process_fields: [
            { id: 3, name: 'Placa' },
            { id: 4, name: 'Chassi' },
            { id: 5, name: 'Renavam' },
        ]
    }
]);

const mockProcessFields = ref([
    { id: 1, name: 'Código da Infração' },
    { id: 2, name: 'Órgão' },
    { id: 3, name: 'Placa' },
    { id: 4, name: 'Chassi' },
    { id: 5, name: 'Renavam' },
    { id: 6, name: 'Estado da placa' },
    { id: 7, name: 'Número do Processamento' },
    { id: 8, name: 'Observação' },
]);

// Simulate API delay
const simulateDelay = (ms: number = 500) => new Promise(resolve => setTimeout(resolve, ms));

const form = ref({
    slug: '',
    name: '',
    process_fields: [],
});

const params = ref<RequestParams>({
    without_pagination: 0,
    page: 1
});

const services = ref<Pagination>({
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

const { hasPermissionTo } = useAcl();
const isLoading = ref(false);
const showDeleteModal = ref(false);
const servicesData = ref<Service[]>([]);
const currentItem = ref(null);

const processSelectData = ref({});

onMounted(async () => {
    isLoading.value = true;
    await loadServices();
    isLoading.value = false;
    await loadProcessFields();
})

const loadServices = async () => {
    await simulateDelay(300);
    
    // Apply search
    let filtered = [...mockServicesData.value];
    if (params.value.search) {
        const search = params.value.search.toLowerCase();
        filtered = filtered.filter(s => 
            s.name?.toLowerCase().includes(search) ||
            s.slug?.toLowerCase().includes(search)
        );
    }
    
    // Pagination
    const perPage = params.value.per_page || 10;
    const page = params.value.page || 1;
    const start = (page - 1) * perPage;
    const end = start + perPage;
    const paginated = filtered.slice(start, end);
    
    services.value = {
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
    servicesData.value = services.value.data.map((sercice) => {
        return {
            id: sercice.id,
            slug: sercice.slug,
            Nome: sercice.name,
        }
    })
}

const getItemById = (id: number) => services.value.data.find((item) => item.id === id);

const loadProcessFields = async () => {
    await simulateDelay(200);
    processSelectData.value = mockProcessFields.value.map((item) => {
        return {
            text: item.name,
            value: item.id
        }
    });
}

const handlePageChange = async (pageUrl: string) => {
    if (pageUrl) {
        isLoading.value = true;

        const urlParams = new URL(pageUrl);
        const page = parseInt(urlParams.searchParams.get('page') || '1', 10);

        params.value.page = page;

        await loadServices();
        isLoading.value = false;

    }
};

const handleSearch = (searchTerm: string) => {
    params.value.search = searchTerm;
    loadServices();
};

const handlePerPageChange = (newPerPage) => {
    params.value.per_page = newPerPage;
    params.value.page = 1;
    loadServices();
};

const actions: Action[] = [
    {
        name: 'edit',
        hasPermission: hasPermissionTo('Update service'),
        action: (item) => {
            currentItem.value = getItemById(item.id);

            form.value.name = currentItem.value.name;
            form.value.process_fields = currentItem.value.process_fields.map(field => field.id);
            form.value.slug = currentItem.value.slug;            
            showModal.value = true;
        },
        icon: 'edit',
        class: 'light blue',
    },
    {
        name: 'delete',
        hasPermission: hasPermissionTo('Delete service'),
        action: (item) => {
            currentItem.value = getItemById(item.id);
            showDeleteModal.value = true;
        },
        icon: 'trash',
        class: 'light red',
    },
]

const showModal = ref(false);
const handleCloseModal = () => {
  showModal.value = false;
};

const handleModalTitle = () => {
    return form.value.name != '' ? 'Editar Serviço' : 'Adicionar Serviço';
}

const clearForm = () => {
    form.value.name = '';
    form.value.process_fields = [];
    form.value.slug = '';
}

const handleSubmit = async() => {
    isLoading.value = true;
    await simulateDelay(500);
    
    if(form.value.slug) {
        // Update existing
        const index = mockServicesData.value.findIndex(s => s.slug === form.value.slug);
        if (index !== -1) {
            mockServicesData.value[index] = {
                ...mockServicesData.value[index],
                name: form.value.name,
                process_fields: form.value.process_fields.map((id: number) => 
                    mockProcessFields.value.find(pf => pf.id === id)
                ).filter(Boolean),
            };
        }
    } else {
        // Create new
        const newService = {
            id: Math.max(...mockServicesData.value.map(s => s.id)) + 1,
            slug: form.value.name.toLowerCase().replace(/\s+/g, '-'),
            name: form.value.name,
            process_fields: form.value.process_fields.map((id: number) => 
                mockProcessFields.value.find(pf => pf.id === id)
            ).filter(Boolean),
        };
        mockServicesData.value.push(newService);
    }
    
    await loadServices();
    
    isLoading.value = false;
    clearForm();
    showModal.value = false;
}

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    currentItem.value = null;
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
    
    const index = mockServicesData.value.findIndex(s => s.slug === currentItem.value.slug);
    if (index !== -1) {
        mockServicesData.value.splice(index, 1);
    }
    
    await loadServices();
    
    isLoading.value = false;
}
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
                :pagination="services" 
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange"
            />
        </CardComponent>
    </ContainerComponent>

</template>
