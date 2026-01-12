import axios from 'axios';
import type { AxiosResponse } from 'axios';
import type { LoginCredentials, LoginResponse, RegisterCredentials, RegisterResponse } from '@/interfaces/auth.interface';

const API_URL = import.meta.env.VITE_API_URL;

const endpoint = {
    login: '/login',
    register: '/register'
}

// Criar instância do axios com token
const axiosInstance = axios.create({
    baseURL: API_URL,
    headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
    }
});

class AuthService {
    static login(credentials: LoginCredentials): Promise<LoginResponse> {
        return new Promise((resolve, reject) => {
            axiosInstance.post<LoginResponse>(endpoint.login, credentials)
                .then((response: AxiosResponse<LoginResponse>) => {
                    resolve(response.data);
                })
                .catch((error: any) => {
                    reject(error);
                });
        });
    }

    static register(credentials: RegisterCredentials): Promise<RegisterResponse> {
        return new Promise((resolve, reject) => {
            // Criar instância sem token para registro (rota pública)
            const registerAxios = axios.create({
                baseURL: API_URL,
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            registerAxios.post<RegisterResponse>(endpoint.register, credentials)
                .then((response: AxiosResponse<RegisterResponse>) => {
                    resolve(response.data);
                })
                .catch((error: any) => {
                    reject(error);
                });
        });
    }

    static logout(): void {
        localStorage.removeItem('token');
    }
}

export default AuthService;