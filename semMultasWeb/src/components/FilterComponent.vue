<script setup lang="ts">
import { ref, defineEmits } from 'vue';
import IconComponent from '@/components/IconComponent.vue';
import ModalComponent from './ModalComponent.vue';

const emit = defineEmits(['applyFilter']);

const modalVisible = ref(false);

const openModal = () => {
    modalVisible.value = true;
};

const closeModal = () => {
    modalVisible.value = false;
};

const handleSubmit = () => {
    emit('applyFilter');
    closeModal();
};
</script>

<template>
    <div class="title-search">
        <div class="input-group">
            <button class="btn btn-outline-secondary" type="button" id="button-addon1" @click="openModal">
                <IconComponent name="filter" />
            </button>
        </div>

        <ModalComponent
            submitText="Filtrar"
            sizeClass="modal-small"
            :show="modalVisible"
            cancelText="Fechar"
            @closeModal="closeModal"
            @submit="handleSubmit"
            titleHeader="Filtros"
        >
            <div>
                <slot name="form"></slot>
            </div>
        </ModalComponent>
    </div>
</template>
