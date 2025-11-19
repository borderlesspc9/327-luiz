<script setup lang="ts">
import ButtonComponent from './ButtonComponent.vue';
import { ref, defineProps, defineEmits, computed } from 'vue';
import type { Action } from '@/types/ActionType';
import { useRoute } from 'vue-router';
import IconComponent from './IconComponent.vue';
import InputComponent from './form/InputComponent.vue';
import LabelComponent from './form/LabelComponent.vue';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    pagination: {
        type: Object,
        default: () => ({}),
    },
    actions: {
        type: Array<Action>,
        default: () => [],
    },
});

const emit = defineEmits(['row-click', 'page-change', 'per-page-change']);
const route = useRoute();
const localPerPage = ref(props.pagination.per_page || 10);

const handleRowClick = (item) => {
  emit('row-click', item);
};

const emitPerPageChange = () => {
    if (localPerPage.value < 1) {
        localPerPage.value = 1;
    }
    emit('per-page-change', localPerPage.value);
};

const handlePageChange = (page: number | null) => {
    if (page) {
        emit('page-change', page);
    }
};

const pages = computed(() => {
    const totalPages = props.pagination.last_page;
    const pagesArray = [];
    for (let i = 1; i <= totalPages; i++) {
        pagesArray.push(i);
    }
    return pagesArray;
});

const isNearDeadline = (dateString: string): boolean => {
    if (!dateString) return false;
        const today = new Date();
        const deadlineDate = parseDate(dateString);
    if (!deadlineDate) return false;
        const timeDiff = deadlineDate.getTime() - today.getTime();
        const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
        return daysDiff <= 15;
};

const parseDate = (dateString: string): Date | null => {
    if (!dateString) return null;
        const parts = dateString.split('/');
    if (parts.length !== 3) return null;
        const [day, month, year] = parts;
        const formattedDate = `${year}-${month}-${day}`;
        const date = new Date(formattedDate);
    return isNaN(date.getTime()) ? null : date;
};

const getTitle = (dateString: string): string => {
    if (isNearDeadline(dateString)) {
        return 'A data está a menos de 15 dias do prazo!';
    }
    return '';
};

const isProcessRoute = () => {
    return route.name === 'process';
};

// const getBackgroundColor = (status: Array<any>): string => {
//     const activeStatus = status.find(status => status.pivot.is_active === 1);
//     return activeStatus ? activeStatus.color : '';
// };

const getTextColor = (status: Array<any>): string => {
    const activeStatus = status.find(status => status.pivot.is_active === 1);
    return activeStatus ? activeStatus.color_text : '';
};

const filteredLinks = computed(() => {
    return props.pagination?.links?.filter(link => !link.label.includes('Previous') && !link.label.includes('Next')) ?? [];
});

</script>

<template>
    <!-- <div class="table-container"> -->

    <div class="table-responsive table-default">
        <table class="table">
            <thead>
                <tr>
                    <template  v-for="(value, key) in items[0]" :key="key">
                    <th v-if="key != 'slug' && key != 'id'">{{ key }}</th>
                    </template>
                    <th v-if="actions && actions.length > 0">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr :class="{ 'line-click': isProcessRoute() }" v-for="(item, index) in items" 
                :key="index" @click="handleRowClick(item)" v-if="items.length > 0" 
                >
                    <template v-for="(value, key) in item" :key="key">
                    <td 
                        :class="{ 'deadline': key === 'deadline_date' && isNearDeadline(value)}"
                        :title="key === 'deadline_date' ? getTitle(value) : ''"
                        v-if="key != 'slug' && key != 'id'"
                    >
                        {{ value }}
                    </td>
                    </template>
                    <td class="td-actions" v-if="actions && actions.length > 0">
                        <template v-for="(action, key) in actions" >
                            <ButtonComponent
                                v-if="action.hasPermission !== undefined ? action.hasPermission : true"
                                :key="key" 
                                :buttonClass="action.class"
                                :icon="action.icon" 
                                @click.stop="action.action(item)"
                            />
                        </template>
                    </td>
                </tr>
                <tr v-else>
                    <td colspan="100%">
                        <div class="alert alert-warning text-center">Nenhum registro encontrado</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- </div> -->
</template>
