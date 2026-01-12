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
import { onMounted, ref, computed, watch } from 'vue';
import type { Pagination } from '@/interfaces/pagination/pagination.interface';
import type { Action } from '@/types/ActionType';
import { useProcessStore } from '@/stores/process.store';
import { useServiceStore } from '@/stores/service.store';
import type { RequestParams } from '@/interfaces/request-params.interface';
import type { Service } from '@/interfaces/service.interface';
import { useClientStore } from '@/stores/client.store';
import { useExportStore } from '@/stores/export.store';
import { useImportStore } from '@/stores/import.store';
import { useSellerStore } from '@/stores/seller.store';
import { useStatusStore } from '@/stores/status.store';
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';
import ConfirmRefreshModal from '@/components/ConfirmRefreshModal.vue';
import { useAcl } from '@/utils/acl';
import BoxFileComponent from '@/components/BoxFileComponent.vue';
import SwitchComponent from '@/components/form/SwitchComponent.vue';
import TableComponentProcess from '@/components/TableComponentProcess.vue';
import IconComponent from '@/components/IconComponent.vue';
import { Process } from '@/interfaces/process.interface';
import { index } from '@/utils/generalAPI';

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
    // Se payment_method é uma string, converter para array
    let paymentMethods = [];
    if (data.payment_method) {
        if (typeof data.payment_method === 'string') {
            // Se contém vírgula, separar; senão, usar como único item
            paymentMethods = data.payment_method.includes(',') 
                ? data.payment_method.split(',').map((m: string) => m.trim()).filter((m: string) => m)
                : [data.payment_method];
        } else if (Array.isArray(data.payment_method)) {
            paymentMethods = data.payment_method;
        }
    }
    
    // Se não há formas de pagamento, adicionar uma vazia
    if (paymentMethods.length === 0) {
        paymentMethods = [''];
    }
    
    return {
        id: data.id || null,
        user_id: data.user_id || '',
        client_id: data.client_id || null,
        status_id: data.status_id || null,
        seller_id: data.seller_id || null,
        service_id: data.service_id || null,
        plate: data.plate || '',
        chassis: data.chassis || '',
        renavam: data.renavam || '',
        state_plate: data.state_plate || '',
        infraction_code: data.infraction_code || '',
        agency: data.agency || '',
        ait: data.ait || '',
        seller: data.seller || '',
        process_value: data.process_value || '',
        payment_methods: paymentMethods,
        observation: data.observation || '',
        process_number: data.process_number || '',
        deadline_date: data.deadline_date || '',
        updateDeadLine: false,
        slug: data.slug || '',
    }
}

const form = ref(formData());
// Preservar slug e ID separadamente para evitar perda durante edição
const currentProcessSlug = ref<string>('');
const currentProcessId = ref<number | null>(null);

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

const processStore = useProcessStore();
const serviceStore = useServiceStore();
const clientStore = useClientStore();
const sellerStore = useSellerStore();
const statusStore = useStatusStore();

const exportStore = useExportStore();
const importStore = useImportStore();

const serviceSelectData = ref([]);
const clientSelectData = ref([]);
const statusSelectData = ref([]);

const sellerSelectData = ref([]);
const agenciesList = ref<string[]>([]);
const statusTitlesList = ref<string[]>([]);
const statusTitleToIdMap = ref<Record<string, number>>({});
const statusTitle = ref<string>('');

const totalProcessToday = computed(() => processStore.getTotalToday);

onMounted(async () => {
    isLoading.value = true;
    await loadProcesses();
    isLoading.value = false;

    await handleServiceData();
    await handleClientData();
    await handleSellerData();
    await handleStatus();
})

