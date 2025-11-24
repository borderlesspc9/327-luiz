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
import AutocompleteComponent from '@/components/form/AutocompleteComponent.vue';
import { onMounted, ref, computed } from 'vue';
import type { Pagination } from '@/interfaces/pagination/pagination.interface';
import type { Action } from '@/types/ActionType';
import type { RequestParams } from '@/interfaces/request-params.interface';
import type { Service } from '@/interfaces/service.interface';
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';
import ConfirmRefreshModal from '@/components/ConfirmRefreshModal.vue';
import { useAcl } from '@/utils/acl';
import SwitchComponent from '@/components/form/SwitchComponent.vue';
import TableComponentProcess from '@/components/TableComponentProcess.vue';
import IconComponent from '@/components/IconComponent.vue';
import { Process } from '@/interfaces/process.interface';

// Mock data stores
const mockProcesses = ref<any[]>([
    {
        id: 1,
        slug: 'process-1',
        ait: '123456789',
        deadline_date: '15/12/2024',
        plate: 'ABC-1234',
        chassis: '9BW12345678901234',
        renavam: '12345678901',
        process_number: '2024001234',
        process_value: '500.00',
        payment_method: 'Pix',
        infraction_code: '601',
        agency: 'DETRAN-SP',
        observation: 'Processo em análise',
        state_plate: 'SP',
        service: { id: 1, name: 'Defesa de Multa' },
        client: { id: 1, name: 'João Silva', cpf: '123.456.789-00', phone: '(11) 98765-4321', birth_date: '15/05/1990', city: 'São Paulo', state: 'SP', address: 'Av. Paulista, 1000', number: '1000' },
        seller: { id: 1, name: 'Vendedor 1', email: 'vendedor1@email.com' },
        user: { id: 1, name: 'Admin', email: 'admin@email.com' },
        status: [
            { id: 1, title: 'Em Andamento', color: '#4CAF50', color_text: '#FFFFFF', pivot: { is_active: 1 }, user: { name: 'Maria Santos' }, created_at: '2024-01-15 10:30:00' },
            { id: 2, title: 'Aguardando', color: '#FF9800', color_text: '#FFFFFF', pivot: { is_active: 0 }, user: { name: 'Pedro Oliveira' }, created_at: '2024-01-10 14:20:00' }
        ]
    },
    {
        id: 2,
        slug: 'process-2',
        ait: '987654321',
        deadline_date: '20/12/2024',
        plate: 'XYZ-5678',
        chassis: '9BW98765432109876',
        renavam: '98765432109',
        process_number: '2024005678',
        process_value: '750.00',
        payment_method: 'Boleto',
        infraction_code: '602',
        agency: 'DETRAN-RJ',
        observation: 'Processo concluído',
        state_plate: 'RJ',
        service: { id: 2, name: 'Recurso de Multa' },
        client: { id: 2, name: 'Maria Santos', cpf: '987.654.321-00', phone: '(21) 99876-5432', birth_date: '20/08/1985', city: 'Rio de Janeiro', state: 'RJ', address: 'Rua do Ouvidor, 50', number: '50' },
        seller: { id: 2, name: 'Vendedor 2', email: 'vendedor2@email.com' },
        user: { id: 2, name: 'Operador', email: 'operador@email.com' },
        status: [
            { id: 1, title: 'Concluído', color: '#2196F3', color_text: '#FFFFFF', pivot: { is_active: 1 }, user: { name: 'Ana Costa' }, created_at: '2024-01-20 09:15:00' }
        ]
    },
    {
        id: 3,
        slug: 'process-3',
        ait: '456789123',
        deadline_date: '10/12/2024',
        plate: 'DEF-9012',
        chassis: '9BW45678912345678',
        renavam: '45678912345',
        process_number: '2024009012',
        process_value: '300.00',
        payment_method: 'Cartão de débito',
        infraction_code: '603',
        agency: 'DETRAN-MG',
        observation: 'Aguardando documentação',
        state_plate: 'MG',
        service: { id: 1, name: 'Defesa de Multa' },
        client: { id: 3, name: 'Pedro Oliveira', cpf: '456.789.123-00', phone: '(31) 91234-5678', birth_date: '10/03/1992', city: 'Belo Horizonte', state: 'MG', address: 'Av. Afonso Pena, 1000', number: '1000' },
        seller: { id: 1, name: 'Vendedor 1', email: 'vendedor1@email.com' },
        user: { id: 1, name: 'Admin', email: 'admin@email.com' },
        status: [
            { id: 1, title: 'Pendente', color: '#F44336', color_text: '#FFFFFF', pivot: { is_active: 1 }, user: { name: 'Carlos Mendes' }, created_at: '2024-01-25 16:45:00' }
        ]
    }
]);

