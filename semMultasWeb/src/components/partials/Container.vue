<script setup lang="ts">
import { inject } from 'vue';

const isOpen = inject('isOpen');
</script>

<template>
    <div class="container-panel" :class="{ 'containerOpen' : isOpen}">
        <slot></slot>
    </div>
</template>

<style>
</style>