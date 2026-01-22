<script setup>
/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const handleLogout = () => {
  auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <h1>Bienvenue, {{ auth.user?.email }}</h1>
      <button type="button" @click="handleLogout">
        Déconnexion
      </button>
    </div>

    <p>Instance : {{ auth.tenant }}</p>
    <p>Vos rôles : {{ auth.user?.roles.join(', ') }}</p>
  </div>
</template>