const mockServices = ref([
    {
        id: 1,
        name: 'Defesa de Multa',
        slug: 'defesa-multa',
        process_fields: [
            { id: 1, label: 'Código da Infração', name: 'infraction_code', type: 'text' },
            { id: 2, label: 'Órgão', name: 'agency', type: 'text' },
            { id: 3, label: 'Placa', name: 'plate', type: 'text' },
            { id: 4, label: 'Chassi', name: 'chassis', type: 'text' },
            { id: 5, label: 'Renavam', name: 'renavam', type: 'text' },
            { id: 6, label: 'Estado da placa', name: 'state_plate', type: 'text' },
        ]
    },
    {
        id: 2,
        name: 'Recurso de Multa',
        slug: 'recurso-multa',
        process_fields: [
            { id: 7, label: 'Número do Processamento', name: 'process_number', type: 'text' },
            { id: 2, label: 'Órgão', name: 'agency', type: 'text' },
            { id: 3, label: 'Placa', name: 'plate', type: 'text' },
            { id: 8, label: 'Observação', name: 'observation', type: 'textarea' },
        ]
    },
    {
        id: 3,
        name: 'Consulta de Veículo',
        slug: 'consulta-veiculo',
        process_fields: [
            { id: 3, label: 'Placa', name: 'plate', type: 'text' },
            { id: 4, label: 'Chassi', name: 'chassis', type: 'text' },
            { id: 5, label: 'Renavam', name: 'renavam', type: 'text' },
        ]
    }
]);

const mockClients = ref([
    { id: 1, name: 'João Silva', slug: 'joao-silva' },
    { id: 2, name: 'Maria Santos', slug: 'maria-santos' },
    { id: 3, name: 'Pedro Oliveira', slug: 'pedro-oliveira' }
]);

const mockSellers = ref([
    { id: 1, name: 'Vendedor 1', slug: 'vendedor-1' },
    { id: 2, name: 'Vendedor 2', slug: 'vendedor-2' },
    { id: 3, name: 'Vendedor 3', slug: 'vendedor-3' }
]);

const mockStatus = ref([
    { id: 1, title: 'Em Andamento', slug: 'em-andamento', color: '#4CAF50', color_text: '#FFFFFF' },
    { id: 2, title: 'Concluído', slug: 'concluido', color: '#2196F3', color_text: '#FFFFFF' },
    { id: 3, title: 'Pendente', slug: 'pendente', color: '#F44336', color_text: '#FFFFFF' },
    { id: 4, title: 'Aguardando', slug: 'aguardando', color: '#FF9800', color_text: '#FFFFFF' }
]);

// Simulate API delay
const simulateDelay = (ms: number = 500) => new Promise(resolve => setTimeout(resolve, ms));

const params = ref<RequestParams>({
    without_pagination: 0,
    page: 1,
    filter: {
        deadline_date: null,
        status: null,
    }
});

const { hasPermissionTo } = useAcl();
const isLoading = ref(false);

const formData = (data: any = {}) => {
    return {
        user_id: data.user_id || '',
        client_id: data.client_id || '',
        status_id: data.status_id || '',
        seller_id: data.seller_id || '',
        service_id: data.service_id || '',
        plate: data.plate || '',
        chassis: data.chassis || '',
        renavam: data.renavam || '',
        state_plate: data.state_plate || '',
        infraction_code: data.infraction_code || '',
        agency: data.agency || '',
        ait: data.ait || '',
        seller: data.seller || '',
        process_value: data.process_value || '',
        payment_method: data.payment_method || '',
        observation: data.observation || '',
        process_number: data.process_number || '',
        deadline_date: data.deadline_date || '',
        updateDeadLine: false,
        slug: data.slug || '',
    }
}

