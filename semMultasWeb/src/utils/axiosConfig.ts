import axios from "axios";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();

const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    timeout: 30000, // 30 segundos - evita requisições travadas
});

axiosInstance.interceptors.request.use(
    (config) => {
        const token = authStore.token;
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default axiosInstance;