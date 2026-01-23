<script setup>
/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */
import { onMounted, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { fetchFoDeliveries } from '@/api/deliveriesApi'

const auth = useAuthStore()
const router = useRouter()

const deliveries = ref([])
const loading = ref(false)
const error = ref(null)

const handleLogout = () => {
  auth.logout()
  router.push({ name: 'login' })
}

onMounted(async () => {
  loading.value = true
  error.value = null
  try {
    deliveries.value = await fetchFoDeliveries()
  } catch (e) {
    error.value = e?.response?.data?.error ?? 'Impossible de charger les livraisons (FO)'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <h1>Dashboard FO</h1>
      <button type="button" @click="handleLogout">
        Déconnexion
      </button>
    </div>

    <p>Utilisateur : {{ auth.user?.email }}</p>
    <p>Instance : {{ auth.tenant }}</p>
    <p>Vos rôles : {{ auth.user?.roles?.join(', ') }}</p>

    <hr />

    <h2>Mes livraisons (FO)</h2>

    <p v-if="loading">Chargement...</p>
    <p v-else-if="error" style="color: red">{{ error }}</p>

    <table v-else border="1" cellpadding="6" cellspacing="0" style="width: 100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Référence</th>
          <th>Statut</th>
          <th>Pickup</th>
          <th>Dropoff</th>
          <th>PlannedAt</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="d in deliveries" :key="d.id">
          <td>{{ d.id }}</td>
          <td>{{ d.reference }}</td>
          <td>{{ d.status }}</td>
          <td>{{ d.pickupAddress }}</td>
          <td>{{ d.dropoffAddress }}</td>
          <td>{{ d.plannedAt }}</td>
        </tr>
        <tr v-if="deliveries.length === 0">
          <td colspan="6">Aucune livraison</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>