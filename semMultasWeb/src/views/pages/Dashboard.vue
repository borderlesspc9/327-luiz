<script setup lang="ts">
import type { Action } from '@/types/ActionType';
import { ref } from 'vue';
import { TableComponent, ModalComponent, CardTitleComponent, ContainerComponent, CardComponent } from '@/utils/components';
import ConfirmDeleteModalComponent from '@/components/ConfirmDeleteModalComponent.vue';

const actions: Action[] = [
    {
        name: 'edit',
        action: (item) => {
            showModal.value = true;
        },
        icon: 'edit',
        class: 'light blue',
    },
    {
        name: 'delete',
        action: (item) => {
            selectedItem.value = item;
            showDeleteModal.value = true;
        },
        icon: 'trash',
        class: 'light red',
    },
];

const dataTable = ref([
    {
        id: 1,
        name: 'John Doe',
        email: 'johndoe@email.com',
        phone: '123456789',
    },
    {
        id: 2,
        name: 'Jane Doe',
        email: 'janedooe@email.com',
        phone: '987654321',
    }
]);

const showDeleteModal = ref(false);
const showModal = ref(false);
const selectedItem = ref(null);

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    selectedItem.value = null;
};

const confirmDelete = () => {
    if (selectedItem.value) {
        dataTable.value = dataTable.value.filter(item => item.id !== selectedItem.value.id);
    }
    closeDeleteModal();
};

</script>

<template>
    <ModalComponent class="modal-only-body" id="teste" :show="showModal" @closeModal="showModal=false">
        <button class="close-modal" @click="showModal=false">X</button>
        <h3>Meu modal</h3>
    </ModalComponent>

    <ConfirmDeleteModalComponent 
        :show="showDeleteModal" 
        @closeModal="closeDeleteModal" 
        @confirmDelete="confirmDelete"
    />

    <CardTitleComponent title="Dashboard" />

    <ContainerComponent>
        <CardComponent>
            <TableComponent class="mt-5" :items="dataTable" :actions="actions" />
        </CardComponent>
    </ContainerComponent>
</template>