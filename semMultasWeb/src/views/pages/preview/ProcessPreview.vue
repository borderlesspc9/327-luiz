<script setup lang="ts">
import { 
  ModalComponent, 
  ButtonComponent, 
  CardTitleComponent, 
  ContainerComponent, 
  CardComponent, 
  FormGroupComponent, 
  InputComponent, 
  SelectComponent, 
  LoadingComponent,
  LabelComponent,
  SearchComponent,
  PaginationComponent
} from '@/utils/components';
import { ref, computed } from 'vue';
import type { Pagination } from '@/interfaces/pagination/pagination.interface';
import type { Action } from '@/types/ActionType';
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';
import ConfirmRefreshModal from '@/components/ConfirmRefreshModal.vue';
import SwitchComponent from '@/components/form/SwitchComponent.vue';
import TableComponentProcess from '@/components/TableComponentProcess.vue';
import IconComponent from '@/components/IconComponent.vue';

const isLoading = ref(false);
const showModal = ref(false);
const showDeleteModal = ref(false);
const showRefreshModal = ref(false);
const showViewModal = ref(false);
const showUpdateStatusModal = ref(false);
const showModalFilter = ref(false);
const showModalImportFile = ref(false);
interface ProcessDataItem {
    id: number;
    slug: string;
    AIT: string;
    'Data limite': string;
    Serviço: string;
    Placa: string;
    Chassi: string;
    Renavam: string;
    'Nome do cliente': string;
    CPF: string;
    'Data de nascimento': string;
    status: Array<{
        id: number;
        title: string;
        color: string;
        color_text: string;
        pivot: { is_active: number };
        user: { name: string };
        created_at: string;
    }>;
}

const currentItem = ref<ProcessDataItem | null>(null);

// Dados fictícios de processos
const processesData = ref<ProcessDataItem[]>([
    {
        id: 1,
        slug: 'process-1',
        AIT: '123456789',
        'Data limite': '15/12/2024',
        Serviço: 'Defesa de Multa',
        Placa: 'ABC-1234',
        Chassi: '9BW12345678901234',
        Renavam: '12345678901',
        'Nome do cliente': 'João Silva',
        CPF: '123.456.789-00',
        'Data de nascimento': '15/05/1990',
        status: [
            { id: 1, title: 'Em Andamento', color: '#4CAF50', color_text: '#FFFFFF', pivot: { is_active: 1 }, user: { name: 'Maria Santos' }, created_at: '2024-01-15 10:30:00' },
            { id: 2, title: 'Aguardando', color: '#FF9800', color_text: '#FFFFFF', pivot: { is_active: 0 }, user: { name: 'Pedro Oliveira' }, created_at: '2024-01-10 14:20:00' }
        ]
    },
    {
        id: 2,
        slug: 'process-2',
        AIT: '987654321',
        'Data limite': '20/12/2024',
        Serviço: 'Recurso de Multa',
        Placa: 'XYZ-5678',
        Chassi: '9BW98765432109876',
        Renavam: '98765432109',
        'Nome do cliente': 'Maria Santos',
        CPF: '987.654.321-00',
        'Data de nascimento': '20/08/1985',
        status: [
            { id: 1, title: 'Concluído', color: '#2196F3', color_text: '#FFFFFF', pivot: { is_active: 1 }, user: { name: 'Ana Costa' }, created_at: '2024-01-20 09:15:00' }
        ]
    },
    {
        id: 3,
        slug: 'process-3',
        AIT: '456789123',
        'Data limite': '10/12/2024',
        Serviço: 'Defesa de Multa',
        Placa: 'DEF-9012',
        Chassi: '9BW45678912345678',
        Renavam: '45678912345',
        'Nome do cliente': 'Pedro Oliveira',
        CPF: '456.789.123-00',
        'Data de nascimento': '10/03/1992',
        status: [
            { id: 1, title: 'Pendente', color: '#F44336', color_text: '#FFFFFF', pivot: { is_active: 1 }, user: { name: 'Carlos Mendes' }, created_at: '2024-01-25 16:45:00' }
        ]
    }
]);

const pagination = ref<Pagination>({
    current_page: 1,
    data: [],
    first_page_url: '',
    from: 1,
    last_page: 3,
    last_page_url: '',
    links: [],
    next_page_url: '',
    path: '',
    per_page: 10,
    prev_page_url: '',
    to: 10,
    total: 30,
});

