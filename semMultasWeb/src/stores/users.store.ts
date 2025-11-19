import { defineStore } from 'pinia'
import { index, show, store, update, destroy } from '@/utils/generalAPI'
import type { Pagination } from '@/interfaces/pagination/pagination.interface'
import type { User } from '@/interfaces/user.interface'
import type { RequestParams } from '@/interfaces/request-params.interface';

const endpoint = {
    index: '/users',
    withSlug: '/users/id',
}

export const useUserStore = defineStore({
    id: 'user',
    state: () => ({
        users: {} as Pagination,
        user: undefined as User,
    }),

    getters: {
        getUsers: (state) => state.users,
        getUser: (state) => state.user,
    },

    actions: {
        async index(params: RequestParams) {
            const result = await index(endpoint.index, params);
            this.users = result.data
        },
        async show(id: string) {
            this.user = await show(endpoint.withSlug.replace('id', id));
        },
        async store(data: string) {
            await store(endpoint.index, data);
            this.index();
        },
        async update(id: string, data: string) {
            await update(endpoint.withSlug.replace('id', id), data);
            this.index();
        },
        async destroy(id: string) {
            await destroy(endpoint.withSlug.replace('id', id));
            this.index();
        },
    },

})