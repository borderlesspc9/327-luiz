<script setup lang="ts">
import { ref, defineProps, defineEmits, computed, watch } from 'vue';
import IconComponent from './IconComponent.vue';

// Definir props para receber a lista de opções e itens selecionados inicialmente
const props = defineProps<{
  options: { text: string, value: string }[];
  modelValue: string[];
  placeholder: string;
}>();

// Definir emits para emitir eventos para o componente pai
const emit = defineEmits(['update:modelValue']);

// Array reativo para armazenar os itens selecionados
const selectedItems = ref<string[]>([...props.modelValue]);

// Array reativo para armazenar a consulta de pesquisa
const searchQuery = ref('');

// Variável reativa para controlar o estado de foco do campo de entrada
const isFocused = ref(false);

// Função para adicionar itens ao array
const addItem = (itemValue: string) => {
  if (itemValue && !selectedItems.value.includes(itemValue)) {
    selectedItems.value.push(itemValue);
    emit('update:modelValue', selectedItems.value);
    searchQuery.value = ''; // Limpar o campo de pesquisa após adicionar
  }
};

// Função para remover itens do array
const removeItem = (itemValue: string) => {
  selectedItems.value = selectedItems.value.filter(i => i !== itemValue);
  emit('update:modelValue', selectedItems.value);
};

// Função para buscar itens (simplesmente um filtro)
const filteredOptions = computed(() => {
  const options = Array.isArray(props.options) ? props.options : [];
  return options.filter(item => item.text.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

// Atualizar `selectedItems` quando `modelValue` mudar
watch(() => props.modelValue, (newValue) => {
  selectedItems.value = [...newValue];
});
</script>

<template>
  <div>
    <!-- Campo de entrada para adicionar e pesquisar novos itens -->
    <div class="input-container input-multi-select" @focusin="isFocused = true"  @focusout="isFocused = false">
      <input v-model="searchQuery"  :placeholder="placeholder"  @focus="isFocused = true"  @blur="isFocused = false" />
      <div class="lit-multi">
        <span v-for="itemValue in selectedItems" :key="itemValue" class="selected-item">
          {{ props.options.find(option => option.value === itemValue)?.text }}
          <button @mousedown.prevent @click="removeItem(itemValue)">
            <IconComponent class="svg-icon" name="close" />
          </button>
        </span>
      </div>
    </div>

    <!-- Lista de opções disponíveis -->
    <ul v-if="isFocused || searchQuery" @mousedown.prevent>
      <li 
        class="item-multi"
        v-for="option in filteredOptions" 
        :key="option.value" 
        @mousedown.prevent="addItem(option.value)"
        :class="{ 'disabled': selectedItems.includes(option.value) }"
      >
        {{ $t(option.text) }}
      </li>
    </ul>
  </div>
</template>

<style scoped>
.input-container {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  padding: 4px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.input-container input {
  border: none;
  outline: none;
  flex-grow: 1;
  padding: 4px;
  width: 100%;
}

.selected-item {
  display: flex;
  align-items: center;
  padding: 4px 8px;
  border-radius: 4px;
  margin-right: 4px;
  background-color: #3175a3;
  color: #fff;
  opacity: 0.8;
}

.selected-item button {
  margin-left: 8px;
  background: none;
  border: none;
  cursor: pointer;
  color: red;
  font-weight: 600;
}

ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

li {
  padding: 8px;
  cursor: pointer;
}

li.disabled {
  color: #ccc;
  cursor: not-allowed;
}
</style>