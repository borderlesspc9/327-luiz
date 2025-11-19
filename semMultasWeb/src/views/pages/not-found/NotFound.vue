<script setup lang="ts">
import IconComponent from '@/components/IconComponent.vue';
import { CardTitleComponent, ContainerComponent } from '@/utils/components';

</script>

<template>
    <CardTitleComponent title="Página Não Encontrada" />

    <ContainerComponent>
        <div class="box-404-container">
            <div class="box-404">
                <div>
                    <IconComponent name="face-frown"/>
                    <h1>404</h1>
                </div>
                <p>Página não encontrada.</p>
            </div>
        </div>
    </ContainerComponent>
</template>