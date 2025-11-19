<script setup lang="ts">
import { ref, watch } from 'vue';
import FormGroupComponent from './form/FormGroupComponent.vue';
import InputComponent from './form/InputComponent.vue';
const props = defineProps<{
    userData: {
        name: string,
        email: string,
        password: string,
    }
}>();

const form = ref({
    name: props.userData.name || '',
    email: props.userData.email || '',
    password: props.userData.password || ''
});

watch(() => props.userData, (newUserData) => {
    form.value = {
        name: newUserData.name,
        email: newUserData.email,
        password: newUserData.password
    };
});

</script>

<template>
    <FormGroupComponent>
        <InputComponent v-model="form.name" placeholder="Nome" type="text" />
    </FormGroupComponent>
    <FormGroupComponent>
        <InputComponent v-model="form.email" placeholder="E-mail" type="text" />
    </FormGroupComponent>
    <FormGroupComponent>
        <InputComponent v-model="form.password" placeholder="Password" type="password" />
    </FormGroupComponent>
</template>