// Watcher para buscar chassi e renavam quando a placa for digitada
watch(() => form.value.plate, async (newPlate, oldPlate) => {
    // Só buscar se:
    // 1. A placa foi alterada
    // 2. Não estamos editando (não tem slug)
    // 3. A placa tem pelo menos 3 caracteres
    // 4. Os campos chassi e renavam estão vazios
    if (newPlate && newPlate !== oldPlate && !form.value.slug && newPlate.length >= 3) {
        if (!form.value.chassis && !form.value.renavam) {
            await fetchProcessByPlate(newPlate);
        }
    }
});

watch(() => form.value.updateDeadLine, (newValue, oldValue) => {
    if (showUpdateStatusModal.value) {
    }
});

const fetchProcessByPlate = async (plate: string) => {
    try {
        const result = await index('/processes/by-plate', { plate } as any);
        if (result && result.data && result.data.chassis && result.data.renavam) {
            form.value.chassis = result.data.chassis;
            form.value.renavam = result.data.renavam;
        }
    } catch (error) {
        // Silenciosamente ignora erros (placa não encontrada, etc)
        console.log('Placa não encontrada ou erro ao buscar:', error);
    }
};

const loadProcesses = async () => {
    await processStore.index(params.value);
    processStore.connectSocketIO();
    processes.value = processStore.getProcesses;
    // Force refresh of table data to ensure updated deadline_date is displayed
    prepareDataToTable();
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
    Status: string;
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
        // Get active status
        const activeStatus = process.status?.find((status: any) => status.pivot?.is_active === 1 || status.pivot?.is_active === true);
        const statusTitle = activeStatus ? activeStatus.title : 'Status não disponível';
        
        return {
            id: process.id,
            slug: process.slug,
            AIT: process.ait,
            'Data limite': formatDateToDisplay(process.deadline_date), // Format to DD/MM/YYYY
            Serviço: process.service?.name,
            Status: statusTitle,
            Placa: process.plate,
            Chassi: process.chassis,
            Renavam: process.renavam,
            'Nome do cliente': process.client?.name,
            CPF: process.client?.cpf,
            'Data de nascimento': process.client?.birth_date ? formatDateToDisplay(process.client.birth_date) : process.client?.birth_date,
            status: process.status,
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
        await serviceStore.index({ without_pagination: 1 });
    
        const services = serviceStore.getServices;
        allServices.value = services;
        serviceSelectData.value = prepareSelectData(services);
    }
};

const handleClientData = async () => {
    if(hasPermissionTo('Read client')){
        await clientStore.index({ without_pagination: 1 });
        const clients = clientStore.getClients;
        clientSelectData.value = prepareSelectData(clients);
    }
};

const handleStatus = async () => {
    if(hasPermissionTo('Read status') || hasPermissionTo('Work process')) {
        await statusStore.index({ without_pagination: 1 });
        const status = statusStore.getstatus;
        statusSelectData.value = prepareSelectDataStatus(status);
        
        // Preparar lista de títulos e mapeamento para autocomplete
        // status pode ser um objeto Pagination com .data ou um array direto
        let statusArray: any[] = [];
        if (status) {
            if (Array.isArray(status)) {
                statusArray = status;
            } else if (status.data && Array.isArray(status.data)) {
                statusArray = status.data;
            }
        }
        
        if (statusArray.length > 0) {
            statusTitlesList.value = statusArray.map((item: any) => item.title || '').filter((title: string) => title !== '');
            statusTitleToIdMap.value = {};
            statusArray.forEach((item: any) => {
                if (item.title && item.id) {
                    statusTitleToIdMap.value[item.title] = item.id;
                }
            });
        } else {
            statusTitlesList.value = [];
            statusTitleToIdMap.value = {};
        }
    } else {
        // Se não tem permissão, limpar as listas
        statusTitlesList.value = [];
        statusTitleToIdMap.value = {};
    }
};

const handleSellerData = async () => {
    await sellerStore.index({ without_pagination: 1 });
    const sellers = sellerStore.getSellers;
    sellerSelectData.value = prepareSelectData(sellers);
};

