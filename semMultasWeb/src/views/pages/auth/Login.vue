<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import type { LoginCredentials } from '@/interfaces/auth.interface';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import FormGroupComponent from '@/components/form/FormGroupComponent.vue';
import LabelComponent from '@/components/form/LabelComponent.vue';
import InputComponent from '@/components/form/InputComponent.vue';
import IconComponent from '@/components/IconComponent.vue';
import { LoadingComponent } from '@/utils/components';
const logoSrc = new URL('@/assets/img/sem-multa.png', import.meta.url).href;


const router = useRouter();
const authStore = useAuthStore();

const form = ref<LoginCredentials>({
    email: '',
    password: ''
});

const isLoading = ref(false);

const login = async () => {
    isLoading.value = true;
    const login = await authStore.login(form.value);
    isLoading.value = false;
    if(login)
        router.push({ name: 'process' });
}

</script>

<template>   

    <IconComponent name="wave"/>
    <div class="content-center">
        <div class="img-container">
            <img :src="logoSrc" alt="logo-lg">
        </div>
        <div class="login">
            <div class="welcome">
                <span>Bem vindo de volta!</span>
            </div>

            <div class="simple-text">
                <span>Faça login para continuar no Sistema</span>
            </div>

            <div class="body-login">
                <FormGroupComponent>
                    <LabelComponent text="Email" />
                    <InputComponent v-model="form.email" placeholder="Email" type="text" @keypress.enter="login"/>
                </FormGroupComponent>
                <FormGroupComponent>
                    <LabelComponent text="Senha" />
                    <InputComponent v-model="form.password" placeholder="Password" type="password" @keypress.enter="login"/>
                </FormGroupComponent>
                <button type="submit" :class="{ 'btn-submit-centered': isLoading}" @click="login" :disabled="isLoading">
                    <div v-if="isLoading" class="loading-overlay">
                        <div class="spinner"></div>
                    </div>
                    <span v-else>Login</span>
                </button>
            </div>


        </div>
    </div>

</template>

<style scoped>
    .btn-submit-centered{
        display: flex!important;
        align-items: center;
        justify-content: center;
    }
    
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 2s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
  </style>