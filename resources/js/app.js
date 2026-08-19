import { createApp } from 'vue';
import '../css/app.css';
import ChatApp from './components/ChatApp.vue';
import { usePwa } from './pwa';

const { initPwa } = usePwa();
initPwa();

const app = createApp(ChatApp);
app.mount('#app');
