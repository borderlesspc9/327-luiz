import { defineStore } from 'pinia'
import { importData } from '@/utils/generalAPI'

const endpoint = {
    index: '/import/all',
}

export const useImportStore = defineStore({
    id: 'import',
    state: () => ({
        import: undefined as any
    }),
    getters: {
        getImport: (state) => state.import
    },
    actions: {
        async fetchImport(data: FormData)
        {
            this.import = await importData(endpoint.index, data);
        }
    }
})