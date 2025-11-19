import { defineStore } from "pinia";
import { index, show, store, update, destroy } from "@/utils/generalAPI";
import type { Pagination } from "@/interfaces/pagination/pagination.interface";
import type { RequestParams } from '@/interfaces/request-params.interface';
import type { Role } from '@/interfaces/roles.interface';

const endpoint = {
    index: '/roles',
    withSlug: '/roles/slug',
}

export const useRoleStore = defineStore({
    id: 'role',
    state: () => ({
        roles: {} as Pagination,
        role: undefined as Role,
    }),

    getters: {
        getRoles: (state) => state.roles,
        getRole: (state) => state.role
    },

    actions: {
        async index(params: RequestParams) {
            const result = await index(endpoint.index, params)
            this.roles = result.data
        },
        async show(slug: string) {
            this.role = await show(endpoint.withSlug.replace('slug', slug))
        },
        async store(data: string) {
            await store(endpoint.index, data)
        },
        async update(slug: string, data: string) {
            await update(endpoint.withSlug.replace('slug', slug), data)
        },
        async destroy(slug: string) {
            await destroy(endpoint.withSlug.replace('slug', slug))
        },
    }
})
