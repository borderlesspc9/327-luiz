<script setup lang="ts">
import { inject, ref, computed, onBeforeUnmount, onMounted } from 'vue';
import IconShearch from '../icons/IconShearch.vue';
import IconComponent from '../IconComponent.vue';
import ModalComponent from '../ModalComponent.vue';
import UserProfileForm from '../UserProfileForm.vue';
import { useRouter, useRoute } from 'vue-router';

const toggleMenu = inject('toggleMenu') as () => void;
const isOpen = inject('isOpen');
const route = useRoute();

const getRandomColor = () => {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color === '#FFFFFF' ? getRandomColor() : color;
}

const backgroundColor = ref(getRandomColor());
const dropdownOpenUser = ref(false);
const modalUserProfile = ref(false);
const searchQuery = ref('');
const router = useRouter();

const toggleDropdownUser = () => {
    dropdownOpenUser.value = !dropdownOpenUser.value;
}

const closeDropdown = (event: Event) => {
    const target = event.target as HTMLElement;
    const dropdown = document.querySelector('.icon-user');
    
    if (dropdown && !dropdown.contains(target)) {
        dropdownOpenUser.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeDropdown);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', closeDropdown);
});

const toggleModalUser = () => {
    modalUserProfile.value = !modalUserProfile.value;
    dropdownOpenUser.value = false;
}

const getUser = () => {
    try {
        const userStr = localStorage.getItem('user');
        return userStr ? JSON.parse(userStr) : { name: 'Usuário', email: '' };
    } catch {
        return { name: 'Usuário', email: '' };
    }
};

const user = getUser();

const initialLetter = computed(() => {
    return user.name ? user.name.charAt(0).toUpperCase() : '';
});

const userData = ref({
    name: user.name || '',
    email: user.email || '',
    password: ''
});

const isPreviewRoute = computed(() => {
    return route.path.startsWith('/preview');
});

const navigateToResult = () => {
    if (isPreviewRoute.value) {
        router.push({ path: '/preview/search', query: { q: searchQuery.value } });
    } else {
        router.push({ path: '/painel/search', query: { q: searchQuery.value } });
    }
};
</script>

<template>
    <div class="nav-header" :class="{'openSide': isOpen}">
        <div class="navbar-header">
            <div style="position: relative;" class="first-box">
                <button type="button" class="btn btn-primary btn-hamburger" @click="toggleMenu()">
                    <span class="one-trash"></span>
                    <span class="fisrt-trash"></span>
                    <span class="fisrt-trash"></span>
                </button>
                <div class="input-group">
                    <button class="btn btn-outline-secondary" type="button" id="button-addon1">
                        <IconShearch />
                    </button>
                    <input 
                        type="text" 
                        class="form-control" 
                        placeholder="Buscar..." 
                        aria-label="Example text with button addon" 
                        aria-describedby="button-addon1" 
                        v-model="searchQuery"
                        @keyup.enter="navigateToResult" 
                    />
                </div>
            </div>
            <div class="second-box">
                <div style="position: relative;">
                    <div class="icon-user" :style="{ backgroundColor: backgroundColor }" @click="toggleDropdownUser">
                        <span>{{ initialLetter }}</span>
                    </div>
                    <div v-if="dropdownOpenUser" class="dropdown-user">
                        <div @click="toggleModalUser" class="dropdown-item">
                            <div class="circle" :style="{ backgroundColor: backgroundColor }">
                                {{ initialLetter }}
                            </div>
                            
                            <span>{{ user.name }}</span>
                        </div>
                        <router-link to="/logout" class="dropdown-item">
                            <div class="circle">
                                <IconComponent name="power-profile"/>
                            </div>
                            <span>Sair</span>
                        </router-link>
                    </div>
                </div>
            </div>

            <ModalComponent sizeClass="modal-small" :show="modalUserProfile" @closeModal="modalUserProfile = false" :titleHeader="'Perfil'">
                <UserProfileForm :userData="userData" />
            </ModalComponent>
        </div>
    </div>
</template>
