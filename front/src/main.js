import { createApp } from 'vue'
import App from './App.vue'
import Echo from 'laravel-echo';

createApp(App).mount('#app')

window.io = require('socket.io-client/dist/socket.io');

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ':6001'
});

