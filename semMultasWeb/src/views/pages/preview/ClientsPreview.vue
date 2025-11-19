<script setup lang="ts">
import { ref } from 'vue';
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
import type { Pagination } from '@/interfaces/pagination/pagination.interface';
import InputComponent from '@/components/form/InputComponent.vue';
import FormGroupComponent from '@/components/form/FormGroupComponent.vue';
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';
import BoxFileComponent from '@/components/BoxFileComponent.vue';
import SearchComponent from '@/components/SearchComponent.vue';
import IconComponent from '@/components/IconComponent.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const isLoading = ref(false);
const showDeleteModal = ref(false);
const showDeleteModalFile = ref(false);
const fileToDelete = ref(null);
const currentItem = ref(null);
const showModal = ref(false);
const showModalFiles = ref(false);
const modalUploadFile = ref(false);

// Dados fictícios
const clientsData = ref([
    {
        id: 1,
        slug: 'joao-silva',
        Nome: 'João Silva',
        Telefone: '(11) 98765-4321',
        'Data 1º habilitação': '15/03/2010',
        CPF: '123.456.789-00',
        CEP: '01310-100',
        Estado: 'SP',
        Cidade: 'São Paulo',
        Bairro: 'Bela Vista',
        Endereço: 'Av. Paulista, 1000'
    },
    {
        id: 2,
        slug: 'maria-santos',
        Nome: 'Maria Santos',
        Telefone: '(21) 99876-5432',
        'Data 1º habilitação': '20/05/2015',
        CPF: '987.654.321-00',
        CEP: '20040-020',
        Estado: 'RJ',
        Cidade: 'Rio de Janeiro',
        Bairro: 'Centro',
        Endereço: 'Rua do Ouvidor, 50'
    },
    {
        id: 3,
        slug: 'pedro-oliveira',
        Nome: 'Pedro Oliveira',
        Telefone: '(31) 91234-5678',
        'Data 1º habilitação': '10/08/2018',
        CPF: '456.789.123-00',
        CEP: '30130-010',
        Estado: 'MG',
        Cidade: 'Belo Horizonte',
        Bairro: 'Centro',
        Endereço: 'Av. Afonso Pena, 1000'
    }
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

const clientsFiles = ref([
    {
        id: 1,
        name: 'Documento.pdf',
        type: 'pdf',
        url: '/storage/test.pdf'
    },
    {
        id: 2,
        name: 'Foto.jpg',
        type: 'image',
        url: '/storage/test.jpg'
    },
]);

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

const form = ref(formData());

const getItemById = (id: number) => clientsData.value.find((item) => item.id === id);

const actions: Action[] = [
    {
        name: 'edit',
        hasPermission: true,
        action: (item) => {
            currentItem.value = getItemById(item.id);
            if (currentItem.value) {
                form.value = formData({
                    name: currentItem.value.Nome,
                    phone: currentItem.value.Telefone,
                    cpf: currentItem.value.CPF,
                    cep: currentItem.value.CEP,
                    state: currentItem.value.Estado,
                    city: currentItem.value.Cidade,
                    address: currentItem.value.Endereço,
                    neighborhood: currentItem.value.Bairro,
                    slug: currentItem.value.slug,
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
            currentItem.value = item;
            showDeleteModal.value = true;
        },
        icon: 'trash',
        class: 'light red',
    },
    {
        name: 'folder',
        hasPermission: true,
        action: (item) => {
            currentItem.value = getItemById(item.id);
            showModalFiles.value = true;
        },
        icon: 'folder',
        class: 'light blue',
    },
    {
        name: 'pdf',
        hasPermission: true,
        action: (item) => {
            console.log('Gerar PDF', item);
        },
        icon: 'file-pdf',
        class: 'light blue',
    }
];

const handlePageChange = async (pageUrl: string) => {
    console.log('Mudar página:', pageUrl);
};

const handleSearch = (searchTerm: string) => {
    console.log('Buscar:', searchTerm);
};

const handlePerPageChange = (newPerPage: any) => {
    console.log('Mudar itens por página:', newPerPage);
};

const handleCloseModal = () => {
    showModal.value = false;
    clearForm();
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
        clientsData.value = clientsData.value.filter(c => c.id !== currentItem.value.id);
    }
    closeDeleteModal();
};

const handleModalTitle = () => {
    return form.value.slug != '' ? 'Editar Cliente' : 'Adicionar Cliente';
};

const handleSubmit = () => {
    console.log('Salvar cliente', form.value);
    showModal.value = false;
    clearForm();
};

const handleFileClick = (file: any) => {
    const baseUrl = import.meta.env.VITE_API_URL_FILES || '';
    const fileUrl = `${baseUrl}${file.url}`;

    if (file.type === 'image' || file.type === 'pdf') {
        window.open(fileUrl, '_blank');
    } else if (file.type === 'excel' || file.type === 'word') {
        const link = document.createElement('a');
        link.href = fileUrl;
        link.download = file.name;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};

const openDeleteModalFile = (file: any) => {
    fileToDelete.value = file.id;
    showDeleteModalFile.value = true;
};

const handleDeleteFile = (fileId: number) => {
    clientsFiles.value = clientsFiles.value.filter(file => file.id !== fileId);
    showDeleteModalFile.value = false;
};

const closeUploadModal = () => {
    modalUploadFile.value = false;
    clearFileInput();
};

const confirmUpload = () => {
    if (currentItem.value) {
        console.log('Upload arquivo', form.value);
    }
    clearFileInput();
};

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.value.fileName = file.name;
        form.value.file = file;
    }
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
};

const handleCepChange = async (cep: string) => {
    const formattedCep = cep.replace(/\D/g, '');

    if (formattedCep.length === 8) {
        // Simulação de busca de CEP
        form.value.neighborhood = 'Bela Vista';
        form.value.address = 'Av. Paulista';
        form.value.city = 'São Paulo';
        form.value.state = 'SP';
        form.value.complement = '';
    } else {
        form.value.address = '';
        form.value.neighborhood = '';
        form.value.city = '';
        form.value.state = '';
        form.value.complement = '';
    }
};

const imagePreview = (file: File | null) => {
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

const clearForm = () => form.value = formData();
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
        :showCustomAction="true"
    >
        <BoxFileComponent 
            :files="clientsFiles || []" 
            @viewFile="handleFileClick"
            @deleteFile="openDeleteModalFile"
            :showBtnDeleteFile="true"
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
                <SearchComponent @update-search="handleSearch" :value="''" />
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
                :pagination="pagination" 
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
