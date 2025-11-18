import axiosInstance from "./axiosConfig";
import { useToast } from "vue-toastification";
import type { RequestParams } from '@/interfaces/request-params.interface';

const toast = useToast();

export const index = async (url: string, params: RequestParams) => {
    try {
        const response = await axiosInstance.get(url, { params });
        return response.data;
    } catch (error: any) {
        toast.error(error.response.data.message);
    }
}

export const show = async (url: string) => {
    try {
        const response = await axiosInstance.get(`${url}`);
        return response.data;
    } catch (error: any) {
        toast.error(error.response.data.message);
    }
}

export const store = async (url: string, data: object) => {
    try {
        const response = await axiosInstance.post(url, data);
        toast.success(response.data.message);
    } catch (error: any) {
        toast.error(error.response.data.message);
    }
}

export const update = async (url: string, data: object) => {
    try {
        const response = await axiosInstance.put(url, data);
        toast.success(response.data.message);
    } catch (error: any) {
        toast.error(error.response.data.message);
    }
}

export const destroy = async (url: string, params: any) => {
    try {
        const response = await axiosInstance.delete(url, params);
        toast.success(response.data.message);
    } catch (error: any) {
        toast.error(error.response.data.message);
    }
}

export const refresh = async (url: string, data: string) => {
    try {
        const response = await axiosInstance.post(url, data);
        toast.success(response.data.message);
    } catch (error: any) {
        toast.error(error.response.data.message);
    }
}

export const exportData = async (url: string) => {
    try {
        const response = await axiosInstance.get(url, { responseType: 'blob' });
        const blob = new Blob([response.data], { type: response.data.type });
        const downloadUrl = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = downloadUrl;
        a.download = `download-${Date.now()}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(downloadUrl);
        toast.success('File downloaded successfully');
    } catch (error: any) {
        toast.error(error.response.data.message);
    }
}

export const importData = async (url: string, data: FormData) => {
    try {
        const response = await axiosInstance.post(url, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        toast.success(response.data.message);
        return response.data;
    } catch (error: any) {
        toast.error(error.response.data.message);
    }
}

export const pdf = async (url: string, payload: any) => {
    try {
        const response = await axiosInstance.post(url, payload, { responseType: 'blob' });
        const blob = new Blob([response.data], { type: response.data.type });
        const downloadUrl = URL.createObjectURL(blob);

        // Abrir o PDF em uma nova aba
        window.open(downloadUrl, '_blank');

        // Revogar o URL do objeto após um tempo para liberar memória
        setTimeout(() => {
            URL.revokeObjectURL(downloadUrl);
        }, 100);

        toast.success('File opened successfully');
    } catch (error: any) {
        toast.error(error.response.data.message);
    }
}  