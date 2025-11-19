<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import CardTitleComponent from '@/components/CardTitleComponent.vue';
import ContainerComponent from '@/components/ContainerComponent.vue';
import CardComponent from '@/components/CardComponent.vue';
import ButtonComponent from '@/components/ButtonComponent.vue';

const router = useRouter();

const pages = [
    { name: 'Dashboard', route: '/preview/dashboard', description: 'Página principal do sistema' },
    { name: 'Processos', route: '/preview/process', description: 'Gerenciamento de processos' },
    { name: 'Status', route: '/preview/status', description: 'Gerenciamento de status' },
    { name: 'Clientes', route: '/preview/clients', description: 'Gerenciamento de clientes' },
    { name: 'Serviços', route: '/preview/services', description: 'Gerenciamento de serviços' },
    { name: 'Cargos', route: '/preview/roles', description: 'Gerenciamento de cargos' },
    { name: 'Usuários', route: '/preview/users', description: 'Gerenciamento de usuários' },
    { name: 'Busca', route: '/preview/search', description: 'Página de busca' },
];

const navigateTo = (route: string) => {
    router.push(route);
};
</script>

<template>
    <CardTitleComponent title="Preview de Páginas" />

    <ContainerComponent>
        <CardComponent titleCard="Navegação de Preview">
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3" v-for="page in pages" :key="page.route">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ page.name }}</h5>
                            <p class="card-text">{{ page.description }}</p>
                            <ButtonComponent 
                                buttonClass="btn-primary" 
                                :text="`Ver ${page.name}`" 
                                @click="navigateTo(page.route)"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </CardComponent>
    </ContainerComponent>
</template>

<style scoped>
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
