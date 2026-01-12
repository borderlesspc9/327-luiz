<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import type { RegisterCredentials } from '@/interfaces/auth.interface';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import FormGroupComponent from '@/components/form/FormGroupComponent.vue';
import LabelComponent from '@/components/form/LabelComponent.vue';
import InputComponent from '@/components/form/InputComponent.vue';
import IconComponent from '@/components/IconComponent.vue';
const logoSrc = new URL('@/assets/img/sem-multa.png', import.meta.url).href;


const router = useRouter();
const authStore = useAuthStore();

const form = ref<RegisterCredentials>({
    name: '',
    email: '',
    password: ''
});

const confirmPassword = ref('');
const isLoading = ref(false);

const register = async () => {
    if (form.value.password !== confirmPassword.value) {
        alert('As senhas não coincidem!');
        return;
    }

    if (form.value.password.length < 6) {
        alert('A senha deve ter no mínimo 6 caracteres!');
        return;
    }

    isLoading.value = true;
    const success = await authStore.register(form.value);
    isLoading.value = false;
    if(success) {
        setTimeout(() => {
            router.push({ name: 'login' });
        }, 1500);
    }
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
                <span>Criar nova conta</span>
            </div>

            <div class="simple-text">
                <span>Preencha os dados para se cadastrar</span>
            </div>

            <div class="body-login">
                <FormGroupComponent>
                    <LabelComponent text="Nome" />
                    <InputComponent v-model="form.name" placeholder="Nome completo" type="text" @keypress.enter="register"/>
                </FormGroupComponent>
                <FormGroupComponent>
                    <LabelComponent text="Email" />
                    <InputComponent v-model="form.email" placeholder="Email" type="email" @keypress.enter="register"/>
                </FormGroupComponent>
                <FormGroupComponent>
                    <LabelComponent text="Senha" />
                    <InputComponent v-model="form.password" placeholder="Senha (mínimo 6 caracteres)" type="password" @keypress.enter="register"/>
                </FormGroupComponent>
                <FormGroupComponent>
                    <LabelComponent text="Confirmar Senha" />
                    <InputComponent v-model="confirmPassword" placeholder="Confirme sua senha" type="password" @keypress.enter="register"/>
                </FormGroupComponent>
                <button type="submit" :class="{ 'btn-submit-centered': isLoading}" @click="register" :disabled="isLoading">
                    <div v-if="isLoading" class="loading-overlay">
                        <div class="spinner"></div>
                    </div>
                    <span v-else>Cadastrar</span>
                </button>
                
                <div class="register-link">
                    <span>Já tem uma conta? </span>
                    <a @click="router.push({ name: 'login' })" style="cursor: pointer; color: #3498db; text-decoration: underline;">Fazer login</a>
                </div>
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

    .register-link {
        margin-top: 15px;
        text-align: center;
        font-size: 14px;
    }
  </style>
