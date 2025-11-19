<script setup lang="ts">

import { defineProps } from 'vue';

const props = defineProps<{
    title: string;
}>();

</script>
<template>
    <div class="box-header-title">
        <h3>{{ title }}</h3>
    </div>
</template>