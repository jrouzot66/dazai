import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/login', name: 'login', component: () => import('@/views/LoginView.vue') },
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/HomeView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/mo',
      name: 'mo_dashboard',
      component: () => import('@/views/MoDashboardView.vue'),
      meta: { requiresAuth: true, requiredPortal: 'mo' }
    },
    {
      path: '/fo',
      name: 'fo_dashboard',
      component: () => import('@/views/FoDashboardView.vue'),
      meta: { requiresAuth: true, requiredPortal: 'fo' }
    }
  ]
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()
  if (!auth.isLoaded) await auth.fetchUser()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next({ name: 'login' })
    return
  }

  if (to.meta.requiredPortal) {
    const portal = auth.user?.portal
    if (portal !== to.meta.requiredPortal) {
      next({ name: 'home' })
      return
    }
  }

  next()
})

export default router