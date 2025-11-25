<template>
    <div class="autocomplete-container">
        <input 
            :type="type" 
            class="form-control" 
            :value="modelValue" 
            @input="handleInput(($event.target as HTMLInputElement)?.value)"
            @focus="showSuggestions = true"
            @blur="handleBlur"
            :placeholder="placeholder" 
            :required="required" 
            :disabled="disabled"
            autocomplete="off"
        />
        <ul v-if="showSuggestions && filteredSuggestions.length > 0" class="suggestions-list">
            <li 
                v-for="(suggestion, index) in filteredSuggestions" 
                :key="index"
                @mousedown.prevent="selectSuggestion(suggestion)"
                :class="{ 'highlighted': index === highlightedIndex }"
            >
                {{ suggestion }}
            </li>
        </ul>
    </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, computed, watch } from 'vue';

const props = defineProps({
    name: String,
    modelValue: String,
    placeholder: String,
    type: {
        type: String,
        default: 'text',
    },
    required: Boolean,
    disabled: Boolean,
    suggestions: {
        type: Array as () => string[],
        default: () => [],
    },
});

const emit = defineEmits(['update:modelValue', 'update:error']);

const showSuggestions = ref(false);
const highlightedIndex = ref(-1);

const filteredSuggestions = computed(() => {
    if (!props.modelValue || props.modelValue.trim() === '') {
        return props.suggestions;
    }
    const searchTerm = props.modelValue.toLowerCase();
    return props.suggestions.filter(suggestion => 
        suggestion.toLowerCase().includes(searchTerm)
    );
});

const handleInput = (value: string) => {
    emit('update:modelValue', value);
    showSuggestions.value = true;
    highlightedIndex.value = -1;
};

const selectSuggestion = (suggestion: string) => {
    emit('update:modelValue', suggestion);
    showSuggestions.value = false;
};

const handleBlur = () => {
    // Delay to allow click on suggestion to work
    setTimeout(() => {
        showSuggestions.value = false;
    }, 200);
};

watch(() => props.suggestions, () => {
    // Quando as sugestões mudam, manter showSuggestions se já estiver aberto
}, { deep: true });
</script>

<style scoped>
.autocomplete-container {
    position: relative;
    width: 100%;
}

.suggestions-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ccc;
    border-top: none;
    border-radius: 0 0 4px 4px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    list-style: none;
    padding: 0;
    margin: 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.suggestions-list li {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
}

.suggestions-list li:last-child {
    border-bottom: none;
}

.suggestions-list li:hover,
.suggestions-list li.highlighted {
    background-color: #f0f0f0;
}

.error-message {
    color: red;
}
</style>

