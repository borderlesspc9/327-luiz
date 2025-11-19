<script setup lang="ts">
import ButtonComponent from './ButtonComponent.vue';
import { ref, defineProps, defineEmits, computed } from 'vue';
import type { Action } from '@/types/ActionType';
import { useRoute } from 'vue-router';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    actions: {
        type: Array<Action>,
        default: () => [],
    },
});

const emit = defineEmits(['row-click', 'page-change', 'per-page-change']);
const route = useRoute();

const handleRowClick = (item) => {
  emit('row-click', item);
};

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

const getBackgroundColor = (status: Array<any>): string => {
    const activeStatus = status.find(status => status.pivot.is_active === 1);
    return activeStatus ? activeStatus.color : '';
};

const getTextColor = (status: Array<any>): string => {
    const activeStatus = status.find(status => status.pivot.is_active === 1);
    return activeStatus ? activeStatus.color_text : '';
};

const copyToClipboard = (value) => {
    const el = document.createElement('textarea');
    el.value = value;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    toast.success('Texto copiado para a área de transferência');
};

</script>

<template>

    <div class="table-responsive table-default">
        <table class="table">
            <thead>
                <tr>
                    <template  v-for="(value, key) in items[0]" :key="key">
                    <th v-if="key != 'slug' && key != 'id' && key != 'status'">{{ key }}</th>
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
                            :style="isProcessRoute() ? { backgroundColor: getBackgroundColor(item.status), color: getTextColor(item.status) } : {}"
                            :class="{ 'deadline': key === 'Data limite' && isNearDeadline(value)}"
                            :title="key === 'Data limite' ? getTitle(value) : ''"
                            v-if="key != 'slug' && key != 'id' && key != 'status'"
                        >
                            <template v-if="key === 'color' || key === 'color_text'">
                                <div :style="{ backgroundColor: value, width: '20px', height: '20px', borderRadius: '5px', boxShadow: '0px 0px 0px 1px #ccc' }"></div>
                            </template>
                            <template v-else>
                                {{ value }}
                                <ButtonComponent icon="copy" @click.stop="copyToClipboard(value)"></ButtonComponent>
                            </template>
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
</template>
