<script setup>
/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */
import { onMounted, ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useWhiteLabelStore } from '@/stores/whiteLabel'
import { useRouter } from 'vue-router'
import { fetchFoDeliveries, applyFoDeliveryTransition } from '@/api/deliveriesApi'

const auth = useAuthStore()
const whiteLabel = useWhiteLabelStore()
const router = useRouter()

const brandSuffix = computed(() => (whiteLabel.displayName ? ` — ${whiteLabel.displayName}` : ''))

const deliveries = ref([])
const loading = ref(false)
const error = ref(null)

const handleLogout = () => {
  auth.logout()
  router.push({ name: 'login' })
}

const refreshDeliveries = async () => {
  loading.value = true
  error.value = null
  try {
    deliveries.value = await fetchFoDeliveries()
  } catch (e) {
    error.value = e?.response?.data?.error ?? 'Impossible de charger les livraisons (FO)'
  } finally {
    loading.value = false
  }
}

const transitionDelivery = async (deliveryId, transition) => {
  error.value = null
  try {
    const updated = await applyFoDeliveryTransition(deliveryId, transition)
    const idx = deliveries.value.findIndex((d) => d.id === deliveryId)
    if (idx !== -1) {
      deliveries.value[idx].status = updated.status
    } else {
      await refreshDeliveries()
    }
  } catch (e) {
    error.value = e?.response?.data?.error ?? 'Erreur transition (FO)'
  }
}

const badgeClassForStatus = (status) => {
  if (status === 'planned') return 'badge badge--success'
  if (status === 'in_transit') return 'badge'
  if (status === 'delivered') return 'badge badge--success'
  if (status === 'cancelled') return 'badge badge--danger'
  return 'badge badge--muted'
}

onMounted(async () => {
  await refreshDeliveries()
})
</script>

<template>
  <div class="page">
    <div class="card">
      <div class="card__body">
        <div class="dash__top">
          <h1 class="h2">Dashboard FO{{ brandSuffix }}</h1>
          <button class="btn" type="button" @click="handleLogout">Déconnexion</button>
        </div>

        <div class="dash__meta">
          <div class="dash__metaItem"><span class="muted">Utilisateur</span><span>{{ auth.user?.email }}</span></div>
          <div class="dash__metaItem"><span class="muted">Instance</span><span>{{ auth.tenant }}</span></div>
          <div class="dash__metaItem"><span class="muted">Rôles</span><span>{{ auth.user?.roles?.join(', ') }}</span></div>
        </div>
      </div>
    </div>

    <div class="card fo__card">
      <div class="card__body">
        <h2 class="h2">Mes livraisons (FO)</h2>

        <p v-if="loading" class="muted">Chargement...</p>
        <p v-else-if="error" class="fo__error">{{ error }}</p>

        <div v-else class="fo__tableWrap">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Référence</th>
                <th>Statut</th>
                <th>Pickup</th>
                <th>Dropoff</th>
                <th>PlannedAt</th>
                <th>ETA</th>
                <th>Distance (km)</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="d in deliveries" :key="d.id">
                <td>{{ d.id }}</td>
                <td>{{ d.reference }}</td>
                <td><span :class="badgeClassForStatus(d.status)">{{ d.status }}</span></td>
                <td>{{ d.pickupAddress }}</td>
                <td>{{ d.dropoffAddress }}</td>
                <td>{{ d.plannedAt }}</td>
                <td>{{ d.etaAt }}</td>
                <td>{{ d.distanceKm }}</td>
                <td class="fo__rowActions">
                  <button
                    v-if="d.status === 'planned'"
                    class="btn"
                    type="button"
                    @click="transitionDelivery(d.id, 'start')"
                  >
                    Démarrer
                  </button>

                  <button
                    v-if="d.status === 'in_transit'"
                    class="btn btn--primary"
                    type="button"
                    @click="transitionDelivery(d.id, 'deliver')"
                  >
                    Livrer
                  </button>
                </td>
              </tr>

              <tr v-if="deliveries.length === 0">
                <td colspan="9" class="muted">Aucune livraison</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.dash__top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.dash__meta {
  margin-top: 12px;
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 10px;

  @media (max-width: 900px) {
    grid-template-columns: 1fr;
  }
}

.dash__metaItem {
  display: grid;
  gap: 4px;
  padding: 10px 12px;
  border: 1px solid rgba(255, 255, 255, 0.10);
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.04);
}

.fo__card {
  margin-top: 16px;
}

.fo__tableWrap {
  margin-top: 12px;
  overflow: auto;
}

.fo__rowActions {
  display: flex;
  gap: 8px;
  align-items: center;
  white-space: nowrap;
}

.fo__error {
  margin: 12px 0 0 0;
  color: #ffb4b4;
}
</style>