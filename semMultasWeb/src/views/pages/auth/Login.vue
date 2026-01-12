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
import { useAcl } from '@/utils/acl';
const logoSrc = new URL('@/assets/img/sem-multa.png', import.meta.url).href;


const router = useRouter();
const authStore = useAuthStore();
const { hasPermissionTo } = useAcl();

const form = ref<LoginCredentials>({
    email: '',
    password: ''
});

const isLoading = ref(false);

const login = async () => {
    isLoading.value = true;
    const loginResult = await authStore.login(form.value);
    isLoading.value = false;
    if(loginResult) {
        // Aguardar um pouco para garantir que o token e user foram salvos no cache
        await new Promise(resolve => setTimeout(resolve, 300));
        
        // Tentar redirecionar para uma página baseada nas permissões do usuário
        // Ordem de prioridade: process > clients > services > dashboard
        let redirectRoute = 'dashboard'; // Rota padrão (sem permissão necessária)
        
        if (hasPermissionTo('Read process')) {
            redirectRoute = 'process';
        } else if (hasPermissionTo('Read client')) {
            redirectRoute = 'clients';
        } else if (hasPermissionTo('Read service')) {
            redirectRoute = 'services';
        }
        
        try {
            await router.push({ name: redirectRoute });
        } catch (error) {
            // Se falhar, usar path direto
            console.error('Erro ao redirecionar:', error);
            const pathMap: Record<string, string> = {
                'dashboard': '/painel',
                'process': '/painel/process',
                'clients': '/painel/clients',
                'services': '/painel/services'
            };
            window.location.href = pathMap[redirectRoute] || '/painel';
        }
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
                
                <div class="register-link">
                    <span>Não tem uma conta? </span>
                    <a @click="router.push({ name: 'register' })" style="cursor: pointer; color: #3498db; text-decoration: underline;">Cadastre-se</a>
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