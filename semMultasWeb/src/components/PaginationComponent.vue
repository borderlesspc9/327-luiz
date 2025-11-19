<script setup lang="ts">
import { ref, defineProps, defineEmits, computed } from 'vue';
import { SelectComponent } from '@/utils/components';
import IconComponent from './IconComponent.vue';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    pagination: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['page-change', 'per-page-change']);

const localPerPage = ref(props.pagination.per_page || 10);

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

const filteredLinks = computed(() => {
    return props.pagination?.links?.filter(link => !link.label.includes('Previous') && !link.label.includes('Next')) ?? [];
});

const perPageList = [
    { value: 1, text: '1' },
    { value: 2, text: '2' },
    { value: 10, text: '10' },
    { value: 25, text: '25' },
    { value: 50, text: '50' },
    { value: 100, text: '100' },
];
</script>

<template>
    <div v-if="items.length > 0" class="pagination-table">
        <div class="btn-pagination-container">
            <button
                class="btn-page"
                :disabled="!props.pagination.prev_page_url" 
                @click="handlePageChange(pagination.prev_page_url)"
            >
                <IconComponent class="svg" name="arrow-left" />
            </button>

            <button
                v-for="link in filteredLinks"
                :key="link.label"
                :class="{ 'active': link.active }"
                @click="handlePageChange(link.url)"
                class="page-link"
            >
                <span v-html="link.label"></span>
            </button>

            <button
                class="btn-page"
                :disabled="!props.pagination.next_page_url" 
                @click="handlePageChange(pagination.next_page_url)"
            >
                <IconComponent class="svg" name="arrow-right" />
            </button>
        </div>
        <div class="form-row">
            <LabelComponent text="Itens por página"/>
            <SelectComponent type="number" :options="perPageList"v-model="localPerPage" min="1" @change="emitPerPageChange"/>
        </div>
    </div>
</template>