import { defineStore } from 'pinia'
import { index } from '@/utils/generalAPI'

const endpoint = {
    index: '/process-fields',
}

const params = {
    without_pagination: 0
}

export const useProcessFieldsStore = defineStore({
    id: 'processFields',
    state: () => ({
        processFields: [],
    }),

    getters: {
        getProcessFields: (state) => state.processFields,
    },

    actions: {
        async index() {
            const result = await index(endpoint.index, params)
            this.processFields = result
        },
    }
})