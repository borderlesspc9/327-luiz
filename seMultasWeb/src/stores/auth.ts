import { defineStore } from 'pinia';
import { useToast } from 'vue-toastification';
import AuthService from '@/services/auth.service';
import type { LoginCredentials, LoginResponse } from '@/interfaces/auth.interface';
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
                cache.storeToken(response.token);
                cache.storeItem('user', JSON.stringify(response.user));
                this.token = response.token;
                this.user = response.user;
                toast.success('Login efetuado com sucesso!');

                return true;
                
            } catch (error) {
                toast.error('Erro ao efetuar login!');
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