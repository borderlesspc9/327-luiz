<script setup lang="ts">
import {ref, provide, onMounted} from 'vue';
import Sidebar from '@/components/partials/Sidebar.vue';
import Header from '@/components/partials/Header.vue'
import Container from '@/components/partials/Container.vue';

//Estado para controlar o menu
const isOpen = ref(true);

//Função para alterar o menun
const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};

provide('isOpen', isOpen);
provide('toggleMenu', toggleMenu);

onMounted(() => {
    let wideScreen = window.innerWidth
    isOpen.value = wideScreen > 992 ? true : false;
});
</script>

<template>
    <Sidebar />
    <Header />
    <Container>
        <router-view></router-view>
    </Container>
</template>