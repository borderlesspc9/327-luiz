import { defineStore } from 'pinia'
import { index } from '@/utils/generalAPI'

const endpoint = {
    index: '/sellers',
}

export const useSellerStore = defineStore({
    id: 'seller',
    state: () => ({
        sellers: {},
    }),
    getters: {
        getSellers: (state) => state.sellers,
    },
    actions: {
        async index(params) {
            const result = await index(endpoint.index, params);
            this.sellers = result
        },
    },
})