const loadAgencies = async () => {
    try {
        const agencies = await processStore.fetchUniqueAgencies();
        agenciesList.value = agencies || [];
    } catch (error) {
        console.error('Error loading agencies:', error);
        agenciesList.value = [];
    }
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
    if (!data) return [];
    // Se data é um objeto Pagination, usar data.data, senão usar data diretamente
    const statusArray = Array.isArray(data) ? data : (data.data || []);
    return statusArray.map((item: any) => {
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
        action: async (item) => {
            // Recarregar os processos para garantir que temos os dados mais recentes
            await loadProcesses();
            
            // Buscar o processo atualizado da lista recarregada
            const updatedProcessItem = processes.value.data.find((p: any) => p.id === item.id) || getItemById(item.id);
            
            // Preservar slug e ID ANTES de qualquer outra operação (crítico!)
            // Usar item.slug e item.id diretamente se disponível, senão buscar do processItem
            const slugToPreserve = item.slug || updatedProcessItem?.slug || '';
            const idToPreserve = item.id || updatedProcessItem?.id || null;
            
            // Preservar em variáveis separadas (não serão afetadas por mudanças no form)
            currentProcessSlug.value = slugToPreserve;
            currentProcessId.value = idToPreserve;
            
            form.value = formData(updatedProcessItem);
            form.value.deadline_date = formatDateToISO(form.value.deadline_date);
            
            // Garantir que o slug e ID estão presentes no form também
            if (slugToPreserve) {
                form.value.slug = slugToPreserve;
            }
            if (idToPreserve) {
                form.value.id = idToPreserve;
            }
            
            // Buscar o status ativo (is_active = true) em vez de apenas o primeiro
            const activeStatus = updatedProcessItem.status?.find((status: any) => status.pivot?.is_active === 1 || status.pivot?.is_active === true);
            if (activeStatus) {
                form.value.status_id = activeStatus.id;
                // Definir statusTitle para o autocomplete
                statusTitle.value = activeStatus.title || '';
            } else {
                // Se não houver status ativo, limpar os campos
                form.value.status_id = null;
                statusTitle.value = '';
            }
            
            
            await loadAgencies();
            await handleStatus(); // Garantir que os status estão carregados
            handleProcessList();
            showModal.value = true;
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
            await processStore.pdf({ templateName: 'Contrato' }, item.slug)
            isLoading.value = false;
        },
        icon: 'file-pdf',
        class: 'light blue',
    },
    {
        name: 'folder',
        hasPermission: hasPermissionTo('Read client file'),
        action: async (item) => {
            const processItem = getItemById(item.id);
            if (processItem && processItem.client && processItem.client.slug) {
                currentClientItem.value = processItem.client;
                await setFilesOnClient(processItem.client.slug);
                showModalFiles.value = true;
            }
        },
        icon: 'folder',
        class: 'light blue',
    },
]

const showModal = ref(false);
const showModalFiles = ref(false);
const clientsFiles = ref<any[]>([]);
const currentClientItem = ref<any>(null);
const fileToDelete = ref<number | null>(null);
const showDeleteModalFile = ref(false);
const isSubmitting = ref(false); // Flag para evitar resetar valores durante submit

const handleAddClick = async () => {
  form.value = formData();
  currentProcessSlug.value = ''; // Limpar slug ao adicionar novo processo
  currentProcessId.value = null; // Limpar ID ao adicionar novo processo
  statusTitle.value = '';
  await loadAgencies();
  await handleStatus(); // Garantir que os status estão carregados
  showModal.value = true;
};

const handleCloseModal = () => {
  
  // Não resetar valores se ainda está fazendo submit (evita perder dados)
  if (isSubmitting.value) {
    return;
  }
  
  showModal.value = false;
  processFields.value = [];
  form.value = formData();
  currentProcessSlug.value = ''; // Limpar slug ao fechar modal
  currentProcessId.value = null; // Limpar ID ao fechar modal
  statusTitle.value = '';
};

const handleModalTitle = () => {
    return form.value.slug ? 'Editar Processo' : 'Adicionar Processo';
}

const loadClientAndReturnFiles = async (slug: string) => {
    await clientStore.show(slug);
    const client: any = clientStore.getClient;
    
    // Verificar se client tem a estrutura esperada
    // O client pode vir como { data: { files: [] } } ou diretamente { files: [] }
    if (client) {
        if (client.data && client.data.files) {
            return client.data.files;
        } else if (client.files) {
            return client.files;
        }
    }
    return [];
};

const setFilesOnClient = async (slug: string) => {
    const filesUpdated = await loadClientAndReturnFiles(slug);
    clientsFiles.value = filesUpdated;
};

const handleCloseModalFiles = () => {
    showModalFiles.value = false;
    currentClientItem.value = null;
    clientsFiles.value = [];
};

const handleFileClick = (file: any) => {
    const baseUrl = import.meta.env.VITE_API_URL_FILES;
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

const openDeleteModalFile = (file: any) => {
    fileToDelete.value = file.id;
    showDeleteModalFile.value = true;
};

const handleDeleteFile = async (fileId: number | null) => {
    if (!fileId) return;
    isLoading.value = true;
    await clientStore.deleteFile(fileId);
    clientsFiles.value = clientsFiles.value.filter((file: any) => file.id !== fileId);
    showDeleteModalFile.value = false;
    isLoading.value = false;
};

const getStatusIdFromTitle = async (title: string): Promise<number | null> => {
    if (!title || title.trim() === '') {
        return null;
    }
    
    // Verificar se já existe no mapeamento
    if (statusTitleToIdMap.value[title]) {
        return statusTitleToIdMap.value[title];
    }
    
    // Se não existe e o usuário tem permissão, criar novo status
    if (hasPermissionTo('Create status')) {
        try {
            const newStatus = await statusStore.store({
                title: title,
                color: '#007bff',
                color_text: '#ffffff'
            });
            
            // Atualizar lista e mapeamento
            await handleStatus();
            
            // Retornar o ID do status recém-criado
            if (newStatus && newStatus.data && newStatus.data.id) {
                return newStatus.data.id;
            }
            
            // Se não retornou, buscar no mapeamento atualizado
            if (statusTitleToIdMap.value[title]) {
                return statusTitleToIdMap.value[title];
            }
        } catch (error) {
            console.error('Error creating new status:', error);
}
    }
    
    return null;
};

const handleSubmit = async() => {
    // Marcar como submitting para evitar que handleCloseModal resete os valores
    isSubmitting.value = true;
    
    // PRESERVAR valores NO INÍCIO, ANTES de qualquer outra operação
    // Isso é CRÍTICO porque o ModalComponent pode fechar o modal e resetar os valores
    // antes do handleSubmit terminar (já que é assíncrono)
    
    const preservedProcessId = currentProcessId.value || form.value.id;
    const preservedProcessSlug = currentProcessSlug.value || form.value.slug;
    
    console.log('handleSubmit - preservedProcessId:', preservedProcessId);
    console.log('handleSubmit - preservedProcessSlug:', preservedProcessSlug);
    
    isLoading.value = true;
    
    try {
        // Sempre converter statusTitle para status_id quando statusTitle está preenchido
        // Isso garante que se o usuário mudou o título no autocomplete, o ID será atualizado
        let statusId = form.value.status_id;
        if (statusTitle.value) {
            const idFromTitle = await getStatusIdFromTitle(statusTitle.value);
            if (idFromTitle) {
                statusId = idFromTitle;
            }
        }
        
        // Determinar se é edição usando múltiplas estratégias:
        // 1. preservedProcessId (preservado no início - mais confiável)
        // 2. preservedProcessSlug (preservado no início)
        // 3. Buscar pelo ID nos dados originais
        
        // Usar preservedProcessId como fonte primária (mais confiável que slug)
        const processId = preservedProcessId;
        let processSlug = preservedProcessSlug;
        
        // Se temos ID mas não temos slug, buscar nos dados originais
        if (processId && !processSlug) {
            const originalProcess = processes.value.data.find((p: any) => p.id === processId);
            if (originalProcess && originalProcess.slug) {
                processSlug = originalProcess.slug;
                console.log('handleSubmit - Found slug from processes.data using ID:', processSlug);
            }
        }
        
        // Se temos um ID, é definitivamente uma edição
        const isEdit = !!(processId || processSlug);
        
    
    // Prepare payload ensuring deadline_date is in the correct format
        // Remover slug, id e updateDeadLine do payload (não devem ser enviados)
        const { updateDeadLine, slug, id, payment_methods, ...formDataWithoutUpdateDeadLine } = form.value;
        
        // Converter array de formas de pagamento para string separada por vírgula
        const paymentMethodString = payment_methods && Array.isArray(payment_methods)
            ? payment_methods.filter((m: string) => m && m.trim()).join(', ')
            : '';
        
        const payload: any = {
        ...formDataWithoutUpdateDeadLine,
            payment_method: paymentMethodString,
        deadline_date: form.value.deadline_date ? formatDateToISO(form.value.deadline_date) : form.value.deadline_date,
        params: params.value
    };
    
        // Só incluir status_id no payload se ele foi explicitamente definido
        // Se statusId for null ou undefined, não incluir no payload para não alterar o status atual
        if (statusId !== null && statusId !== undefined) {
            payload.status_id = statusId;
        }
        
        
        // Se é edição mas não temos slug, buscar pelo ID
        if (isEdit && !processSlug && processId) {
            // Buscar o processo completo para obter o slug
            const fullProcess = processes.value.data.find((p: any) => p.id === processId);
            if (fullProcess && fullProcess.slug) {
                processSlug = fullProcess.slug;
            }
        }
        
        // Usar slug para determinar se é edição ou criação
        // O slug não é enviado no payload, apenas usado para identificar a rota
        if(isEdit && processSlug && processSlug.trim() !== '') {
            try {
                await processStore.update(processSlug, payload);
            } catch (error) {
                console.error('handleSubmit - Error on UPDATE:', error);
                throw error;
            }
    }else{
        await processStore.store(payload);
    }
    
    // Reload processes to get updated data from backend, including the updated deadline_date
    await loadProcesses();
    
    isLoading.value = false;
        isSubmitting.value = false; // Permitir que handleCloseModal funcione novamente
        showModal.value = false;
    form.value = formData();
        currentProcessSlug.value = ''; // Limpar slug após salvar
        currentProcessId.value = null; // Limpar ID após salvar
        statusTitle.value = '';
    processFields.value = [];
        
        // Se o modal de atualizar status estiver aberto, atualizar os dados também
        if (showUpdateStatusModal.value && currentItem.value) {
            const updatedProcess = processes.value.data.find((p: any) => p.id === currentItem.value?.id);
            if (updatedProcess) {
                const activeStatus = updatedProcess.status?.find((status: any) => status.pivot?.is_active === 1 || status.pivot?.is_active === true);
                if (activeStatus) {
                    form.value.status_id = activeStatus.id;
                    statusTitle.value = activeStatus.title || '';
                } else {
                    form.value.status_id = null;
                    statusTitle.value = '';
                }
                // Atualizar deadline_date também
                form.value.deadline_date = updatedProcess.deadline_date ? formatDateToISO(updatedProcess.deadline_date) : '';
                // Atualizar currentItem para refletir as mudanças
                currentItem.value = updatedProcess;
            }
        }
    } catch (error) {
        isLoading.value = false;
        isSubmitting.value = false; // Permitir que handleCloseModal funcione novamente mesmo em caso de erro
        throw error;
    }
}

const handleSubmitStatus = async() => {
    // Marcar como submitting para evitar que handleUpdateStatusModal resete os valores
    isSubmittingStatus.value = true;
    
    // PRESERVAR slug ANTES de qualquer operação assíncrona
    // Isso evita que seja perdido se o modal fechar antes do submit terminar
    const preservedSlug = currentItem.value?.slug;
    
    if(!preservedSlug) {
        console.error('handleSubmitStatus - No slug available');
        isSubmittingStatus.value = false;
        return;
    }
    
        isLoading.value = true;
    
    try {
        // PRESERVAR valores ANTES de qualquer operação assíncrona
        // Isso é crítico porque o ModalComponent pode fechar o modal e resetar os valores
        const preservedUpdateDeadLine = form.value.updateDeadLine === true || form.value.updateDeadLine === 'true' || form.value.updateDeadLine === 1 || form.value.updateDeadLine === '1';
        const preservedDeadlineDate = form.value.deadline_date;
        
        
        // Sempre converter statusTitle para status_id quando statusTitle está preenchido
        // Isso garante que se o usuário mudou o título no autocomplete, o ID será atualizado
        let statusId = form.value.status_id;
        if (statusTitle.value) {
            const idFromTitle = await getStatusIdFromTitle(statusTitle.value);
            if (idFromTitle) {
                statusId = idFromTitle;
            }
        }
        
        // Verificar o valor de updateDeadLine antes de criar o payload
        // Usar o valor preservado em vez do valor atual do form (que pode ter sido resetado)
        const updateDeadLineValue = preservedUpdateDeadLine;
        
        
        // Prepare payload ensuring deadline_date is in the correct format when updateDeadLine is true
        const payload: any = {
            status_id: statusId || null,
            updateDeadLine: updateDeadLineValue,
            params: params.value
        };
        
        // Se updateDeadLine for true, sempre enviar deadline_date (mesmo que seja null, para permitir limpar a data)
        // Usar o valor preservado em vez do valor atual do form
        if (updateDeadLineValue) {
            payload.deadline_date = preservedDeadlineDate 
                ? formatDateToISO(preservedDeadlineDate) 
                : null;
        
        await processStore.update(`update-status/${preservedSlug}`, payload);
        
        // Reload processes to get updated data from backend, including the updated deadline_date
        await loadProcesses();
        
        isLoading.value = false;
        isSubmittingStatus.value = false; // Permitir que handleUpdateStatusModal funcione novamente
        showUpdateStatusModal.value = false;
        currentItem.value = null;
        form.value.status_id = '';
        statusTitle.value = '';
        form.value.updateDeadLine = false;
        form.value.deadline_date = ''; // Reset deadline_date after submission
        
        // Se o modal de editar processo estiver aberto, atualizar os dados do form também
        if (showModal.value && currentProcessId.value) {
            const updatedProcess = processes.value.data.find((p: any) => p.id === currentProcessId.value);
            if (updatedProcess) {
                const activeStatus = updatedProcess.status?.find((status: any) => status.pivot?.is_active === 1 || status.pivot?.is_active === true);
                if (activeStatus) {
                    form.value.status_id = activeStatus.id;
                    statusTitle.value = activeStatus.title || '';
                } else {
                    form.value.status_id = null;
                    statusTitle.value = '';
                }
                // Atualizar deadline_date também
                form.value.deadline_date = updatedProcess.deadline_date ? formatDateToISO(updatedProcess.deadline_date) : '';
            }
        }
    } catch (error) {
        isLoading.value = false;
        isSubmittingStatus.value = false; // Permitir que handleUpdateStatusModal funcione novamente mesmo em caso de erro
        throw error;
    }
}

const handleDelete = async () => {
    if (currentItem.value && currentItem.value.slug) {
        isLoading.value = true;
        await processStore.destroy(currentItem.value.slug, { params: params.value });
        await loadProcesses();
        isLoading.value = false;
    }
}

const handleRefresh = async () => {
    if (currentItem.value && currentItem.value.slug) {
        isLoading.value = true;
        await processStore.refresh(`refresh-date/${currentItem.value.slug}`, { ...form.value, params: params.value });
        await loadProcesses();
        form.value.updateDeadLine = false;
        isLoading.value = false;
    }
}


const handleExport = async () => {
    isLoading.value = true;
    await exportStore.fetchExport(params.value);
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
    // Não resetar valores se ainda está fazendo submit (evita perder dados)
    if (isSubmittingStatus.value) {
        console.log('handleUpdateStatusModal - Skipping reset because isSubmittingStatus is true');
        return;
    }
    
    showUpdateStatusModal.value = false;
    currentItem.value = null;
    form.value.status_id = '';
    statusTitle.value = '';
    form.value.updateDeadLine = false;
    form.value.deadline_date = ''; // Reset deadline_date when closing modal
};

// const translations = {
//     service: "Serviço",
//     ait: "AIT",
//     process_value: "Valor do Processo",
//     payment_method: "Método de Pagamento",
//     infraction_code: "Código de infração",
//     plate: "Placa",
//     chassis: "Chassi",
//     deadline_date: "Data Limite",
//     status: "Status",
//     renavam: "Renavam",
//     state_plate: "Estado da placa",
//     agency: "Órgão",
//     observation: "Observação",
//     process_number: "Número do Processamento",
// };

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

  try {
  const formData = new FormData();
  formData.append('file', importFile.value);
  
  await importStore.fetchImport(formData);
  resetFileInput();
    showModalImportFile.value = false;
  await loadProcesses();
  } catch (error) {
    console.error('Erro ao importar arquivo:', error);
    // O erro já foi tratado e exibido no toast pela função importData
  } finally {
  isLoading.value = false;
  }
};

interface ProcessField {
    label: string;
    name: string;
    type: string;
}

const processFields = ref<ProcessField[]>([]);

const handleProcessList = async () => {
    // Garantir que os serviços estão carregados antes de tentar acessar process_fields
    if (allServices.value.length === 0) {
        await handleServiceData();
    }
    processFields.value = getProcessFieldsByServiceId();
    await loadAgencies();
}

const allServices = ref<Service[]>([]);
const getProcessFieldsByServiceId = () => {
    if (!form.value.service_id) {
        return [];
    }
    const service = allServices.value.find((service: Service) => service.id == form.value.service_id);
    if (!service || !service.process_fields) {
        return [];
    }
    // Se process_fields é uma string JSON, fazer parse; se já é um array, retornar diretamente
    if (typeof service.process_fields === 'string') {
        try {
            return JSON.parse(service.process_fields);
        } catch (e) {
            console.error('Error parsing process_fields:', e);
            return [];
        }
    }
    return service.process_fields || [];
};

const showDeleteModal = ref(false);
const showRefreshModal = ref(false);
const showViewModal = ref(false);
const showUpdateStatusModal = ref(false);
const isSubmittingStatus = ref(false); // Flag para evitar resetar valores durante submit

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

const handleRowClick = async (item: any) => {
    //verificar se tem permissão Work process
    if(hasPermissionTo('Work process')){
        // Recarregar os processos para garantir que temos os dados mais recentes
        await loadProcesses();
        
        // Buscar o processo atualizado da lista recarregada
        const updatedItem = processes.value.data.find((p: any) => p.id === item.id) || item;
        
        // Buscar o status ativo (is_active = true ou 1)
        const activeStatus = updatedItem.status?.find((status: any) => status.pivot?.is_active === 1 || status.pivot?.is_active === true);
        
        currentItem.value = updatedItem;
        // Format deadline_date to ISO format for the date input
        form.value.deadline_date = updatedItem.deadline_date ? formatDateToISO(updatedItem.deadline_date) : updatedItem.deadline_date;
        form.value.updateDeadLine = false; // Reset updateDeadLine flag

        if (activeStatus) {
            form.value.status_id = activeStatus.id;
            statusTitle.value = activeStatus.title || '';
        } else {
            form.value.status_id = null;
            statusTitle.value = '';
        }
        showUpdateStatusModal.value = true;
        
    }
};

const formatDate = (dateString: string) =>{
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Os meses são baseados em zero
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
    // { value: 'Cheque', text: 'Cheque' },
    // { value: 'Outro', text: 'Outro' },
];

// Funções para gerenciar múltiplas formas de pagamento
const addPaymentMethod = () => {
    if (!form.value.payment_methods) {
        form.value.payment_methods = [''];
    }
    form.value.payment_methods.push('');
};

const removePaymentMethod = (index: number) => {
    // Não permitir remover o primeiro método de pagamento
    if (index > 0 && form.value.payment_methods && form.value.payment_methods.length > 1) {
        form.value.payment_methods.splice(index, 1);
    }
};

// Retornar apenas os métodos de pagamento disponíveis (não selecionados em outros campos)
const getAvailablePaymentMethods = (currentIndex: number) => {
    if (!form.value.payment_methods) {
        return paymentMethod;
    }
    
    // Obter todos os métodos já selecionados em outros campos (exceto o do índice atual)
    const selectedMethods = form.value.payment_methods
        .map((method: string, index: number) => index !== currentIndex && method ? method.trim() : null)
        .filter((method: string | null) => method && method !== '');
    
    // Retornar apenas os métodos que não foram selecionados em outros campos
    // Se o campo atual já tem um valor selecionado, ele também deve aparecer na lista
    return paymentMethod.filter((option: any) => {
        // Sempre incluir o método atualmente selecionado neste campo
        const currentValue = form.value.payment_methods[currentIndex];
        if (currentValue && option.value === currentValue.trim()) {
            return true;
        }
        // Para outros métodos, verificar se não estão selecionados em outros campos
        return !selectedMethods.includes(option.value);
    });
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

    <ModalComponent
        :show="showModalFiles" 
        cancelText="Fechar"
        actionText="Novo arquivo"
        :title-header="'Arquivos de ' + (currentClientItem?.name || 'Cliente')" 
        :showSubmitBtn="false"
        @close-modal="handleCloseModalFiles"
        :showCustomAction="false"
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
                    <AutocompleteComponent 
                        v-model="statusTitle"
                        :suggestions="statusTitlesList"
                        placeholder="Digite ou selecione um status"
                        :showAllSuggestions="true"
                    />
                    <!-- Debug: {{ statusTitlesList.length }} status disponíveis -->
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
                    <money3 class="form-control"v-model="form.process_value" v-bind="moneyConfig"></money3>
                </FormGroupComponent>
            </div>
            <div class="col-6">
                <FormGroupComponent>
                    <LabelComponent text="Forma de pagamento" />
                    <div v-for="(method, index) in form.payment_methods" :key="index" class="mb-2">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="flex-grow-1">
                                <SelectComponent 
                                    v-model="form.payment_methods[index]" 
                                    :options="getAvailablePaymentMethods(index)"
                                    :placeholder="`Forma de pagamento ${index + 1}`"
                                />
                            </div>
                            <button 
                                v-if="index > 0"
                                type="button" 
                                class="btn btn-sm btn-outline-danger"
                                @click="removePaymentMethod(index)"
                                style="height: 38px; width: 38px; padding: 0; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; line-height: 1;"
                                title="Remover forma de pagamento"
                            >
                                ×
                            </button>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        class="btn btn-sm btn-outline-primary mt-2"
                        @click="addPaymentMethod"
                        style="padding: 0.375rem 0.75rem; font-size: 0.875rem;"
                    >
                        <i class="bi bi-plus-circle me-1"></i>
                        Adicionar forma de pagamento
                    </button>
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
                    :suggestions="agenciesList"
                    :placeholder="field.label"
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
            <template v-for="status in currentItem.status">
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
                <AutocompleteComponent 
                    v-model="statusTitle"
                    :suggestions="statusTitlesList"
                    placeholder="Digite ou selecione um status"
                    :showAllSuggestions="true"
                />
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