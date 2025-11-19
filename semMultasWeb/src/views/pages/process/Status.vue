<script setup lang="ts">
import { useStatusStore } from '@/stores/status.store';
import { onMounted, ref } from 'vue'
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
import type { Status } from '@/interfaces/status.interface';
import type { Pagination } from '@/interfaces/pagination/pagination.interface'
import InputComponent from '@/components/form/InputComponent.vue';
import FormGroupComponent from '@/components/form/FormGroupComponent.vue';
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';
import LabelComponent from '@/components/form/LabelComponent.vue';
import { RequestParams } from '@/interfaces/request-params.interface';

import SearchComponent from '@/components/SearchComponent.vue';
import PaginationComponent from '@/components/PaginationComponent.vue';

const statusStore = useStatusStore();

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
    await statusStore.index(params.value);
    status.value = statusStore.getstatus;
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
    
    if (form.value.slug) {
        await statusStore.update(form.value.slug, form.value);
    } else {
        await statusStore.store(form.value);
    }
    showModal.value = false;
    clearForm();
    await loadStatus();
    isLoading.value = false;
};

const handleDelete = async () => {
    isLoading.value = true;
    if (currentItem.value) {
        if (currentItem.value && currentItem.value.slug) {
            await statusStore.destroy(currentItem.value.slug);
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