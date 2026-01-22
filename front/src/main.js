import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import axios from 'axios'

axios.defaults.withCredentials = true

const app = createApp(App)
const pinia = createPinia()
app.use(pinia)

// On initialise le store et on applique le token si présent
import { useAuthStore } from './stores/auth'
const auth = useAuthStore()
if (auth.token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${auth.token}`
}

app.use(router)
app.mount('#app')