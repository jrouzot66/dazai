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
</script>

<template>
  <section>
    <h2>Livraisons (MO)</h2>

    <button
        type="button"
        @click="emit('update:showForm', !showForm)"
        style="margin-bottom: 16px;"
    >
      {{ showForm ? 'Annuler' : 'Créer une livraison' }}
    </button>

    <div v-if="showForm" style="border: 1px solid #ccc; padding: 16px; margin-bottom: 16px;">
      <h3>Nouvelle livraison</h3>

      <div style="margin-bottom: 12px;">
        <label>Adresse de pickup :</label>
        <input
            :value="formData.pickupAddress"
            type="text"
            style="width: 100%;"
            @input="setFormField('pickupAddress', $event.target.value)"
        />
      </div>

      <div style="margin-bottom: 12px;">
        <label>Adresse de dropoff :</label>
        <input
            :value="formData.dropoffAddress"
            type="text"
            style="width: 100%;"
            @input="setFormField('dropoffAddress', $event.target.value)"
        />
      </div>

      <div style="margin-bottom: 12px;">
        <label>Vendor :</label>
        <select
            :value="formData.vendorId"
            style="width: 100%;"
            @change="setFormField('vendorId', $event.target.value)"
        >
          <option value="">-- Sélectionner --</option>
          <option v-for="org in organizations" :key="org.id" :value="org.id">
            {{ org.name }}
          </option>
        </select>
      </div>

      <div style="margin-bottom: 12px;">
        <label>Buyer :</label>
        <select
            :value="formData.buyerId"
            style="width: 100%;"
            @change="setFormField('buyerId', $event.target.value)"
        >
          <option value="">-- Sélectionner --</option>
          <option v-for="org in organizations" :key="org.id" :value="org.id">
            {{ org.name }}
          </option>
        </select>
      </div>

      <button type="button" :disabled="submitting" @click="emit('create-delivery')">
        {{ submitting ? 'Création...' : 'Créer' }}
      </button>

      <p v-if="error" style="color: red;">{{ error }}</p>
    </div>

    <p v-if="loading">Chargement...</p>
    <p v-else-if="error && !showForm" style="color: red;">{{ error }}</p>

    <table v-else border="1" cellpadding="6" cellspacing="0" style="width: 100%; margin-bottom: 24px;">
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
              @click="emit('transition-delivery', d.id, 'plan')"
          >
            Planifier
          </button>

          <button
              v-if="d.status === 'draft' || d.status === 'planned'"
              type="button"
              style="margin-left: 8px;"
              @click="emit('transition-delivery', d.id, 'cancel')"
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
  </section>
</template>