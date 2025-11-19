<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'
import IconComponent from './IconComponent.vue';
import ButtonComponent from './ButtonComponent.vue';

const props = defineProps({
  files: {
    type: Array,
    required: true
  },
  showBtnDeleteFile: {
    type: Boolean,
    default: false
  }
});

const emits = defineEmits(['viewFile', 'deleteFile']);

</script>

<template>
    <div class="file-list">
      <div 
        class="card-file" 
        v-for="(file, index) in files" 
        :key="index"
      >
      <div :class="`card-header label-file-${file.type}`"></div>
      <div class="delete-file" v-if="showBtnDeleteFile">
        <IconComponent class="svg" name="x" @click="$emit('deleteFile', file)" />
      </div>

        <div class="card-icon">
          <div class="icon-file">
            <IconComponent :class="`svg ${file.type}`" :name="file.type" />
          </div>
        </div>
        <div class="card-content">
          <h6 class="title-file">{{ file.name }}</h6>
          <ButtonComponent text="Ver" :class="`card-button ${file.type}`" @click="$emit('viewFile', file)" />
        </div>
      </div>
    </div>
</template>