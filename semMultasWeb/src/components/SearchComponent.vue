<script setup lang="ts">
import { defineProps, defineEmits, ref } from 'vue';
import IconComponent from '@/components/IconComponent.vue';

const emit = defineEmits<{
  (e: 'update-search', value: string): void;
}>();

let searchTimeout = ref<NodeJS.Timeout | null>(null);

const handleInput = (event: Event) => {
    const inputValue = (event.target as HTMLInputElement).value;

    if (searchTimeout.value) clearTimeout(searchTimeout.value);

    searchTimeout.value = setTimeout(() => {
        emitUpdateSearch(inputValue);
    }, 800);
};

const emitUpdateSearch = (value: string) => {
    emit('update-search', value);
};

</script>

<template>
    <div class="title-search">
            <div class="input-group">
                <button class="btn btn-outline-secondary" type="button" id="button-addon1">
                    <IconComponent name="search-2" />
                </button>
                <input 
                    type="text" 
                    class="form-control" 
                    placeholder="Search..." 
                    aria-label="Example text with button addon" 
                    aria-describedby="button-addon1"
                    @input="handleInput"
                >
            </div>
        </div>
</template>