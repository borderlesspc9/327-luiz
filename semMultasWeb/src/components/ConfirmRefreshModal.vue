<script setup lang="ts">
import { ModalComponent } from '@/utils/components';
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    showModal: Boolean,
});

const emits = defineEmits(['closeModal', 'confirmRefresh', 'cancelRefresh']);

const handleCloseModal = () => {
    emits('closeModal');
};

const handleCancel = () => {
    emits('cancelRefresh');
    handleCloseModal();
};

const handleConfirm = () => {
    emits('confirmRefresh');
    handleCloseModal();
};
</script>

<template>
    <ModalComponent
        sizeClass="modal-m"
        submitText="Confirmar"
        cancelText="Cancelar"
        submitBtnClass="btn-info" 
        :show="showModal"
        @closeModal="handleCloseModal()"
        @submit="handleConfirm"
    >

        <div class="modal-body">
            <svg width="80px" height="80px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M21 12C21 16.9706 16.9706 21 12 21C9.69494 21 7.59227 20.1334 6 18.7083L3 16M3 12C3 7.02944 7.02944 3 12 3C14.3051 3 16.4077 3.86656 18 5.29168L21 8M3 21V16M3 16H8M21 3V8M21 8H16" stroke="#172b4d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>            <div class="mt-2 text-alert-delete">
                <span>Tem certeza que deseja adiar a data?</span>
                <span>Essa ação não poderá ser revertida.</span>
            </div>
        </div>
    </ModalComponent>
</template>