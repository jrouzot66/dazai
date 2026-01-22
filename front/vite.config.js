import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    vue(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    cors: true, // Autorise les requêtes cross-origin
    allowedHosts: [
      '.fluxion.local', // Autorise tous les sous-domaines fluxion.local
      'localhost'
    ],
    hmr: {
      host: 'localhost' // Pour que le rechargement à chaud fonctionne via ton PC
    }
  }
})