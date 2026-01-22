import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/login', name: 'login', component: () => import('@/views/LoginView.vue') },
        {
            path: '/',
            name: 'dashboard',
            component: () => import('@/views/DashboardView.vue'),
            meta: { requiresAuth: true }
        }
    ]
})

// Protection des routes
router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore()
    if (!auth.isLoaded) await auth.fetchUser()

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        next({ name: 'login' })
    } else {
        next()
    }
})

export default router