const form = ref({
    user_id: '',
    client_id: '1',
    status_id: '1',
    seller_id: '1',
    service_id: '1',
    plate: 'ABC-1234',
    chassis: '9BW12345678901234',
    renavam: '12345678901',
    state_plate: 'SP',
    infraction_code: '601',
    agency: 'DETRAN-SP',
    ait: '123456789',
    seller: 'Vendedor 1',
    process_value: '500.00',
    payment_method: 'Pix',
    observation: 'Processo em análise',
    process_number: '2024001234',
    deadline_date: '2024-12-15',
    updateDeadLine: false,
    slug: '',
});

interface ProcessField {
    label: string;
    name: string;
    type: string;
}

// Serviços completos com process_fields
const allServices = ref([
    {
        id: 1,
        name: 'Defesa de Multa',
        process_fields: [
            { label: 'Código da Infração', name: 'infraction_code', type: 'text' },
            { label: 'Órgão', name: 'agency', type: 'text' },
            { label: 'Placa', name: 'plate', type: 'text' },
            { label: 'Chassi', name: 'chassis', type: 'text' },
            { label: 'Renavam', name: 'renavam', type: 'text' },
            { label: 'Estado da placa', name: 'state_plate', type: 'text' },
        ] as ProcessField[]
    },
    {
        id: 2,
        name: 'Recurso de Multa',
        process_fields: [
            { label: 'Número do Processamento', name: 'process_number', type: 'text' },
            { label: 'Órgão', name: 'agency', type: 'text' },
            { label: 'Placa', name: 'plate', type: 'text' },
            { label: 'Observação', name: 'observation', type: 'textarea' },
        ] as ProcessField[]
    },
    {
        id: 3,
        name: 'Consulta de Veículo',
        process_fields: [
            { label: 'Placa', name: 'plate', type: 'text' },
            { label: 'Chassi', name: 'chassis', type: 'text' },
            { label: 'Renavam', name: 'renavam', type: 'text' },
        ] as ProcessField[]
    },
]);

const serviceSelectData = ref([
    { value: '1', text: 'Defesa de Multa' },
    { value: '2', text: 'Recurso de Multa' },
    { value: '3', text: 'Consulta de Veículo' },
]);

const clientSelectData = ref([
    { value: '1', text: 'João Silva' },
    { value: '2', text: 'Maria Santos' },
    { value: '3', text: 'Pedro Oliveira' },
]);

const statusSelectData = ref([
    { value: '1', text: 'Em Andamento' },
    { value: '2', text: 'Concluído' },
    { value: '3', text: 'Pendente' },
    { value: '4', text: 'Aguardando' },
]);

const sellerSelectData = ref([
    { value: '1', text: 'Vendedor 1' },
    { value: '2', text: 'Vendedor 2' },
    { value: '3', text: 'Vendedor 3' },
]);

const paymentMethod = [
    { value: 'Dinheiro', text: 'Dinheiro' },
    { value: 'Crédito à vista', text: 'Crédito à vista' },
    { value: 'Crédito parcelado', text: 'Crédito parcelado' },
    { value: 'Cartão de débito', text: 'Cartão de débito' },
    { value: 'Pix', text: 'Pix' },
    { value: 'Boleto', text: 'Boleto' },
    { value: 'Transferência', text: 'Transferência' },
];

const processFields = ref<ProcessField[]>([]);

const totalProcessToday = computed(() => processesData.value.length);

