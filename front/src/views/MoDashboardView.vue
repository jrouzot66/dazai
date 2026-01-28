<script setup>
/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'
import { fetchMoDeliveries, createMoDelivery, applyMoDeliveryTransition } from '@/api/deliveriesApi'
import { fetchOrganizations } from '@/api/organization'
import { fetchMoWarehouses, createMoWarehouse } from '@/api/warehousesApi'
import MoDeliveriesSection from '@/components/mo/MoDeliveriesSection.vue'
import MoWarehousesSection from '@/components/mo/MoWarehousesSection.vue'

const auth = useAuthStore()
const router = useRouter()
const loading = ref(true)
const error = ref(null)

const deliveries = ref([])
const organizations = ref([])

const showForm = ref(false)
const submitting = ref(false)

const formData = ref({
  pickupAddress: '',
  dropoffAddress: '',
  vendorId: '',
  buyerId: '',
})

const warehouses = ref([])
const warehousesLoading = ref(false)
const warehousesError = ref(null)
const showWarehouseForm = ref(false)
const submittingWarehouse = ref(false)

const warehouseForm = ref({
  name: '',
  address: '',
})

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

const refreshWarehouses = async () => {
  warehousesLoading.value = true
  warehousesError.value = null
  try {
    warehouses.value = await fetchMoWarehouses()
  } catch (err) {
    warehousesError.value = err.response?.data?.error || 'Impossible de charger les entrepôts'
    console.error(err)
  } finally {
    warehousesLoading.value = false
  }
}

const createWarehouse = async () => {
  if (!warehouseForm.value.name || !warehouseForm.value.address) {
    warehousesError.value = 'Tous les champs sont requis'
    return
  }

  submittingWarehouse.value = true
  warehousesError.value = null
  try {
    const created = await createMoWarehouse({
      name: warehouseForm.value.name,
      address: warehouseForm.value.address,
    })

    warehouses.value.unshift(created)
    warehouseForm.value = { name: '', address: '' }
    showWarehouseForm.value = false
  } catch (err) {
    warehousesError.value = err.response?.data?.error || 'Erreur lors de la création de l’entrepôt'
    console.error(err)
  } finally {
    submittingWarehouse.value = false
  }
}

onMounted(async () => {
  try {
    if (!auth.user) {
      await auth.fetchUser()
    }

    deliveries.value = await fetchMoDeliveries()
    organizations.value = await fetchOrganizations()
    await refreshWarehouses()
  } catch (err) {
    error.value = err.response?.data?.message || err.message
    console.error(err)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="page">
    <div class="card">
      <div class="card__body">
        <div class="dash__top">
          <h1 class="h2">Dashboard MO</h1>
          <button class="btn" type="button" @click="handleLogout">Déconnexion</button>
        </div>

        <div class="dash__meta">
          <div class="dash__metaItem"><span class="muted">Utilisateur</span><span>{{ auth.user?.email }}</span></div>
          <div class="dash__metaItem"><span class="muted">Instance</span><span>{{ auth.tenant }}</span></div>
          <div class="dash__metaItem"><span class="muted">Rôles</span><span>{{ auth.user?.roles?.join(', ') }}</span></div>
        </div>
      </div>
    </div>

    <div class="dash__sections">
      <MoDeliveriesSection
          :loading="loading"
          :error="error"
          :deliveries="deliveries"
          :organizations="organizations"
          :show-form="showForm"
          :submitting="submitting"
          :form-data="formData"
          @update:showForm="showForm = $event"
          @update:formData="formData = $event"
          @create-delivery="createDelivery"
          @transition-delivery="transitionDelivery"
      />

      <MoWarehousesSection
          :loading="warehousesLoading"
          :error="warehousesError"
          :warehouses="warehouses"
          :show-form="showWarehouseForm"
          :submitting="submittingWarehouse"
          :form-data="warehouseForm"
          @update:showForm="showWarehouseForm = $event"
          @update:formData="warehouseForm = $event"
          @create-warehouse="createWarehouse"
      />
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

.dash__sections {
  margin-top: 16px;
  display: grid;
  gap: 16px;
}
</style>