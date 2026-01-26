<script setup>
/**
 * Section MO: création + liste warehouses
 */
const props = defineProps({
  loading: { type: Boolean, required: true },
  error: { type: String, default: null },
  warehouses: { type: Array, required: true },

  showForm: { type: Boolean, required: true },
  submitting: { type: Boolean, required: true },
  formData: { type: Object, required: true },
})

const emit = defineEmits([
  'update:showForm',
  'update:formData',
  'create-warehouse',
])

const setFormField = (key, value) => {
  emit('update:formData', { ...props.formData, [key]: value })
}
</script>

<template>
  <section>
    <h2>Entrepôts (Warehouses)</h2>

    <button
        type="button"
        @click="emit('update:showForm', !showForm)"
        style="margin-bottom: 16px;"
    >
      {{ showForm ? 'Annuler' : 'Créer un entrepôt' }}
    </button>

    <div v-if="showForm" style="border: 1px solid #ccc; padding: 16px; margin-bottom: 16px;">
      <h3>Nouvel entrepôt</h3>

      <div style="margin-bottom: 12px;">
        <label>Nom :</label>
        <input
            :value="formData.name"
            type="text"
            style="width: 100%;"
            @input="setFormField('name', $event.target.value)"
        />
      </div>

      <div style="margin-bottom: 12px;">
        <label>Adresse :</label>
        <input
            :value="formData.address"
            type="text"
            style="width: 100%;"
            @input="setFormField('address', $event.target.value)"
        />
      </div>

      <button type="button" :disabled="submitting" @click="emit('create-warehouse')">
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
        <th>Nom</th>
        <th>Adresse</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="w in warehouses" :key="w.id">
        <td>{{ w.id }}</td>
        <td>{{ w.name }}</td>
        <td>{{ w.address }}</td>
      </tr>
      <tr v-if="warehouses.length === 0">
        <td colspan="3">Aucun entrepôt</td>
      </tr>
      </tbody>
    </table>
  </section>
</template>