<script setup lang="ts">
    import { useLogoutStore } from '@/stores/logout.store';
    import { onMounted, ref } from 'vue';
    import { LoadingComponent } from '@/utils/components';

    const logoutStore = useLogoutStore();
    const isLoading = ref(false);

    onMounted(async() => {
        isLoading.value = true;
        await logoutStore.logout();
        isLoading.value = false;
    });

</script>

<template>
    <LoadingComponent :show="isLoading" />
</template>