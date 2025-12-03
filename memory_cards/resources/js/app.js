import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min';

import { createApp } from 'vue';
import axios from 'axios';
import requestPlugin from './plugins/requestPlugin';
import MemoryCardCreate from './components/Create.vue';
import MemoryCardView from './components/View.vue';
import MemoryCardMenu from './components/Menu.vue';
import MemoryCardImport from './components/Import.vue';
import MemoryCardConfirm from './components/Confirm.vue';
import MemoryCardGroups from './components/Groups.vue';
import MemoryCardLangs from './components/Langs.vue';

const app = createApp({});
app.config.globalProperties.$trans = JSON.parse(document.getElementById('app').dataset.trans);
app.config.globalProperties.$axios = axios;
app.config.globalProperties.$bootstrap = bootstrap;
app.use(requestPlugin, axios);

app.component('memory-card-create', MemoryCardCreate);
app.component('memory-card-view', MemoryCardView);
app.component('memory-card-menu', MemoryCardMenu);
app.component('memory-card-import', MemoryCardImport);
app.component('memory-card-confirm', MemoryCardConfirm);
app.component('memory-card-groups', MemoryCardGroups);
app.component('memory-card-langs', MemoryCardLangs);

app.mount('#app');

// Регистрация сервис-воркера
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((registration) => {
                console.log('Service Worker registered with scope:', registration.scope);
            })
            .catch((error) => {
                console.log('Service Worker registration failed:', error);
            });
    });
}
