import { defineStore } from 'pinia'
import { index, show, store, update, destroy } from '@/utils/generalAPI'
import type { Pagination } from '@/interfaces/pagination/pagination.interface'
import type { Service } from '@/interfaces/service.interface'
import type { RequestParams } from '@/interfaces/request-params.interface';

const endpoint = {
    index: '/services',
    withSlug: '/services/slug',
}

export const useServiceStore = defineStore({
    id: 'service',
    state: () => ({
        services: {} as Pagination,
        service: undefined as Service,
    }),

    getters: {
        getServices: (state) => state.services,
        getService: (state) => state.service
    },

    actions: {
        async index(params: RequestParams) {
            const result = await index(endpoint.index, params)
            this.services = result.data
        },
        async show(slug: string) {
            this.service = await show(endpoint.withSlug.replace('slug', slug))
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