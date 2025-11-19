import { defineStore } from 'pinia';
import { exportData } from '@/utils/generalAPI';

const endpoint = {
    index: '/export/all',
}

export const useExportStore = defineStore({
    id: 'export',
    state: () => ({
        export: undefined as any
    }),
    getters: {
        getExport: (state) => state.export
    },
    actions: {
        async fetchExport(params: any)
        {
            this.export = await exportData(endpoint.index);
        }
    }
});