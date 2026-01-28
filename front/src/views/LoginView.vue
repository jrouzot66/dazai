<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const error = ref(null)
const auth = useAuthStore()
const router = useRouter()

const handleLogin = async () => {
  try {
    await auth.login(email.value, password.value)
    router.push({ name: 'home' })
  } catch (e) {
    error.value = 'Identifiants invalides'
  }
}
</script>

<template>
  <div class="page login">
    <div class="card login__card">
      <div class="card__body">
        <h1 class="h2">Connexion <span class="muted">{{ auth.tenant }}</span></h1>
        <p class="muted login__subtitle">Connecte-toi pour accéder à ton dashboard.</p>

        <form class="login__form" @submit.prevent="handleLogin">
          <div class="field">
            <label>Email</label>
            <input v-model="email" class="input" type="email" placeholder="email@exemple.tld" required />
          </div>

          <div class="field">
            <label>Mot de passe</label>
            <input v-model="password" class="input" type="password" placeholder="••••••••" required />
          </div>

          <button class="btn btn--primary" type="submit">Se connecter</button>

          <p v-if="error" class="login__error">{{ error }}</p>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.login {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 24px 16px;

  .login__card {
    width: min(520px, 100%);
  }

  .login__subtitle {
    margin: 0 0 14px 0;
  }

  .login__form {
    display: grid;
    gap: 12px;
  }

  .login__error {
    margin: 0;
    color: #ffb4b4;
  }
}
</style>