const form = ref(formData());

const processes = ref<Pagination>({
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

const serviceSelectData = ref([]);
const clientSelectData = ref([]);
const statusSelectData = ref([]);
const agenciesList = ref<string[]>(['DETRAN-SP', 'DETRAN-RJ', 'DETRAN-MG', 'DETRAN-PR', 'DETRAN-RS', 'DETRAN-BA', 'DETRAN-GO']);
const sellerSelectData = ref([]);

// Mock total process today
const totalProcessToday = computed(() => {
    const today = new Date().toLocaleDateString('pt-BR');
    return mockProcesses.value.filter(p => p.deadline_date === today).length || mockProcesses.value.length;
});

onMounted(async () => {
    isLoading.value = true;
    await loadProcesses();
    isLoading.value = false;

    await handleServiceData();
    await handleClientData();
    await handleSellerData();
    await handleStatus();
})

const loadProcesses = async () => {
    await simulateDelay(300);
    
    // Apply filters
    let filtered = [...mockProcesses.value];
    
    if (params.value.filter?.status) {
        filtered = filtered.filter(p => p.status.some((s: any) => s.id === params.value.filter.status));
    }
    
    if (params.value.filter?.deadline_date) {
        filtered = filtered.filter(p => p.deadline_date === params.value.filter.deadline_date);
    }
    
    // Apply search
    if (params.value.search) {
        const search = params.value.search.toLowerCase();
        filtered = filtered.filter(p => 
            p.ait?.toLowerCase().includes(search) ||
            p.plate?.toLowerCase().includes(search) ||
            p.client?.name?.toLowerCase().includes(search) ||
            p.process_number?.toLowerCase().includes(search)
        );
    }
    
    // Pagination
    const perPage = params.value.per_page || 10;
    const page = params.value.page || 1;
    const start = (page - 1) * perPage;
    const end = start + perPage;
    const paginated = filtered.slice(start, end);
    
    processes.value = {
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
    
    // Simulate Socket.IO connection
    console.log('Socket.IO connected (simulated)');
};

const showModalFilter = ref(false);
const closeModalFilter = () => {
    showModalFilter.value = false;
};

const handleFilter = () => {
    loadProcesses();
};

const resetFilters = () => {
    if (params.value.filter) {
        params.value.filter.status = null;
        params.value.filter.deadline_date = null;
    }

    loadProcesses();
    closeModalFilter();
};

interface ProcessData {
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
    status: string;
}

const processesData = ref<ProcessData[]>([]);
const prepareDataToTable = () => {
    processesData.value = processes.value.data.map((process): ProcessData => {
        const activeStatus = process.status?.find((s: any) => s.pivot?.is_active === 1);
        return {
            id: process.id,
            slug: process.slug,
            AIT: process.ait,
            'Data limite': formatDateToDisplay(process.deadline_date), // Format to DD/MM/YYYY
            Serviço: process.service?.name,
            Placa: process.plate,
            Chassi: process.chassis,
            Renavam: process.renavam,
            'Nome do cliente': process.client?.name,
            CPF: process.client?.cpf,
            'Data de nascimento': process.client?.birth_date ? formatDateToDisplay(process.client.birth_date) : process.client?.birth_date,
            status: activeStatus?.title || 'Sem status',
        }
    })
}

const getItemById = (id: number) => processes.value.data.find((item) => item.id === id);

const handlePageChange = async (pageUrl: string) => {
    if (pageUrl) {
        isLoading.value = true;

        const urlParams = new URL(pageUrl);
        const page = parseInt(urlParams.searchParams.get('page') || '1', 10);

        params.value.page = page;

        await loadProcesses();
        isLoading.value = false;
    }
};

const handlePerPageChange = (newPerPage: any) => {
    params.value.per_page = newPerPage;
    params.value.page = 1;
    loadProcesses();
};

const handleSearch = (searchTerm: string) => {
    params.value.search = searchTerm;
    loadProcesses();
};

const handleServiceData = async () => {
    if(hasPermissionTo('Read service')){
        await simulateDelay(200);
        const services = mockServices.value;
        allServices.value = services;
        serviceSelectData.value = prepareSelectData(services);
    }
};

const handleClientData = async () => {
    if(hasPermissionTo('Read client')){
        await simulateDelay(200);
        const clients = mockClients.value;
        clientSelectData.value = prepareSelectData(clients);
    }
};

const handleStatus = async () => {
    if(hasPermissionTo('Read status') || hasPermissionTo('Work process')) {
        await simulateDelay(200);
        const status = mockStatus.value;
        statusSelectData.value = prepareSelectDataStatus(status);
    }
};

const handleSellerData = async () => {
    await simulateDelay(200);
    const sellers = mockSellers.value;
    sellerSelectData.value = prepareSelectData(sellers);
};

const prepareSelectData = (data: any) => {
    return data.map((item: any) => {
        return {
            value: item.id,
            text: item.name
        }
    });
}

const prepareSelectDataStatus = (data: any) => {
    return data.map((item: any) => {
        return {
            value: item.id,
            text: item.title
        }
    });
}

const actions: Action[] = [
    {
        name: 'edit',
        hasPermission: hasPermissionTo('Update process'),
        action: (item) => {
            const process = getItemById(item.id);
            if (process) {
                form.value = formData(process);
                form.value.deadline_date = formatDateToISO(form.value.deadline_date);
                const activeStatus = process.status?.find((s: any) => s.pivot?.is_active === 1);
                form.value.status_id = activeStatus?.id || '';
                console.log('form', form.value);
                
                // Extract unique agencies from mock processes
                const uniqueAgencies = [...new Set(mockProcesses.value.map(p => p.agency).filter(Boolean))];
                agenciesList.value = uniqueAgencies.length > 0 ? uniqueAgencies : ['DETRAN-SP', 'DETRAN-RJ', 'DETRAN-MG', 'DETRAN-PR', 'DETRAN-RS', 'DETRAN-BA', 'DETRAN-GO'];
                
                handleProcessList();
            showModal.value = true;
            }
        },
        icon: 'edit',
        class: 'light blue',
    },
    {
        name: 'delete',
        hasPermission: hasPermissionTo('Delete process'),
        action: (item) => {
            currentItem.value = getItemById(item.id);
            showDeleteModal.value = true;
        },
        icon: 'trash',
        class: 'light red',
    },
    {
        name: 'show',
        hasPermission: hasPermissionTo('Read process'),
        action: (item) => {
            currentItem.value = getItemById(item.id);
            showViewModal.value = true;
        },
        icon: 'eye',
        class: 'light btn-outline-primary',
    },
    {
        name: 'refresh',
        hasPermission: hasPermissionTo('Refresh process date'),
        action: (item) => {
            currentItem.value = getItemById(item.id);
            showRefreshModal.value = true;
        },
        icon: 'refresh',
        class: 'light dark-blue',
    },
    {
        name: 'pdf',
        hasPermission: hasPermissionTo('Create PDF Contract'),
        action: async (item) => {
            currentItem.value = getItemById(item.id);
            
            isLoading.value = true;
            await simulateDelay(1000);
            // Simulate PDF download
            const link = document.createElement('a');
            link.href = '#';
            link.download = `contrato-${item.slug}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            isLoading.value = false;
        },
        icon: 'file-pdf',
        class: 'light blue',
    },
]

const showModal = ref(false);

const handleCloseModal = () => {
    showModal.value = false;
    processFields.value = [];
  form.value = formData();
};

const handleAddClick = () => {
    // Extract unique agencies from mock processes
    const uniqueAgencies = [...new Set(mockProcesses.value.map(p => p.agency).filter(Boolean))];
    agenciesList.value = uniqueAgencies.length > 0 ? uniqueAgencies : ['DETRAN-SP', 'DETRAN-RJ', 'DETRAN-MG', 'DETRAN-PR', 'DETRAN-RS', 'DETRAN-BA', 'DETRAN-GO'];
    showModal.value = true;
};

const handleModalTitle = () => {
    return form.value.plate != '' ? 'Editar Processo' : 'Adicionar Processo';
}

const handleSubmit = async() => {
    isLoading.value = true;
    await simulateDelay(500);
    
    // Ensure deadline_date is in the correct format
    const deadlineDate = form.value.deadline_date ? formatDateToISO(form.value.deadline_date) : form.value.deadline_date;
    
    if(form.value.slug) {
        // Update existing
        const index = mockProcesses.value.findIndex(p => p.slug === form.value.slug);
        if (index !== -1) {
            // Remove deadline_date from form.value to ensure the formatted one is used
            const { deadline_date, ...formDataWithoutDeadline } = form.value;
            mockProcesses.value[index] = {
                ...mockProcesses.value[index],
                ...formDataWithoutDeadline,
                deadline_date: deadlineDate, // Ensure deadline_date is updated with formatted date
                client: mockClients.value.find(c => c.id == form.value.client_id),
                seller: mockSellers.value.find(s => s.id == form.value.seller_id),
                service: mockServices.value.find(s => s.id == form.value.service_id),
            };
        }
    } else {
        // Create new
        const newProcess = {
            id: Math.max(...mockProcesses.value.map(p => p.id)) + 1,
            slug: `process-${Date.now()}`,
            ...form.value,
            deadline_date: deadlineDate, // Ensure deadline_date is set
            client: mockClients.value.find(c => c.id == form.value.client_id),
            seller: mockSellers.value.find(s => s.id == form.value.seller_id),
            service: mockServices.value.find(s => s.id == form.value.service_id),
            status: [{
                id: form.value.status_id,
                ...mockStatus.value.find(s => s.id == form.value.status_id),
                pivot: { is_active: 1 },
                user: { name: 'Current User' },
                created_at: new Date().toISOString()
            }]
        };
        mockProcesses.value.push(newProcess);
    }
    
    await loadProcesses();
    isLoading.value = false;
    form.value = formData();
    processFields.value = [];
    showModal.value = false;
}

const handleSubmitStatus = async() => {
    if(currentItem.value && currentItem.value.slug) {
        isLoading.value = true;
        await simulateDelay(500);
        
        const index = mockProcesses.value.findIndex(p => p.slug === currentItem.value.slug);
        if (index !== -1) {
            // Update status
            const newStatus = {
                id: form.value.status_id,
                ...mockStatus.value.find(s => s.id == form.value.status_id),
                pivot: { is_active: 1 },
                user: { name: 'Current User' },
                created_at: new Date().toISOString()
            };
            
            // Deactivate old status
            mockProcesses.value[index].status.forEach((s: any) => {
                s.pivot.is_active = 0;
            });
            
            // Add new status
            mockProcesses.value[index].status.push(newStatus);
            
            // Update deadline_date if updateDeadLine is true, ensuring it's in the correct format
            if (form.value.updateDeadLine && form.value.deadline_date) {
                mockProcesses.value[index].deadline_date = formatDateToISO(form.value.deadline_date);
            }
        }
        
        isLoading.value = false;
    }

    await loadProcesses();
    showUpdateStatusModal.value = false;
    form.value.status_id = '';
    form.value.updateDeadLine = false;
    form.value.deadline_date = ''; // Reset deadline_date after submission
}

const handleDelete = async () => {
    if (currentItem.value && currentItem.value.slug) {
        isLoading.value = true;
        await simulateDelay(500);
        
        const index = mockProcesses.value.findIndex(p => p.slug === currentItem.value.slug);
        if (index !== -1) {
            mockProcesses.value.splice(index, 1);
        }
        
        await loadProcesses();
        isLoading.value = false;
    }
}

const handleRefresh = async () => {
    if (currentItem.value && currentItem.value.slug) {
        isLoading.value = true;
        await simulateDelay(500);
        
        const index = mockProcesses.value.findIndex(p => p.slug === currentItem.value.slug);
        if (index !== -1) {
            // Simulate refresh - update deadline date
            const newDate = new Date();
            newDate.setDate(newDate.getDate() + 30);
            mockProcesses.value[index].deadline_date = newDate.toLocaleDateString('pt-BR');
        }
        
        await loadProcesses();
        form.value.updateDeadLine = false;
        isLoading.value = false;
    }
}

const handleExport = async () => {
    isLoading.value = true;
    await simulateDelay(1000);
    
    // Simulate export
    const csv = 'AIT,Data Limite,Cliente,CPF\n' +
        mockProcesses.value.map(p => 
            `${p.ait},${p.deadline_date},${p.client?.name},${p.client?.cpf}`
        ).join('\n');
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `processos-export-${Date.now()}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    isLoading.value = false;
}

const showModalImportFile = ref(false);
const importFile = ref<File | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

const resetFileInput = () => {
  importFile.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const handleCloseModaImportFile = () => {
  showModalImportFile.value = false;
  resetFileInput();
};

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    importFile.value = target.files[0];
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
    form.value.deadline_date = ''; // Reset deadline_date when closing modal
};

const getFilledFields = (item: any) => {
    const fields = [];
    const keysToDisplay = [
        'service',
        'ait',
        'process_value',
        'payment_method',
        'infraction_code',
        'plate',
        'renavam',
        'process_number',
        'chassis',
        'deadline_date',
        'status',
    ];

    for (const key of keysToDisplay) {
        if (item[key] != undefined) {
            let value = item[key];
            
            if (key == 'status' && Array.isArray(value)) {
                let currentStatus = value?.find(status => status.pivot?.is_active === 1);
                value = currentStatus ? currentStatus.title : 'Status não disponível';
            }

            if (key == 'client') {
                value = item.client ? item.client : 'Cliente não disponível';
            }

            if (key == 'service') {
                value = item.service ? item.service.name : 'Serviço não disponível';
            }

            // Format deadline_date to DD/MM/YYYY for display
            if (key == 'deadline_date' && value) {
                value = formatDateToDisplay(value);
            }

            fields.push({ label: key, value });
        }
    }
    return fields;
};

const submitImportFile = async () => {
  if (!importFile.value) return;

  isLoading.value = true;
  await simulateDelay(1500);

  // Simulate import - add some processes
  const newProcesses = [
    {
        id: mockProcesses.value.length + 1,
        slug: `process-import-${Date.now()}-1`,
        ait: '111111111',
        deadline_date: '25/12/2024',
        plate: 'IMP-0001',
        chassis: '9BW11111111111111',
        renavam: '11111111111',
        process_number: '2024009999',
        process_value: '400.00',
        payment_method: 'Pix',
        service: mockServices.value[0],
        client: mockClients.value[0],
        seller: mockSellers.value[0],
        user: { id: 1, name: 'Admin', email: 'admin@email.com' },
        status: [{
            id: 1,
            ...mockStatus.value[0],
            pivot: { is_active: 1 },
            user: { name: 'System' },
            created_at: new Date().toISOString()
        }]
    }
  ];
  
  mockProcesses.value.push(...newProcesses);
  resetFileInput();
  await loadProcesses();
  isLoading.value = false;
};

interface ProcessField {
    label: string;
    name: string;
    type: string;
}

const processFields = ref<ProcessField[]>([]);

const handleProcessList = () => {
    processFields.value = getProcessFieldsByServiceId();
}

const allServices = ref<Service[]>([]);
const getProcessFieldsByServiceId = () => {
    const service = allServices.value.find((service: Service) => service.id == form.value.service_id);
    return service ? service.process_fields : [];
}

const showDeleteModal = ref(false);
const showRefreshModal = ref(false);
const showViewModal = ref(false);
const showUpdateStatusModal = ref(false);

const currentItem = ref<Process | null>(null);

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

const closeRefreshModal = () => {
    showRefreshModal.value = false;
    currentItem.value = null;
};

const confirmRefresh = () => {
    if (currentItem.value) {
        form.value.updateDeadLine = true
        handleRefresh();
    }
    closeRefreshModal();
};

const handleRowClick = (item: any) => {
    if(hasPermissionTo('Work process')){
        const process = getItemById(item.id);
        if (process) {
            const activeStatus = process.status?.find((status: any) => status.pivot?.is_active === 1);
            currentItem.value = process;
            // Format deadline_date to ISO format for the date input
            form.value.deadline_date = process.deadline_date ? formatDateToISO(process.deadline_date) : process.deadline_date;
            form.value.updateDeadLine = false; // Reset updateDeadLine flag

            if (activeStatus) {
                form.value.status_id = activeStatus.id;
            }
            showUpdateStatusModal.value = true;
        }
    }
};

const formatDate = (dateString: string) =>{
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
  
    return `${day}-${month}-${year} ${hours}:${minutes}`;
}

const isObjectNotEmpty = (obj: any) => {
    return Object.values(obj).some(item => item !== null);
}

const paymentMethod = [
    { value: 'Dinheiro', text: 'Dinheiro' },
    { value: 'Crédito à vista', text: 'Crédito à vista' },
    { value: 'Crédito parcelado', text: 'Crédito parcelado' },
    { value: 'Cartão de débito', text: 'Cartão de débito' },
    { value: 'Pix', text: 'Pix' },
    { value: 'Boleto', text: 'Boleto' },
    { value: 'Transferência', text: 'Transferência' },
]

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
}

const formatDateToISO = (date: string) => {
    if (!date) return '';
    
    // Se já está no formato ISO (YYYY-MM-DD), retorna direto
    if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
        return date;
    }
    
    // Se está no formato DD/MM/YYYY, converte para ISO
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(date)) {
        const [day, month, year] = date.split('/');
        return `${year}-${month}-${day}`;
    }
    
    // Tenta converter de qualquer formato de data válido
    try {
        const dateObj = new Date(date);
        if (!isNaN(dateObj.getTime())) {
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    } catch (e) {
        console.error('Error formatting date:', e);
    }
    
    return date; // Retorna o valor original se não conseguir converter
}

// Format date to DD/MM/YYYY for display
const formatDateToDisplay = (date: string) => {
    if (!date) return '';
    
    // Se já está no formato DD/MM/YYYY, retorna direto
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(date)) {
        return date;
    }
    
    // Se está no formato ISO (YYYY-MM-DD), converte para DD/MM/YYYY
    if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
        const [year, month, day] = date.split('-');
        return `${day}/${month}/${year}`;
    }
    
    // Tenta converter de qualquer formato de data válido
    try {
        const dateObj = new Date(date);
        if (!isNaN(dateObj.getTime())) {
            const day = String(dateObj.getDate()).padStart(2, '0');
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const year = dateObj.getFullYear();
            return `${day}/${month}/${year}`;
        }
    } catch (e) {
        console.error('Error formatting date for display:', e);
    }
    
    return date; // Retorna o valor original se não conseguir converter
}

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
                    <SelectComponent v-model="form.client_id" :options="clientSelectData" />
                </FormGroupComponent>
            </div>
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Vendedor" />
                    <SelectComponent v-model="form.seller_id" :options="sellerSelectData" />
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
                    <SelectComponent v-model="form.status_id" :options="statusSelectData" />
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
                    <money3 class="form-control" v-model="form.process_value" v-bind="moneyConfig"></money3>
                </FormGroupComponent>
            </div>
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Forma de pagamento" />
                    <SelectComponent v-model="form.payment_method" :options="paymentMethod" />
                </FormGroupComponent>
            </div>
        </div>        
    
        <FormGroupComponent>
            <LabelComponent text="Serviços" />
            <SelectComponent v-model="form.service_id" :options="serviceSelectData" @change="handleProcessList()" />
        </FormGroupComponent>

        <template v-if="processFields.length > 0">
            <FormGroupComponent v-for="(field, index) in processFields" :key="index">
                <LabelComponent :text="field.label" />
                <AutocompleteComponent 
                    v-if="field.name === 'agency'"
                    v-model="form[field.name]" 
                    :placeholder="field.label" 
                    :type="field.type"
                    :suggestions="agenciesList"
                />
                <InputComponent 
                    v-else
                    v-model="form[field.name]" 
                    :placeholder="field.label" 
                    :type="field.type" 
                />
            </FormGroupComponent>
        </template>

        <FormGroupComponent v-else>
            <LabelComponent text="Nenhum campo disponível para este serviço" />
        </FormGroupComponent>
 
    </ModalComponent>

    <!-- modal para importar processos -->
    <ModalComponent v-if="hasPermissionTo('Import data')" :show="showModalImportFile" @closeModal="handleCloseModaImportFile" titleHeader="Importar Processos" @submit="submitImportFile()">
        <FormGroupComponent>
            <InputComponent ref="fileInput" placeholder="Selecione o arquivo" @change="handleFileChange($event)" type="file" accept=".xls,.xlsx" />
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
                        <span>{{ $t(field.label) }}:</span> <span>{{ field.value }}</span>
                    </div>
                </div>
                <div class="column">
                    <div class="card-text card-flex" v-for="(field, index) in getFilledFields(currentItem).slice(Math.ceil(getFilledFields(currentItem).length / 2))" :key="index">
                        <span>{{ $t(field.label) }}:</span> <span>{{ field.value }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" v-if="currentItem && currentItem.user">
            <div class="card-header card-w-700">
                Usuário
            </div>
            <div class="card-body flex-body">
                <div class="column">
                    <div class="card-text card-flex">
                        <span>Nome:</span>
                        <span>{{ currentItem.user.name }}</span>
                    </div>
                </div>
                <div class="column">
                    <div class="card-text card-flex">
                        <span>E-mail:</span>
                        <span>{{ currentItem.user.email }}</span>
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
                        <span>{{ currentItem.client?.name }}</span>
                    </div>
                    <div class="card-text card-flex">
                        <span>CPF:</span>
                        <span>{{ currentItem.client?.cpf }}</span>
                    </div>
                    <div class="card-text card-flex">
                        <span>Telefone:</span>
                        <span>{{ currentItem.client?.phone }}</span>
                    </div>
                </div>
                <div class="column">
                    <div class="card-text card-flex">
                        <span>Cidade:</span>
                        <span>{{ currentItem.client?.city }} ({{ currentItem.client?.state }})</span>
                    </div>
                    <div class="card-text card-flex">
                        <span>Endereço:</span>
                        <span>{{ currentItem.client?.address }}, {{ currentItem.client?.number }}</span>
                    </div>
                    <div class="card-text card-flex">
                        <span>Complemento:</span>
                        <span>{{ currentItem.client?.address }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" v-if="currentItem">
            <div class="card-header card-w-700">
                Vendedor
            </div>
            <div class="card-body flex-body">
                <div class="column">
                    <div class="card-text card-flex">
                        <span>Nome:</span><span>{{ currentItem.seller?.name }}</span>
                    </div>
                </div>
                <div class="column">
                    <p class="card-text card-flex"><span>Email:</span>{{ currentItem.seller?.email }}</p>
                </div>
            </div>
        </div>

        <div class="card" v-if="currentItem">
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
                    <ButtonComponent buttonClass="btn-secondary btn-import" text="Importar" icon="file-upload" light @click="showModalImportFile = true" v-if="hasPermissionTo('Import data')" />
                    <ButtonComponent buttonClass="btn-secondary btn-export" text="Exportar" icon="file-download" light @click="handleExport()" v-if="hasPermissionTo('Export data')" />
                    <ButtonComponent buttonClass="btn-secondary" text="Adicionar" icon="plus" light @click="handleAddClick" v-if="hasPermissionTo('Create process')" />
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
                :pagination="processes"
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
                <SelectComponent v-model="form.status_id" :options="statusSelectData" />
            </FormGroupComponent>
            <FormGroupComponent>
                <div class="form-check form-switch">
                    <SwitchComponent v-model="form.updateDeadLine"/>
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
            <SelectComponent v-model="params.filter.status" :options="statusSelectData" />
        </FormGroupComponent>

        <FormGroupComponent>
            <LabelComponent text="Data limite" />
            <InputComponent v-model="params.filter.deadline_date" type="date" />
        </FormGroupComponent>

        <FormGroupComponent v-if="isObjectNotEmpty(params.filter)">
            <ButtonComponent buttonClass="btn-light light" type="button" text="Limpar" light @click="resetFilters()" />
        </FormGroupComponent>
    </ModalComponent>

</template>
