<script setup lang="ts">
import { defineProps, defineEmits, ref } from 'vue';

defineProps({
    titleCard: String,
    showHeader: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div class="card">
        <div class="card-header" v-if="showHeader">
            <div class="title-search">
                <h3 class="title-card">{{ titleCard }}</h3>
                <slot name="search"></slot>
                <slot name="filter"></slot>
            </div>
            <slot name="button"></slot>
        </div>
        <slot></slot>
    </div>
</template>
