
/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */
import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('jwt_token'),
    isAuthenticated: !!localStorage.getItem('jwt_token'),
    isLoaded: false,
    axiosInstance: axios // On gardera une référence à Axios
  }),

  actions: {
    async login(email, password) {
      try {
        console.log('📝 Attempting login with:', email)
        const response = await axios.post('/api/login', { email, password })

        console.log('✅ Login response received:', response.data)
        this.token = response.data.token
        localStorage.setItem('jwt_token', this.token)

        this.updateAxiosHeaders()
        this.isAuthenticated = true

        console.log('🔄 Calling fetchUser...')
        return await this.fetchUser()
      } catch (e) {
        console.error('❌ Login failed:', e.response?.status, e.response?.data)
        this.logout()
        throw new Error("Identifiants incorrects")
      }
    },

    updateAxiosHeaders() {
      // Configure le header global
      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      // Et aussi pour cette instance
      this.axiosInstance.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
    },

    async fetchUser() {
      if (!this.token) {
        this.isLoaded = true
        return
      }

      try {
        // DEBUG : On affiche ce qu'on envoie
        const authHeader = `Bearer ${this.token}`
        console.log('🔍 Token exists:', !!this.token)
        console.log('🔍 Auth header:', authHeader.substring(0, 30) + '...')

        const response = await axios.get('/api/logged', {
          headers: { 'Authorization': authHeader }
        })

        console.log('✅ Success:', response.data)
        this.user = response.data
        this.isAuthenticated = true
      } catch (e) {
        console.error('❌ Error:', e.response?.status, e.response?.data)
        this.logout()
      } finally {
        this.isLoaded = true
      }
    },

    logout() {
      this.token = null
      this.user = null
      this.isAuthenticated = false
      localStorage.removeItem('jwt_token')
      delete axios.defaults.headers.common['Authorization']
    }
  }
})