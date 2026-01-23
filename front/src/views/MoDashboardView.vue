<script setup>
/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'
import { fetchMoDeliveries, createMoDelivery, applyMoDeliveryTransition } from '@/api/deliveriesApi'
import { fetchOrganizations } from '@/api/organization'

const auth = useAuthStore()
const router = useRouter()
const loading = ref(true)
const error = ref(null)
const deliveries = ref([])
const showForm = ref(false)
const submitting = ref(false)

const formData = ref({
  pickupAddress: '',
  dropoffAddress: '',
  vendorId: '',
  buyerId: '',
})

const organizations = ref([])

const handleLogout = () => {
  auth.logout()
  router.push({ name: 'login' })
}

const createDelivery = async () => {
  if (!formData.value.pickupAddress || !formData.value.dropoffAddress || !formData.value.vendorId || !formData.value.buyerId) {
    error.value = 'Tous les champs sont requis'
    return
  }

  submitting.value = true
  try {
    const response = await createMoDelivery({
      pickupAddress: formData.value.pickupAddress,
      dropoffAddress: formData.value.dropoffAddress,
      vendorId: parseInt(formData.value.vendorId),
      buyerId: parseInt(formData.value.buyerId),
    })

    deliveries.value.unshift(response)
    formData.value = { pickupAddress: '', dropoffAddress: '', vendorId: '', buyerId: '' }
    showForm.value = false
    error.value = null
  } catch (err) {
    error.value = err.response?.data?.error || 'Erreur lors de la création'
    console.error(err)
  } finally {
    submitting.value = false
  }
}

const transitionDelivery = async (deliveryId, transition) => {
  error.value = null
  try {
    const updated = await applyMoDeliveryTransition(deliveryId, transition)
    const idx = deliveries.value.findIndex((d) => d.id === deliveryId)
    if (idx !== -1) {
      deliveries.value[idx].status = updated.status
      deliveries.value[idx].plannedAt = updated.plannedAt
    }
  } catch (err) {
    error.value = err.response?.data?.error || 'Erreur transition'
    console.error(err)
  }
}

onMounted(async () => {
  try {
    if (!auth.user) {
      await auth.fetchUser()
    }

    deliveries.value = await fetchMoDeliveries()
    organizations.value = await fetchOrganizations()
  } catch (err) {
    error.value = err.response?.data?.message || err.message
    console.error(err)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <h1>Dashboard MO</h1>
      <button type="button" @click="handleLogout">
        Déconnexion
      </button>
    </div>

    <p>Utilisateur : {{ auth.user?.email }}</p>
    <p>Instance : {{ auth.tenant }}</p>
    <p>Vos rôles : {{ auth.user?.roles?.join(', ') }}</p>

    <hr />

    <h2>Livraisons (MO)</h2>

    <button type="button" @click="showForm = !showForm" style="margin-bottom: 16px;">
      {{ showForm ? 'Annuler' : 'Créer une livraison' }}
    </button>

    <div v-if="showForm" style="border: 1px solid #ccc; padding: 16px; margin-bottom: 16px;">
      <h3>Nouvelle livraison</h3>
      <div style="margin-bottom: 12px;">
        <label>Adresse de pickup :</label>
        <input v-model="formData.pickupAddress" type="text" style="width: 100%;" />
      </div>
      <div style="margin-bottom: 12px;">
        <label>Adresse de dropoff :</label>
        <input v-model="formData.dropoffAddress" type="text" style="width: 100%;" />
      </div>
      <div style="margin-bottom: 12px;">
        <label>Vendor :</label>
        <select v-model="formData.vendorId" style="width: 100%;">
          <option value="">-- Sélectionner --</option>
          <option v-for="org in organizations" :key="org.id" :value="org.id">
            {{ org.name }}
          </option>
        </select>
      </div>
      <div style="margin-bottom: 12px;">
        <label>Buyer :</label>
        <select v-model="formData.buyerId" style="width: 100%;">
          <option value="">-- Sélectionner --</option>
          <option v-for="org in organizations" :key="org.id" :value="org.id">
            {{ org.name }}
          </option>
        </select>
      </div>
      <button type="button" @click="createDelivery" :disabled="submitting">
        {{ submitting ? 'Création...' : 'Créer' }}
      </button>
      <p v-if="error" style="color: red;">{{ error }}</p>
    </div>

    <p v-if="loading">Chargement...</p>
    <p v-else-if="error && !showForm" style="color: red;">{{ error }}</p>

    <table v-else border="1" cellpadding="6" cellspacing="0" style="width: 100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Référence</th>
          <th>Statut</th>
          <th>Pickup</th>
          <th>Dropoff</th>
          <th>PlannedAt</th>
          <th>Vendor</th>
          <th>Buyer</th>
          <th>Actions</th>
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
          <td>{{ d.vendor }}</td>
          <td>{{ d.buyer }}</td>
          <td>
            <button
              v-if="d.status === 'draft'"
              type="button"
              @click="transitionDelivery(d.id, 'plan')"
            >
              Planifier
            </button>

            <button
              v-if="d.status === 'draft' || d.status === 'planned'"
              type="button"
              style="margin-left: 8px;"
              @click="transitionDelivery(d.id, 'cancel')"
            >
              Annuler
            </button>
          </td>
        </tr>
        <tr v-if="deliveries.length === 0">
          <td colspan="9">Aucune livraison</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>