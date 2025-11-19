import { defineStore } from 'pinia'
import axiosInstance from '@/utils/axiosConfig'
import { useToast } from "vue-toastification";

const endpoint = {
    logout: '/logout',
}

const toast = useToast();

export const useLogoutStore = defineStore({
    id: 'logout',
    state: () => ({}),
    actions: {
        async logout() {
            try{
                await axiosInstance.get(endpoint.logout);
                localStorage.removeItem('user');
                localStorage.removeItem('token');
                location.href = '/login';
            }catch(error){
                toast.error(error.response.data.message);
            }
        },
    },
})