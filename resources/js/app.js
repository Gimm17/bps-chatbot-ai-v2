import { createApp } from 'vue';
import '../css/app.css';
import ChatApp from './components/ChatApp.vue';

const app = createApp(ChatApp);
app.mount('#app');
