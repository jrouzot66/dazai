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
  <section class="moWh">
    <div class="card">
      <div class="card__header">
        <h2 class="h2">Entrepôts (Warehouses)</h2>

        <div class="moWh__toolbar">
          <button
              class="btn btn--primary"
              type="button"
              @click="emit('update:showForm', !showForm)"
          >
            {{ showForm ? 'Annuler' : 'Créer un entrepôt' }}
          </button>
        </div>
      </div>

      <div class="card__body">
        <div v-if="showForm" class="moWh__form card">
          <div class="card__body">
            <h3 class="h3">Nouvel entrepôt</h3>

            <div class="field">
              <label>Nom :</label>
              <input
                  class="input"
                  :value="formData.name"
                  type="text"
                  @input="setFormField('name', $event.target.value)"
              />
            </div>

            <div class="field">
              <label>Adresse :</label>
              <input
                  class="input"
                  :value="formData.address"
                  type="text"
                  @input="setFormField('address', $event.target.value)"
              />
            </div>

            <div class="moWh__actions">
              <button class="btn btn--primary" type="button" :disabled="submitting" @click="emit('create-warehouse')">
                {{ submitting ? 'Création...' : 'Créer' }}
              </button>
            </div>

            <p v-if="error" class="moWh__error">{{ error }}</p>
          </div>
        </div>

        <p v-if="loading" class="muted">Chargement...</p>
        <p v-else-if="error && !showForm" class="moWh__error">{{ error }}</p>

        <div v-else class="moWh__tableWrap">
          <table class="table">
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
              <td colspan="3" class="muted">Aucun entrepôt</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped lang="scss">
.moWh {
  .moWh__toolbar {
    margin-top: 12px;
    display: flex;
    gap: 10px;
  }

  .moWh__form {
    margin-bottom: 16px;
    border-radius: 14px;
  }

  .moWh__actions {
    margin-top: 8px;
    display: flex;
    gap: 10px;
  }

  .moWh__error {
    margin: 12px 0 0 0;
    color: #ffb4b4;
  }

  .moWh__tableWrap {
    margin-top: 12px;
    overflow: auto;
  }
}
</style>