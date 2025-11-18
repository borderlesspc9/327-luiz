import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { vMaska } from 'maska/vue';
import App from './App.vue';
import router from './router';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import './assets/main.css';
import './assets/scss/styles.scss';
import './assets/scss/main.scss';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import messages from '@intlify/unplugin-vue-i18n/messages';
import { createI18n } from 'vue-i18n';
import money from 'v-money3'

const i18n = createI18n({
  locale: localStorage.getItem('locale') || 'pt-br',
  messages,
});

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(Toast);
app.use(i18n);
app.use(money)

app.directive('maska', vMaska);

app.mount('#app');