const actions: Action[] = [
    {
        name: 'edit',
        hasPermission: true,
        action: (item) => {
            const process = processesData.value.find(p => p.id === item.id);
            if (process) {
                form.value.plate = process.Placa;
                form.value.ait = process.AIT;
                form.value.slug = process.slug;
                form.value.service_id = '1'; // Mock - definir serviço antes de carregar campos
                handleProcessList(); // Carregar campos do serviço
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
        name: 'show',
        hasPermission: true,
        action: (item) => {
            const found = processesData.value.find(p => p.id === item.id);
            currentItem.value = found || null;
            showViewModal.value = true;
        },
        icon: 'eye',
        class: 'light btn-outline-primary',
    },
    {
        name: 'refresh',
        hasPermission: true,
        action: (item) => {
            currentItem.value = item;
            showRefreshModal.value = true;
        },
        icon: 'refresh',
        class: 'light dark-blue',
    },
    {
        name: 'pdf',
        hasPermission: true,
        action: (item) => {
            console.log('Gerar PDF', item);
        },
        icon: 'file-pdf',
        class: 'light blue',
    },
];

const handleCloseModal = () => {
    showModal.value = false;
    processFields.value = [];
    form.value = {
        user_id: '',
        client_id: '',
        status_id: '',
        seller_id: '',
        service_id: '',
        plate: '',
        chassis: '',
        renavam: '',
        state_plate: '',
        infraction_code: '',
        agency: '',
        ait: '',
        seller: '',
        process_value: '',
        payment_method: '',
        observation: '',
        process_number: '',
        deadline_date: '',
        updateDeadLine: false,
        slug: '',
    };
};

const handleModalTitle = () => {
    return form.value.plate != '' ? 'Editar Processo' : 'Adicionar Processo';
};

const handleSubmit = () => {
    console.log('Salvar processo', form.value);
    handleCloseModal();
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    currentItem.value = null;
};

const confirmDelete = () => {
    if (currentItem.value) {
        const itemId = currentItem.value.id;
        processesData.value = processesData.value.filter(p => p.id !== itemId);
    }
    closeDeleteModal();
};

const closeRefreshModal = () => {
    showRefreshModal.value = false;
    currentItem.value = null;
};

const confirmRefresh = () => {
    console.log('Atualizar data limite');
    closeRefreshModal();
};

const handleRowClick = (item: any) => {
    const process = processesData.value.find(p => p.id === item.id);
    if (process) {
        currentItem.value = process;
        form.value.deadline_date = process['Data limite'].split('/').reverse().join('-');
        const activeStatus = process.status?.find((s: any) => s.pivot?.is_active === 1);
        if (activeStatus) {
            form.value.status_id = activeStatus.id.toString();
        }
        showUpdateStatusModal.value = true;
    }
};

const handleCloseModaView = () => {
    showViewModal.value = false;
    currentItem.value = null;
};

const handleUpdateStatusModal = () => {
    showUpdateStatusModal.value = false;
    currentItem.value = null;
    form.value.status_id = '';
    form.value.updateDeadLine = false;
};

const handleSubmitStatus = () => {
    console.log('Atualizar status', form.value);
    handleUpdateStatusModal();
};

const closeModalFilter = () => {
    showModalFilter.value = false;
};

const handleFilter = () => {
    console.log('Aplicar filtros');
    closeModalFilter();
};

const resetFilters = () => {
    console.log('Limpar filtros');
    closeModalFilter();
};

const handleSearch = (searchTerm: string) => {
    console.log('Buscar:', searchTerm);
};

const handlePageChange = (pageUrl: string) => {
    console.log('Mudar página:', pageUrl);
};

const handlePerPageChange = (newPerPage: any) => {
    console.log('Mudar itens por página:', newPerPage);
};

const handleExport = () => {
    console.log('Exportar dados');
};

const getProcessFieldsByServiceId = (): ProcessField[] => {
    if (!form.value.service_id) {
        return [];
    }
    const service = allServices.value.find((s) => s.id == Number(form.value.service_id));
    return service ? service.process_fields : [];
};

const handleProcessList = () => {
    processFields.value = getProcessFieldsByServiceId();
};

const getFilledFields = (item: any) => {
    return [
        { label: 'service', value: item.Serviço },
        { label: 'ait', value: item.AIT },
        { label: 'plate', value: item.Placa },
        { label: 'chassis', value: item.Chassi },
        { label: 'renavam', value: item.Renavam },
        { label: 'deadline_date', value: item['Data limite'] },
    ];
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${day}-${month}-${year} ${hours}:${minutes}`;
};

const isObjectNotEmpty = (obj: any) => {
    return Object.values(obj).some(item => item !== null);
};

const moneyConfig = {
    masked: false,
    prefix: 'R$ ',
    suffix: '',
    thousands: '.',
    decimal: ',',
    precision: 2,
    disableNegative: false,
    disabled: false,
    min: null,
    max: null,
    allowBlank: false,
    minimumNumberOfCharacters: 0,
    shouldRound: true,
    focusOnRight: false,
};
</script>

<template>
    <LoadingComponent :show="isLoading" />

    <ConfirmDeleteModalComponent 
        :show="showDeleteModal" 
        @closeModal="closeDeleteModal" 
        @confirmDelete="confirmDelete"
    />

    <ConfirmRefreshModal
        :show="showRefreshModal" 
        @closeModal="closeRefreshModal" 
        @confirmRefresh="confirmRefresh"
    />

    <!-- modal de cadastro -->
    <ModalComponent :show="showModal" @closeModal="handleCloseModal" :titleHeader="handleModalTitle()" @submit="handleSubmit()">
        <div class="row">
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Cliente" />
                    <SelectComponent :modelValue="form.client_id" @update:modelValue="form.client_id = $event" :options="clientSelectData" />
                </FormGroupComponent>
            </div>
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Vendedor" />
                    <SelectComponent :modelValue="form.seller_id" @update:modelValue="form.seller_id = $event" :options="sellerSelectData" />
                </FormGroupComponent>
            </div>
        </div>

        <div class="row">
            <div class="col-2">
                <FormGroupComponent>
                    <LabelComponent text="Data limite" />
                    <InputComponent v-model="form.deadline_date" type="date" />
                </FormGroupComponent>
            </div>
            <div class="col-5">
                <FormGroupComponent>
                    <LabelComponent text="Status" />
                    <SelectComponent :modelValue="form.status_id" @update:modelValue="form.status_id = $event" :options="statusSelectData" />
                </FormGroupComponent>
            </div>
            <div class="col-5">
                <FormGroupComponent>
                    <LabelComponent text="AIT" />
                    <InputComponent v-model="form.ait" type="text" />
                </FormGroupComponent>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Valor do processo" />
                    <InputComponent v-model="form.process_value" placeholder="R$ 0,00" type="text" />
                </FormGroupComponent>
            </div>
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Forma de pagamento" />
                    <SelectComponent :modelValue="form.payment_method" @update:modelValue="form.payment_method = $event" :options="paymentMethod" />
                </FormGroupComponent>
            </div>
        </div>        
    
        <FormGroupComponent>
            <LabelComponent text="Serviços" />
            <SelectComponent :modelValue="form.service_id" @update:modelValue="form.service_id = $event; handleProcessList()" :options="serviceSelectData" />
        </FormGroupComponent>

        <template v-if="processFields.length > 0">
            <FormGroupComponent v-for="(field, index) in processFields" :key="index">
                <LabelComponent :text="field.label" />
                <InputComponent :modelValue="(form as any)[field.name]" @update:modelValue="(form as any)[field.name] = $event" :placeholder="field.label" :type="field.type" />
            </FormGroupComponent>
        </template>

        <FormGroupComponent v-else>
            <LabelComponent text="Nenhum campo disponível para este serviço" />
        </FormGroupComponent>
    </ModalComponent>

    <!-- modal para importar processos -->
    <ModalComponent :show="showModalImportFile" @closeModal="showModalImportFile = false" titleHeader="Importar Processos" @submit="() => {}">
        <FormGroupComponent>
            <InputComponent placeholder="Selecione o arquivo" type="file" accept=".xls,.xlsx" />
        </FormGroupComponent>
    </ModalComponent>

    <!-- modal para ver processos -->
    <ModalComponent 
        :show="showViewModal"
        cancelText="Fechar"
        @closeModal="handleCloseModaView"
        :showSubmitBtn="false"
        titleHeader="Visualizar Processo"
    >
        <div class="card" v-if="currentItem">
            <div class="card-body flex-body">
                <div class="column">
                    <div class="card-text card-flex" v-for="(field, index) in getFilledFields(currentItem).slice(0, Math.ceil(getFilledFields(currentItem).length / 2))" :key="index">
                        <span>{{ field.label }}:</span> <span>{{ field.value }}</span>
                    </div>
                </div>
                <div class="column">
                    <div class="card-text card-flex" v-for="(field, index) in getFilledFields(currentItem).slice(Math.ceil(getFilledFields(currentItem).length / 2))" :key="index">
                        <span>{{ field.label }}:</span> <span>{{ field.value }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" v-if="currentItem">
            <div class="card-header card-w-700">
                Cliente
            </div>
            <div class="card-body flex-body">
                <div class="column">
                    <div class="card-text card-flex">
                        <span>Nome:</span>
                        <span>{{ currentItem['Nome do cliente'] }}</span>
                    </div>
                    <div class="card-text card-flex">
                        <span>CPF:</span>
                        <span>{{ currentItem.CPF }}</span>
                    </div>
                </div>
                <div class="column">
                    <div class="card-text card-flex">
                        <span>Data de nascimento:</span>
                        <span>{{ currentItem['Data de nascimento'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" v-if="currentItem && currentItem.status">
            <div class="card-header card-w-700">
                Histórico de status
            </div>
            <template v-for="status in currentItem.status" :key="status.id">
                <div class="card-body flex-body">
                    <div class="column">
                        <div class="card-text card-flex">
                            <span>Título</span><span>{{ status.title }}</span>
                        </div>
                    </div>
                    <div class="column">
                        <div class="card-text card-flex">
                            <span>Usuário</span><span>{{ status.user.name }}</span>
                        </div>
                    </div>
                    <div class="column">
                        <div class="card-text card-flex">
                            <span>Ativo:</span><span>{{ status.pivot.is_active ? 'Sim' : 'Não' }}</span>
                        </div>
                    </div>
                    <div class="column">
                        <div class="card-text card-flex">
                            <span>Data:</span><span>{{ formatDate(status.created_at) }}</span>
                        </div>
                    </div>
                    <div class="column">
                        <div :style="{ backgroundColor: status.color, width: '20px', height: '20px', borderRadius: '5px', boxShadow: '0px 0px 0px 1px #ccc' }"></div>
                    </div>
                </div>
            </template>
        </div>
    </ModalComponent>
            
    <CardTitleComponent :title="`Processos (${totalProcessToday})`" />

    <ContainerComponent>
        <CardComponent titleCard="Lista de processos">
            
            <template #search>
                <SearchComponent @update-search="handleSearch" />
                
                <div class="title-search">
                    <div class="input-group">
                        <button class="btn btn-outline-secondary" type="button" id="button-addon1" @click="showModalFilter = true">
                            <IconComponent name="filter" />
                        </button>
                    </div>
                </div>
            </template>

            <template #button>
                <div class="button-group">
                    <ButtonComponent buttonClass="btn-secondary btn-import" text="Importar" icon="file-upload" light @click="showModalImportFile = true" />
                    <ButtonComponent buttonClass="btn-secondary btn-export" text="Exportar" icon="file-download" light @click="handleExport()" />
                    <ButtonComponent buttonClass="btn-secondary" text="Adicionar" icon="plus" light @click="showModal = true" />
                </div>
            </template>
            
            <TableComponentProcess 
                :items="processesData" 
                :actions="actions" 
                @row-click="handleRowClick"
            />

            <PaginationComponent
                :items="processesData" 
                @page-change="handlePageChange"
                :pagination="pagination"
                @per-page-change="handlePerPageChange" 
            />

        </CardComponent>
    </ContainerComponent>

    <!-- modal status do processo -->
    <ModalComponent 
        sizeClass="modal-small"
        :show="showUpdateStatusModal"
        cancelText="Fechar"
        @closeModal="handleUpdateStatusModal"
        @submit="handleSubmitStatus()"
        titleHeader="Atualizar Status"
    >
        <div>
            <FormGroupComponent>
                <LabelComponent text="Status" />
                <SelectComponent :modelValue="form.status_id" @update:modelValue="form.status_id = $event" :options="statusSelectData" />
            </FormGroupComponent>
            <FormGroupComponent>
                <div class="form-check form-switch">
                    <SwitchComponent :modelValue="form.updateDeadLine" @update:modelValue="form.updateDeadLine = $event"/>
                    <LabelComponent class="form-check-label" text="Alterar data limite"/>
                </div>
            </FormGroupComponent>
            <FormGroupComponent v-if="form.updateDeadLine">
                <LabelComponent text="Data limite" />
                <InputComponent v-model="form.deadline_date" type="date" />
            </FormGroupComponent>
        </div>
    </ModalComponent>

    <!-- Modal de filtro -->
    <ModalComponent
        submitText="Filtrar"
        sizeClass="modal-small"
        :show="showModalFilter"
        cancelText="Fechar"
        @closeModal="closeModalFilter()"
        @submit="handleFilter()"
        titleHeader="Filtros"
    >
        <FormGroupComponent>
            <LabelComponent text="Status" />
            <SelectComponent :modelValue="''" @update:modelValue="() => {}" :options="statusSelectData" />
        </FormGroupComponent>

        <FormGroupComponent>
            <LabelComponent text="Data limite" />
            <InputComponent type="date" />
        </FormGroupComponent>

        <FormGroupComponent>
            <ButtonComponent buttonClass="btn-light light" type="button" text="Limpar" light @click="resetFilters()" />
        </FormGroupComponent>
    </ModalComponent>
</template>

