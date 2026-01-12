import { defineStore } from 'pinia';
import { useToast } from 'vue-toastification';
import AuthService from '@/services/auth.service';
import type { LoginCredentials, LoginResponse, RegisterCredentials, RegisterResponse } from '@/interfaces/auth.interface';
import { useCache } from '@/utils/cache';

const cache = useCache();

const toast = useToast();

export const useAuthStore = defineStore('auth',{
    
    state: () => ({
        token: localStorage.getItem('token') || null,
        user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user') as string) : []
    }),

    getters: {
        isAuthenticated: (state) => !!localStorage.getItem('token') || !!state.token,
        getUser: (state) => state.user
    },

    actions: {

        async login(credentials: LoginCredentials) {
            try {
                const response: LoginResponse = await AuthService.login(credentials);
                
                if (!response || !response.token) {
                    toast.error('Resposta inválida do servidor');
                    return false;
                }
                
                cache.storeToken(response.token);
                cache.storeItem('user', JSON.stringify(response.user));
                this.token = response.token;
                this.user = response.user;
                toast.success('Login efetuado com sucesso!');

                return true;
                
            } catch (error: any) {
                console.error('Login error:', error);
                const message = error?.response?.data?.message || 'Erro ao efetuar login!';
                toast.error(message);
                return false;
            }
        },

        async register(credentials: RegisterCredentials) {
            try {
                const response: RegisterResponse = await AuthService.register(credentials);
                
                if (!response || !response.message) {
                    toast.error('Resposta inválida do servidor');
                    return false;
                }
                
                toast.success('Conta criada com sucesso! Faça login para continuar.');
                return true;
                
            } catch (error: any) {
                console.error('Register error:', error);
                const message = error?.response?.data?.message || error?.response?.data?.error || 'Erro ao criar conta!';
                
                // Se for erro de validação, mostrar erros específicos
                if (error?.response?.data?.email) {
                    toast.error(error.response.data.email[0]);
                } else if (error?.response?.data?.name) {
                    toast.error(error.response.data.name[0]);
                } else if (error?.response?.data?.password) {
                    toast.error(error.response.data.password[0]);
                } else {
                    toast.error(message);
                }
                return false;
            }
        },

        logout(): void {
            AuthService.logout();
            this.token = null;
            this.user = [];
            toast.success('Logout efetuado com sucesso!');
        }
    }

})