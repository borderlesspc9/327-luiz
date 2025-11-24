import { defineStore } from 'pinia';
import { index, show, store, update, destroy, refresh, pdf } from '@/utils/generalAPI';
import type { Pagination } from '@/interfaces/pagination/pagination.interface';
import type { Process } from '@/interfaces/process.interface';
import { useToast } from "vue-toastification";
import { io, Socket } from 'socket.io-client';

const toast = useToast();
const endpoint = {
    index: '/processes',
    withSlug: '/processes/slug'
}

//pegar a data de hoje
const filter = {
    deadline_date: new Date().toISOString().split('T')[0]
}

let socket: Socket | null = null;

export const useProcessStore = defineStore({
    id: 'process',
    state: () => ({
        processes: {} as Pagination,
        process: undefined as Process,
        totalToday: 0,
        uniqueAgencies: [] as string[],
    }),
    getters: {
        getProcesses: (state) => state.processes,
        getProcess: (state) => state.process,
        getTotalToday: (state) => state.totalToday,
        getUniqueAgencies: (state) => state.uniqueAgencies,
    },
    actions: {
        async index(params: any) {
            const result = await index(endpoint.index, params);
            this.processes = result.data;
            this.setTotalToday();
        },
        async fetchUniqueAgencies() {
            try {
                const result = await index(`${endpoint.index}/agencies/unique`, {});
                this.uniqueAgencies = result.data || [];
                return this.uniqueAgencies;
            } catch (error) {
                console.error('Error fetching unique agencies:', error);
                return [];
            }
        },
        async show(slug: string) {
            this.process = await show(endpoint.withSlug.replace('slug', slug));
        },
        async store(data: object) {
            await store(endpoint.index, data);
            this.index({ without_pagination: 0 });
        },
        async update(slug: string, data: object) {
            await update(endpoint.withSlug.replace('slug', slug), data);
            this.index({ without_pagination: 0 });
        },
        async destroy(slug: string) {
            await destroy(endpoint.withSlug.replace('slug', slug));
            this.index({ without_pagination: 0 });
        },
        async refresh(slug: string, data: object) {
            await refresh(endpoint.withSlug.replace('slug', slug), data);
            this.index({ without_pagination: 0 });
        },
        async setTotalToday() {
            const result = await index(endpoint.index, { without_pagination: 1, filter: filter });
            this.totalToday = result.data.length;
        },
        async pdf(payload: any, slug: string) {
            await pdf(`${endpoint.withSlug.replace('slug', slug)}/pdf`, payload);
        },

        // socket.io
        connectSocketIO() {
            if (socket && socket.connected) {
                return;
            }

            const socketUrl = import.meta.env.VITE_SOCKET_URL
            socket = io(socketUrl, {
                transports: ['websocket', 'polling'],
                secure: true,
            });

            socket.on('connect', () => {
                console.log('Connected to socket.io');
            });

            socket.on('proccess', (data: any) => {
                const user = JSON.parse(localStorage.getItem('user') as string);
                
                if (user.id === data.process.user_id) {
                    return;
                }
                
                toast.info('Lista de processos atualizada!');

                this.processes = data.processes;
                this.setTotalToday();
            });

            socket.on('error', (error: any) => {
                console.log('Error', error);
            });

            socket.on('connect_error', (error: any) => {
                console.log('Connect Error', error);
            });

            socket.on('disconnect', () => {
                console.log('Disconnected from socket.io');
            });
        }
    }
});