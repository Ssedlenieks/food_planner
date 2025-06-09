import { createApp } from 'vue';
import App from './App.vue'; // This wraps <router-view />
import router from './router';
import '../css/app.css';
import axios from 'axios';
import '@fortawesome/fontawesome-free/css/all.min.css'









axios.defaults.withCredentials = true;
axios.defaults.baseURL = 'http://127.0.0.1:8001';



createApp(App).use(router).mount('#app');
