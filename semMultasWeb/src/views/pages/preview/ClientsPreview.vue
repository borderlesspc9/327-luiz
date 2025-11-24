<script setup lang="ts">
import { onMounted, ref } from 'vue'
import {
    TableComponent,
    ModalComponent,
    ButtonComponent,
    CardTitleComponent,
    ContainerComponent,
    CardComponent,
    LoadingComponent,
    PaginationComponent,
    LabelComponent
} from '@/utils/components';
import type { Action } from '@/interfaces/actions.interface';
import type { Client } from '@/interfaces/client.interface';
import type { Pagination } from '@/interfaces/pagination/pagination.interface'
import InputComponent from '@/components/form/InputComponent.vue';
import FormGroupComponent from '@/components/form/FormGroupComponent.vue';
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';
import type { RequestParams } from '@/interfaces/request-params.interface';
import BoxFileComponent from '@/components/BoxFileComponent.vue';
import { fetchAddressByCep } from '@/utils/addressByCep'
import { useAcl } from '@/utils/acl';

import SearchComponent from '@/components/SearchComponent.vue';
import IconComponent from '@/components/IconComponent.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

// Mock data
const mockClientsData = ref([
    {
        id: 1,
        slug: 'joao-silva',
        name: 'João Silva',
        phone: '(11) 98765-4321',
        license_date: '15/03/2010',
        cpf: '123.456.789-00',
        cep: '01310-100',
        state: 'SP',
        city: 'São Paulo',
        neighborhood: 'Bela Vista',
        address: 'Av. Paulista, 1000',
        number: '1000',
        complement: '',
        rg: '12345678',
        rg_letter: 'A',
        rg_issuer: 'SSP-SP',
        birth_date: '1990-05-15',
        files: [
            { id: 1, name: 'Documento.pdf', type: 'pdf', url: '/storage/test.pdf' },
            { id: 2, name: 'Foto.jpg', type: 'image', url: '/storage/test.jpg' },
        ]
    },
    {
        id: 2,
        slug: 'maria-santos',
        name: 'Maria Santos',
        phone: '(21) 99876-5432',
        license_date: '20/05/2015',
        cpf: '987.654.321-00',
        cep: '20040-020',
        state: 'RJ',
        city: 'Rio de Janeiro',
        neighborhood: 'Centro',
        address: 'Rua do Ouvidor, 50',
        number: '50',
        complement: '',
        rg: '87654321',
        rg_letter: 'B',
        rg_issuer: 'SSP-RJ',
        birth_date: '1985-08-20',
        files: []
    },
    {
        id: 3,
        slug: 'pedro-oliveira',
        name: 'Pedro Oliveira',
        phone: '(31) 91234-5678',
        license_date: '10/08/2018',
        cpf: '456.789.123-00',
        cep: '30130-010',
        state: 'MG',
        city: 'Belo Horizonte',
        neighborhood: 'Centro',
        address: 'Av. Afonso Pena, 1000',
        number: '1000',
        complement: '',
        rg: '45678912',
        rg_letter: 'C',
        rg_issuer: 'SSP-MG',
        birth_date: '1992-03-10',
        files: []
    }
]);

// Simulate API delay
const simulateDelay = (ms: number = 500) => new Promise(resolve => setTimeout(resolve, ms));

