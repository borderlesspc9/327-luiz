import { defineStore } from "pinia";
import { index } from "@/utils/generalAPI";
import type { Pagination } from "@/interfaces/pagination/pagination.interface";
import type { RequestParams } from '@/interfaces/request-params.interface';
import type { Permission } from '@/interfaces/permissions.interface';

const endpoint = {
    index: '/permissions',
}

export const usePermissionStore = defineStore({
    id: 'permission',
    state: () => ({
        permissions: {} as Pagination,
    }),

    getters: {
        getPermissions: (state) => state.permissions,
    },

    actions: {
        async index(params: RequestParams) {
            const result = await index(endpoint.index, params)
            this.permissions = result.data
        },
    }
})