import { defineStore } from 'pinia'
import { index } from '@/utils/generalAPI'
import type { RequestParams } from '@/interfaces/request-params.interface';

const endpoint = {
    index: '/search',
}

export const useSearchStore = defineStore({
    id: 'search',
    state: () => ({
        searchResults: {},
    }),
    getters: {
        getSearchResults: (state) => state.searchResults,
    },
    actions: {
        async search(params: RequestParams) {
            try {
                const result = await index(endpoint.index, params);
                this.searchResults = result;
            } catch (error) {
                console.error('Search error:', error);
            }
        }
    },
})