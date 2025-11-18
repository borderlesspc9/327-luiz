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
import { useServiceStore } from '@/stores/service.store';
import type { RequestParams } from '@/interfaces/request-params.interface';
import { useProcessFieldsStore } from '@/stores/processFields.store';
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';
import { useAcl } from '@/utils/acl';
import SearchComponent from '@/components/SearchComponent.vue';
import PaginationComponent from '@/components/PaginationComponent.vue';

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

const serviceStore = useServiceStore();
const processFieldsStore = useProcessFieldsStore();

onMounted(async () => {
    isLoading.value = true;
    await loadServices();
    isLoading.value = false;
    await loadProcessFields();
})

const loadServices = async () => {
    await serviceStore.index(params.value);
    services.value = serviceStore.getServices;
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

const processSelectData = ref({});

const loadProcessFields = async () => {
    await processFieldsStore.index();
    processSelectData.value = processFieldsStore.getProcessFields.map((item) => {
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
    if(form.value.slug) {
        await serviceStore.update(form.value.slug, form.value);
    }else{
        await serviceStore.store(form.value);
    }
    await loadServices();
    
    isLoading.value = false;
    clearForm();
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
    await serviceStore.destroy(currentItem.value.slug);
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