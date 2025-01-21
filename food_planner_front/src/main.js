import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import FontAwesomeIcon from './plugins/fontawesome';
import './assets/styles.css';
const app = createApp(App);

app.component('font-awesome-icon', FontAwesomeIcon); // Register globally
app.use(router); // If you're using Vue Router
app.mount('#app');