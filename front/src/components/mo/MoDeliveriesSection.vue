<script setup>
/**
 * Section MO: création + liste + actions workflow (plan/cancel)
 */
const props = defineProps({
  loading: { type: Boolean, required: true },
  error: { type: String, default: null },
  deliveries: { type: Array, required: true },
  organizations: { type: Array, required: true },

  showForm: { type: Boolean, required: true },
  submitting: { type: Boolean, required: true },
  formData: { type: Object, required: true },
})

const emit = defineEmits([
  'update:showForm',
  'update:formData',
  'create-delivery',
  'transition-delivery',
])

const setFormField = (key, value) => {
  emit('update:formData', { ...props.formData, [key]: value })
}

const badgeClassForStatus = (status) => {
  if (status === 'planned') return 'badge badge--success'
  if (status === 'cancelled') return 'badge badge--danger'
  return 'badge badge--muted'
}
</script>

<template>
  <section class="mo">
    <div class="card">
      <div class="card__header">
        <h2 class="h2">Livraisons (MO)</h2>

        <div class="mo__toolbar">
          <button
              class="btn btn--primary"
              type="button"
              @click="emit('update:showForm', !showForm)"
          >
            {{ showForm ? 'Annuler' : 'Créer une livraison' }}
          </button>
        </div>
      </div>

      <div class="card__body">
        <div v-if="showForm" class="mo__form card">
          <div class="card__body">
            <h3 class="h3">Nouvelle livraison</h3>

            <div class="field">
              <label>Adresse de pickup :</label>
              <input
                  class="input"
                  :value="formData.pickupAddress"
                  type="text"
                  @input="setFormField('pickupAddress', $event.target.value)"
              />
            </div>

            <div class="field">
              <label>Adresse de dropoff :</label>
              <input
                  class="input"
                  :value="formData.dropoffAddress"
                  type="text"
                  @input="setFormField('dropoffAddress', $event.target.value)"
              />
            </div>

            <div class="field">
              <label>Vendor :</label>
              <select
                  class="select"
                  :value="formData.vendorId"
                  @change="setFormField('vendorId', $event.target.value)"
              >
                <option value="">-- Sélectionner --</option>
                <option v-for="org in organizations" :key="org.id" :value="org.id">
                  {{ org.name }}
                </option>
              </select>
            </div>

            <div class="field">
              <label>Buyer :</label>
              <select
                  class="select"
                  :value="formData.buyerId"
                  @change="setFormField('buyerId', $event.target.value)"
              >
                <option value="">-- Sélectionner --</option>
                <option v-for="org in organizations" :key="org.id" :value="org.id">
                  {{ org.name }}
                </option>
              </select>
            </div>

            <div class="mo__actions">
              <button class="btn btn--primary" type="button" :disabled="submitting" @click="emit('create-delivery')">
                {{ submitting ? 'Création...' : 'Créer' }}
              </button>
            </div>

            <p v-if="error" class="mo__error">{{ error }}</p>
          </div>
        </div>

        <p v-if="loading" class="muted">Chargement...</p>
        <p v-else-if="error && !showForm" class="mo__error">{{ error }}</p>

        <div v-else class="mo__tableWrap">
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
              <th>Vendor</th>
              <th>Buyer</th>
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
              <td>{{ d.vendor }}</td>
              <td>{{ d.buyer }}</td>
              <td class="mo__rowActions">
                <button
                    v-if="d.status === 'draft'"
                    class="btn"
                    type="button"
                    @click="emit('transition-delivery', d.id, 'plan')"
                >
                  Planifier
                </button>

                <button
                    v-if="d.status === 'draft' || d.status === 'planned'"
                    class="btn btn--danger"
                    type="button"
                    @click="emit('transition-delivery', d.id, 'cancel')"
                >
                  Annuler
                </button>
              </td>
            </tr>

            <tr v-if="deliveries.length === 0">
              <td colspan="11" class="muted">Aucune livraison</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped lang="scss">
.mo {
  .mo__toolbar {
    margin-top: 12px;
    display: flex;
    gap: 10px;
  }

  .mo__form {
    margin-bottom: 16px;
    border-radius: 14px;
  }

  .mo__actions {
    margin-top: 8px;
    display: flex;
    gap: 10px;
  }

  .mo__error {
    margin: 12px 0 0 0;
    color: #ffb4b4;
  }

  .mo__tableWrap {
    margin-top: 12px;
    overflow: auto;
  }

  .mo__rowActions {
    display: flex;
    gap: 8px;
    align-items: center;
    white-space: nowrap;
  }
}
</style>