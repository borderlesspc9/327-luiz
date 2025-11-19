<script setup>
import { defineProps, defineEmits } from 'vue';

defineProps({
    id: String,
    // title: String,
    show: Boolean,
    mClass: String,
    sizeClass: String,
    titleHeader: String,
    cancelText: {
        type: String,
        default: 'Cancelar'
    },
    submitText: {
        type: String,
        default: 'Enviar'
    },
    cancelBtnClass: {
        type: String,
        default: 'btn-secondary'
    },
    submitBtnClass: {
        type: String,
        default: 'btn-primary'
    },
    showSubmitBtn: {
        type: Boolean,
        default: true
    },
    actionText: {
        type: String,
        default: 'Confirmar'
    },
    showCustomAction: {
        type: Boolean,
        default: false
    }
});

const emits = defineEmits(['closeModal', 'submit', 'customAction']);

const close = () => {
    emits('closeModal');
};

const handleSubmit = () => {
    emits('submit');
    close();
};

const handleCustomAction = () => {
    emits('customAction');
};

</script>

<template>
    <div class="overlay" :class="{show, mClass}">
        <div class="modal fade" :class="[{ show, 'hide': !show}, sizeClass ]" :id="id" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{titleHeader}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="close"></button>
                    </div>
                    <div class="modal-body">
                        <slot></slot>
                    </div>
                    <div class="modal-footer">
                        <button type="button" :class="['btn', cancelBtnClass]" data-bs-dismiss="modal" aria-label="Close" @click="close">{{ cancelText }}</button>
                        <button type="button" v-if="showSubmitBtn" :class="['btn', submitBtnClass]" @click="handleSubmit()">{{ submitText }}</button>
                        <button type="button" v-if="showCustomAction" :class="['btn', submitBtnClass]" @click="handleCustomAction()">{{ actionText }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
@keyframes slideIn {
    0% {
        transform: translateY(-100%);
        opacity: 0;
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    0% {
        transform: translateY(0);
        opacity: 1;
    }
    100% {
        transform: translateY(-100%);
        opacity: 0;
    }
}

.overlay {
    display: none;
    position: fixed;
    z-index: 1050;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
    opacity: 0;
    animation: opacity 0.3s;
    &.show {
        display: flex;
        opacity: 1;
    }
}

.modal {
    border-radius: 5px;
    padding: 20px;
    position: relative;

    .modal-dialog{
        max-width: 1200px!important;
    }
    .modal-content{
        border: none!important;

        .modal-header {
            background-color: #9155fd;
            color: #fff;

            .btn-close {
                filter: invert(1);
                opacity: 1;
                margin-left: 0;
            }
            gap: 10px;
        }
    }
    &.show {
        display: block;
        animation: slideIn 0.3s forwards;
    }
    &hide{
        animation: slideOut 0.3s forwards;
    }
    .btn-close {
        cursor: pointer;
    }

}

.modal-only-body{
    .modal-header,
    .modal-footer {
        display: none;
    }
    .modal{
        position: relative;
        padding: 0;
        border-radius: 8px;
        max-width: 600px;
        width: 100%;
        height: auto;
        .modal-body{
            margin: 0;
        }
    }
}
</style>