const clients = ref<Pagination>({
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

const params = ref<RequestParams>({
    without_pagination: 0,
    page: 1,
});

const { hasPermissionTo } = useAcl();
const isLoading = ref(false);
const showDeleteModal = ref(false);
const showDeleteModalFile = ref(false);
const fileToDelete = ref(null);
const currentItem = ref(null);
const clientsFiles = ref([]);

const formData = (data: any = {}) => {
    
    return {
        name: data.name || '',
        phone: data.phone || '',
        cpf: data.cpf || '',
        cep: data.cep || '',
        rg: data.rg || '',
        rg_letter: data.rg_letter || '',
        rg_issuer: data.rg_issuer || '',
        birth_date: data.birth_date || '',
        license_date: data.license_date || '',
        state: data.state || '',
        city: data.city || '',
        address: data.address || '',
        neighborhood: data.neighborhood || '',
        number: data.number || '',
        complement: data.complement || '',
        slug: data.slug || '',
        fileName: '',
        file: null,
    }
}

const clientsData = ref<Client[]>([]);
const form = ref(formData());

onMounted(async () => {
    isLoading.value = true;
    await loadClients();
    isLoading.value = false;
})

const loadClients = async () => {
    await simulateDelay(300);
    
    // Apply search
    let filtered = [...mockClientsData.value];
    if (params.value.search) {
        const search = params.value.search.toLowerCase();
        filtered = filtered.filter(c => 
            c.name?.toLowerCase().includes(search) ||
            c.cpf?.toLowerCase().includes(search) ||
            c.phone?.toLowerCase().includes(search)
        );
    }
    
    // Pagination
    const perPage = params.value.per_page || 10;
    const page = params.value.page || 1;
    const start = (page - 1) * perPage;
    const end = start + perPage;
    const paginated = filtered.slice(start, end);
    
    clients.value = {
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
    clientsData.value = clients.value.data.map((client) => {
        return {
            id: client.id,
            slug: client.slug,
            Nome: client.name,
            Telefone: client.phone,
            'Data 1º habilitação': client.license_date,
            CPF: client.cpf,
            CEP: client.cep,
            Estado: client.state,
            Cidade: client.city,
            Bairro: client.neighborhood,
            Endereço: client.address,
        }
    });
};

const getItemById = (id: number) => clients.value.data.find((item) => item.id === id);

const handlePageChange = async (pageUrl: string) => {
    if (pageUrl) {
        isLoading.value = true;

        const urlParams = new URL(pageUrl);
        const page = parseInt(urlParams.searchParams.get('page') || '1', 10);

        params.value.page = page;

        await loadClients();
        isLoading.value = false;

    }
};

const handleSearch = (searchTerm: string) => {
    params.value.search = searchTerm;
    loadClients();
};


const handlePerPageChange = (newPerPage) => {
    params.value.per_page = newPerPage;
    params.value.page = 1;
    loadClients();
};

const loadClientAndReturnFiles = async (slug: string) => {
    await simulateDelay(200);
    const client = mockClientsData.value.find(c => c.slug === slug);
    return client?.files || [];
};

const setFilesOnClient = async () => {
    const filesUpdated = await loadClientAndReturnFiles(currentItem.value.slug);
    clientsFiles.value = filesUpdated;
};

const actions: Action[] = [
    {
        name: 'edit',
        hasPermission: hasPermissionTo('Update client'),
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
        hasPermission: hasPermissionTo('Delete client'),
        action: (item) => {
            currentItem.value = item;
            showDeleteModal.value = true;
        },
        icon: 'trash',
        class: 'light red',
    },
    {
        name: 'folder',
        hasPermission: hasPermissionTo('Read client file'),
        action: async (item) => {
            currentItem.value = getItemById(item.id);
            
            await setFilesOnClient();
            showModalFiles.value = true;
        },
        icon: 'folder',
        class: 'light blue',
    },
    {
        name: 'pdf',
        hasPermission: hasPermissionTo('Create PDF Procuration'),
        action: async (item) => {
            currentItem.value = item;
            isLoading.value = true;
            await simulateDelay(1000);
            // Simulate PDF download
            const link = document.createElement('a');
            link.href = '#';
            link.download = `procuracao-${item.slug}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            isLoading.value = false;
        },
        icon: 'file-pdf',
        class: 'light blue',
    }
]

const fileActions: Action[] = [
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
const showModalFiles = ref(false);
const modalUploadFile = ref(false);

const handleCloseModal = () => {
  showModal.value = false;
};

const closeUploadModal = () => {
    modalUploadFile.value = false;
    clearFileInput()
};
const handleCloseModalFiles = () => {
    showModalFiles.value = false;
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

const confirmUpload = () => {
    if (currentItem.value) {
        uploadClientFile();
    }

    clearFileInput()
};

const handleModalTitle = () => {
    return form.value.slug != '' ? 'Editar Cliente' : 'Adicionar Cliente';
}

const handleSubmit = async() => {
    isLoading.value = true;
    await simulateDelay(500);
    
    if (form.value.slug) {
        // Update existing
        const index = mockClientsData.value.findIndex(c => c.slug === form.value.slug);
        if (index !== -1) {
            mockClientsData.value[index] = {
                ...mockClientsData.value[index],
                ...form.value,
            };
        }
    } else {
        // Create new
        const newClient = {
            id: Math.max(...mockClientsData.value.map(c => c.id)) + 1,
            slug: form.value.name.toLowerCase().replace(/\s+/g, '-'),
            ...form.value,
            files: []
        };
        mockClientsData.value.push(newClient);
    }
    
    showModal.value = false;
    clearForm();
    await loadClients();
    isLoading.value = false;
};

const uploadClientFile = async() => {
    isLoading.value = true;
    await simulateDelay(500);
    
    if (currentItem.value && form.value.file) {
        const client = mockClientsData.value.find(c => c.slug === currentItem.value.slug);
        if (client) {
            const newFile = {
                id: Math.max(...(client.files || []).map((f: any) => f.id || 0)) + 1,
                name: form.value.fileName || form.value.file.name,
                type: getFileType(form.value.file),
                url: `/storage/${form.value.file.name}`
            };
            if (!client.files) client.files = [];
            client.files.push(newFile);
            await setFilesOnClient();
        }
    }
    
    clearForm();
    isLoading.value = false;
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    form.value.fileName = file.name;
    form.value.file = file;
};

const clearFileInput = () => {
  form.value.fileName = '';
  form.value.file = null;

  const input = document.getElementById('fileInput') as HTMLInputElement;
  if (input) {
    input.value = '';
  }
};

const isDragOver = ref<boolean>(false);

const handleDragOver = () => {
    isDragOver.value = true;
};

const handleDragLeave = () => {
    isDragOver.value = false;
};

const handleDrop = (event: DragEvent) => {
  event.preventDefault();
  isDragOver.value = false;
  const file = event.dataTransfer?.files?.[0];
  if (file) {
    if(form.value.fileName == ''){
        form.value.fileName = file.name;
    }
    form.value.file = file;

    const input = document.getElementById('fileInput') as HTMLInputElement;
    if (input) {
      input.files = event.dataTransfer.files;
    }
  }
}

const handleDelete = async () => {
    isLoading.value = true;
    await simulateDelay(500);
    
    const index = mockClientsData.value.findIndex(c => c.slug === currentItem.value.slug);
    if (index !== -1) {
        mockClientsData.value.splice(index, 1);
    }
    
    await loadClients();
    isLoading.value = false;
};

const clearForm = () => form.value = formData();

const handleFileClick = (file) => {
  const baseUrl = import.meta.env.VITE_API_URL_FILES || '';
  const fileUrl = `${baseUrl}${file.url}`;

  if (file.type === 'image' || file.type === 'pdf') {
    // Abrir imagem ou PDF em uma nova aba
    window.open(fileUrl, '_blank');
  } else if (file.type === 'excel' || file.type === 'word') {
    // Fazer download do arquivo Excel
    const link = document.createElement('a');
    link.href = fileUrl;
    link.download = file.name;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
};

const openDeleteModalFile = (file) => {
    fileToDelete.value = file.id
    showDeleteModalFile.value = true;
};

const handleDeleteFile = async(fileToDelete) => {
    isLoading.value = true;
    await simulateDelay(300);
    
    if (currentItem.value) {
        const client = mockClientsData.value.find(c => c.slug === currentItem.value.slug);
        if (client && client.files) {
            client.files = client.files.filter((f: any) => f.id !== fileToDelete);
            await setFilesOnClient();
        }
    }
    
    showDeleteModalFile.value = false;
    isLoading.value = false;
};

const handleCepChange = async (cep: string) => {
    const formattedCep = cep.replace(/\D/g, '');

    if (formattedCep.length === 8) {
        try {
            const addressData = await fetchAddressByCep(form.value.cep);

            if (addressData) {
                form.value.neighborhood = addressData.bairro || '';
                form.value.address = addressData.logradouro || '';
                form.value.city = addressData.localidade || '';
                form.value.state = addressData.uf || '';
                form.value.complement = addressData.complemento || '';
            }
        } catch (error) {
            return null;
        }
    } else {
        form.value.address = '';
        form.value.neighborhood = '';
        form.value.city = '';
        form.value.state = '';
        form.value.complement = '';
    }
};


const imagePreview = (file) => {
    if (file) {
        return URL.createObjectURL(file);
    }
    return '';
};

const getFileType = (file: File | null) => {
    if (!file) return '';
    const extension = file.name.split('.').pop()?.toLowerCase();
    switch (extension) {
        case 'pdf':
            return 'pdf';
        case 'doc':
        case 'docx':
            return 'word';
        case 'xls':
        case 'xlsx':
            return 'excel';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'webp':
            return 'image';
        default:
            return 'file';
    }
};

const formatDateToBrazilian = (dateString: string): string => {
    if (!dateString) return '';
    // Converte de YYYY-MM-DD para DD/MM/YYYY
    const [year, month, day] = dateString.split('-');
    return `${day}/${month}/${year}`;
};

const copyDateToClipboard = (dateString: string, fieldName: string) => {
    if (!dateString) {
        toast.warning(`Não há data de ${fieldName} para copiar`);
        return;
    }
    
    const formattedDate = formatDateToBrazilian(dateString);
    
    const el = document.createElement('textarea');
    el.value = formattedDate;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    
    toast.success(`Data de ${fieldName} copiada: ${formattedDate}`);
};

</script>

<template>

    <LoadingComponent :show="isLoading" />

    <ModalComponent :show="showModal" @closeModal="handleCloseModal" :titleHeader="handleModalTitle()" @submit="handleSubmit">
        <div class="row">
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Nome" for="name" />
                    <InputComponent v-model="form.name" placeholder="Nome" type="text" />
                </FormGroupComponent>
            </div>
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Telefone" for="phone" />
                    <InputComponent v-model="form.phone" v-maska="'(##) 9 ####-####'" placeholder="Telefone" type="text" />
                </FormGroupComponent>
            </div>
        </div>

        <div class="row">
            <div class="col-4">
                <FormGroupComponent>
                    <LabelComponent text="CPF" for="cpf" />
                    <InputComponent v-model="form.cpf" v-maska="'###.###.###-##'" placeholder="CPF" type="text" />
                </FormGroupComponent>
            </div>
            <div class="col-3">
                <FormGroupComponent>
                    <LabelComponent text="RG" for="rg" />
                    <InputComponent v-model="form.rg" v-maska="'##########'" placeholder="RG" type="text" />
                </FormGroupComponent>
            </div>
            <div class="col-2">
                <FormGroupComponent>
                    <LabelComponent text="Letra" for="rg_letter" />
                    <InputComponent v-model="form.rg_letter" placeholder="Letra" type="text" maxlength="1" />
                </FormGroupComponent>
            </div>
            <div class="col-3">
                <FormGroupComponent>
                    <LabelComponent text="Órgão Expedidor" for="rg_issuer" />
                    <InputComponent v-model="form.rg_issuer" placeholder="Órgão Expedidor" type="text" />
                </FormGroupComponent>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <FormGroupComponent>
                    <LabelComponent text="Data de nascimento" for="birth_date" />
                    <div class="input-with-copy">
                        <InputComponent v-model="form.birth_date" placeholder="Data de nascimento" type="date" />
                        <button 
                            type="button" 
                            class="btn-copy-date" 
                            @click="copyDateToClipboard(form.birth_date, 'nascimento')"
                            title="Copiar data"
                        >
                            <IconComponent class="svg" name="copy" />
                        </button>
                    </div>
                </FormGroupComponent>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <FormGroupComponent>
                    <LabelComponent text="Data da primeira habilitação" for="license_date" />
                    <div class="input-with-copy">
                        <InputComponent v-model="form.license_date" placeholder="Data da primeira habilitação" type="date" />
                        <button 
                            type="button" 
                            class="btn-copy-date" 
                            @click="copyDateToClipboard(form.license_date, 'primeira habilitação')"
                            title="Copiar data"
                        >
                            <IconComponent class="svg" name="copy" />
                        </button>
                    </div>
                </FormGroupComponent>
            </div>
        </div>

        <div class="row">
            <div class="col-4">
                <FormGroupComponent>
                    <LabelComponent text="CEP" for="cep" />
                    <InputComponent v-model="form.cep" v-maska="'#####-###'" placeholder="CEP" type="text" @input="handleCepChange(form.cep)"/>
                </FormGroupComponent>
            </div>
            <div class="col-4">
                <FormGroupComponent>
                    <LabelComponent text="Estado" for="state" />
                    <InputComponent v-model="form.state" placeholder="Estado" type="text" />
                </FormGroupComponent>
            </div>
            <div class="col-4">
                <FormGroupComponent>
                    <LabelComponent text="Cidade" for="city" />
                    <InputComponent v-model="form.city" placeholder="Cidade" type="text" />
                </FormGroupComponent>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Bairro" for="neighborhood" />
                    <InputComponent v-model="form.neighborhood" placeholder="Bairro" type="text" />
                </FormGroupComponent>
            </div>
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Endereço" for="address" />
                    <InputComponent v-model="form.address" placeholder="Endereço" type="text" />
                </FormGroupComponent>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Número" for="number" />
                    <InputComponent v-model="form.number" v-maska="'####'" placeholder="Número" type="text" />
                </FormGroupComponent>
            </div>
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Complemento" for="complement" />
                    <InputComponent v-model="form.complement" placeholder="Complemento" type="text" />
                </FormGroupComponent>
            </div>
        </div>  
    </ModalComponent>

    <ModalComponent
        :show="showModalFiles" 
        cancelText="Fechar"
        actionText="Novo arquivo"
        :title-header="'Arquivos de '+currentItem?.Nome" 
        :showSubmitBtn="false"
        @close-modal="handleCloseModalFiles"
        @customAction="modalUploadFile = true"
        :showCustomAction="hasPermissionTo('Upload File')"
    >
    <BoxFileComponent 
        :files="clientsFiles || []" 
        @viewFile="handleFileClick"
        @deleteFile="openDeleteModalFile"
        :showBtnDeleteFile="hasPermissionTo('Delete File')"
    />

    </ModalComponent>

    <ConfirmDeleteModalComponent 
        :show="showDeleteModalFile" 
        @closeModal="showDeleteModalFile = false"
        @confirmDelete="handleDeleteFile(fileToDelete)"
    />

    <ModalComponent
        sizeClass="modal-m"
        :show="modalUploadFile" 
        @close-modal="closeUploadModal"
        @submit="confirmUpload"
    >   
        <div class="file-upload" v-if="!form.file || form.file.length < 1">
            <input type="file" id="fileInput" @change="handleFileUpload" />
            <label 
                for="fileInput" 
                id="fileLabel"
                @dragover.prevent="handleDragOver" 
                @dragleave="handleDragLeave" 
                @drop.prevent="handleDrop"
                :class="{ dragover: isDragOver }"
            >
                <div class="svg-label">
                    <div class="svg-container">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                            <path d="M144 480C64.5 480 0 415.5 0 336c0-62.8 40.2-116.2 96.2-135.9c-.1-2.7-.2-5.4-.2-8.1c0-88.4 71.6-160 160-160c59.3 0 111 32.2 138.7 80.2C409.9 102 428.3 96 448 96c53 0 96 43 96 96c0 12.2-2.3 23.8-6.4 34.6C596 238.4 640 290.1 640 352c0 70.7-57.3 128-128 128l-368 0zm79-217c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l39-39L296 392c0 13.3 10.7 24 24 24s24-10.7 24-24l0-134.1 39 39c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-80-80c-9.4-9.4-24.6-9.4-33.9 0l-80 80z"/>
                        </svg>
                    </div>
                    <p>Solte ou escolha o arquivo</p>
                </div>
                <InputComponent v-model="form.fileName" placeholder="Nome do arquivo (opcional)" type="text" />
            </label>
        </div>
        <div class="container-centered" v-else>
            <div class="image-preview" v-if="getFileType(form.file) === 'image'">
                <ButtonComponent buttonClass="btn-danger" icon="trash" @click="clearFileInput" />
                <img :src="imagePreview(form.file)" alt="Imagem de pré-visualização" />
            </div>
            <div class="file-preview" v-else>
                <ButtonComponent buttonClass="btn-danger" icon="trash" @click="clearFileInput" />
                <div class="file-icon">
                    <IconComponent class="icon" name="pdf" v-if="getFileType(form.file) === 'pdf'"/>
                    
                    <IconComponent class="icon" name="word" v-if="getFileType(form.file) === 'word'"/>
                    
                    <IconComponent class="icon" name="excel" v-if="getFileType(form.file) === 'excel'"/>
                    
                    <IconComponent class="icon" name="word" v-if="getFileType(form.file) === 'file'"/>
                    
                </div>
                <InputComponent v-model="form.fileName" placeholder="Nome do arquivo (opcional)" type="text" />
            </div>
        </div>
    </ModalComponent>

    <ConfirmDeleteModalComponent
        :show="showDeleteModal" 
        @closeModal="closeDeleteModal" 
        @confirmDelete="confirmDelete"
    />

    <CardTitleComponent title="Clientes" />

    <ContainerComponent>
        <CardComponent titleCard="Lista de clientes">
            <template #search>
                <SearchComponent @update-search="handleSearch" :value="params.search" />
            </template>

            <template #button>
                <ButtonComponent buttonClass="btn-secondary" text="Adicionar" icon="plus" light @click="showModal = true" />
            </template>        
            <TableComponent 
                :items="clientsData" 
                :actions="actions" 
            />

            <PaginationComponent
                :items="clientsData" 
                :pagination="clients" 
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange"
            />
        </CardComponent>
    </ContainerComponent>

</template>

<style scoped>
.input-with-copy {
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
}

.input-with-copy :deep(.form-control) {
    flex: 1;
}

.btn-copy-date {
    background-color: transparent;
    border: none;
    padding: 4px 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s ease;
    opacity: 0.6;
    color: #6c757d;
}

.btn-copy-date:hover {
    opacity: 1;
    background-color: #f8f9fa;
    color: #495057;
}

.btn-copy-date:active {
    transform: scale(0.95);
}

.btn-copy-date .svg {
    width: 16px;
    height: 16px;
}
</style>
