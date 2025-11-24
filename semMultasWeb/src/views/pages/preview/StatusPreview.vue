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
import { onMounted, ref } from 'vue'
import type { Action } from '@/interfaces/actions.interface';
import type { Status } from '@/interfaces/status.interface';
import type { Pagination } from '@/interfaces/pagination/pagination.interface'
import InputComponent from '@/components/form/InputComponent.vue';
import FormGroupComponent from '@/components/form/FormGroupComponent.vue';
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';
import LabelComponent from '@/components/form/LabelComponent.vue';
import { RequestParams } from '@/interfaces/request-params.interface';

import SearchComponent from '@/components/SearchComponent.vue';
import PaginationComponent from '@/components/PaginationComponent.vue';

// Mock data
const mockStatusData = ref([
    {
        id: 1,
        slug: 'em-andamento',
        title: 'Em Andamento',
        color: '#4CAF50',
        color_text: '#FFFFFF',
    },
    {
        id: 2,
        slug: 'concluido',
        title: 'Concluído',
        color: '#2196F3',
        color_text: '#FFFFFF',
    },
    {
        id: 3,
        slug: 'pendente',
        title: 'Pendente',
        color: '#F44336',
        color_text: '#FFFFFF',
    },
    {
        id: 4,
        slug: 'aguardando',
        title: 'Aguardando',
        color: '#FF9800',
        color_text: '#FFFFFF',
    },
    {
        id: 5,
        slug: 'cancelado',
        title: 'Cancelado',
        color: '#9E9E9E',
        color_text: '#FFFFFF',
    },
]);

// Simulate API delay
const simulateDelay = (ms: number = 500) => new Promise(resolve => setTimeout(resolve, ms));

const status = ref<Pagination>({
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

const isLoading = ref(false);
const showDeleteModal = ref(false);
const currentItem = ref<Status | null>(null);

const params = ref<RequestParams>({
    without_pagination: 0,
    page: 1,
    search: '',
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

onMounted(async () => {
    isLoading.value = true;
    await loadStatus();
    
    isLoading.value = false;
})

const loadStatus = async () => {
    await simulateDelay(300);
    
    // Apply search
    let filtered = [...mockStatusData.value];
    if (params.value.search) {
        const search = params.value.search.toLowerCase();
        filtered = filtered.filter(s => 
            s.title?.toLowerCase().includes(search) ||
            s.slug?.toLowerCase().includes(search)
        );
    }
    
    // Pagination
    const perPage = params.value.per_page || 10;
    const page = params.value.page || 1;
    const start = (page - 1) * perPage;
    const end = start + perPage;
    const paginated = filtered.slice(start, end);
    
    status.value = {
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

interface StatusData {
    id: any;
    slug: any;
    Título: any;
    Cor: any;
    'Cor do texto': any;
}

const statusData = ref<StatusData[]>([]);
const prepareDataToTable = () => {
    statusData.value = status.value.data.map((status): StatusData => {
        return {
            id: status.id,
            slug: status.slug,
            'Título': status.title,
            'Cor': status.color,
            'Cor do texto': status.color_text,
        }
    })
}

const getItemById = (id: any) => status.value.data.find((item) => item.id === id);

const handlePageChange = async (pageUrl: string) => {
    if (pageUrl) {
        isLoading.value = true;

        const urlParams = new URL(pageUrl);
        const page = parseInt(urlParams.searchParams.get('page') || '1', 10);

        params.value.page = page;

        await loadStatus();
        isLoading.value = false;

    }
};

const handleSearch = (searchTerm: string) => {
    params.value.search = searchTerm;
    loadStatus();
};


const handlePerPageChange = (newPerPage: any) => {
    params.value.per_page = newPerPage;
    params.value.page = 1;
    loadStatus();
};

const actions: Action[] = [
    {
        name: 'edit',
        action: (item) => {
            currentItem.value = getItemById(item.id);

            form.value = formData(currentItem.value);
            showModal.value = true;
        },
        icon: 'edit',
        class: 'light blue',
    },
    {
        name: 'delete',
        action: (item) => {
            currentItem.value = item;
            showDeleteModal.value = true;
        },
        icon: 'trash',
        class: 'light red',
    },
]

const showModal = ref(false);
const handleCloseModal = () => {
  showModal.value = false;
  clearForm();
};

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

const handleModalTitle = () => {
    return form.value.title != '' ? 'Editar Status' : 'Adicionar Status';
}

const handleSubmit = async() => {
    isLoading.value = true;
    await simulateDelay(500);
    
    if (form.value.slug) {
        // Update existing
        const index = mockStatusData.value.findIndex(s => s.slug === form.value.slug);
        if (index !== -1) {
            mockStatusData.value[index] = {
                ...mockStatusData.value[index],
                ...form.value,
            };
        }
    } else {
        // Create new
        const newStatus = {
            id: Math.max(...mockStatusData.value.map(s => s.id)) + 1,
            slug: form.value.title.toLowerCase().replace(/\s+/g, '-'),
            ...form.value,
        };
        mockStatusData.value.push(newStatus);
    }
    
    showModal.value = false;
    clearForm();
    await loadStatus();
    isLoading.value = false;
};

const handleDelete = async () => {
    isLoading.value = true;
    await simulateDelay(500);
    
    if (currentItem.value) {
        if (currentItem.value && currentItem.value.slug) {
            const index = mockStatusData.value.findIndex(s => s.slug === currentItem.value.slug);
            if (index !== -1) {
                mockStatusData.value.splice(index, 1);
            }
        }
    }
    await loadStatus();
    isLoading.value = false;
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
                :pagination="status"
                @page-change="handlePageChange" 
                @per-page-change="handlePerPageChange" 
            />

        </CardComponent>
    </ContainerComponent>

</template>
