<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

onMounted(async () => {
  if (!auth.isLoaded) await auth.fetchUser()

  const portal = auth.user?.portal
  if (portal === 'mo') {
    await router.replace({ name: 'mo_dashboard' })
    return
  }
  if (portal === 'fo') {
    await router.replace({ name: 'fo_dashboard' })
    return
  }
})
</script>

<template>
  <div class="page">
    <div class="card">
      <div class="card__body">
        <h1 class="h2">Accueil</h1>

        <p v-if="auth.user?.portal === 'unknown'" class="home__warning">
          Votre compte n’a pas de profil MO/FO valide. Contactez un administrateur.
        </p>
        <p v-else class="muted">Redirection en cours…</p>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.home__warning {
  color: #ffcf9d;
}
</style>