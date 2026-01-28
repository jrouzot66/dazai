import { defineStore } from 'pinia'

const normalizeHex = (value) => {
    if (!value || typeof value !== 'string') return null
    const v = value.trim()
    if (/^#[0-9A-Fa-f]{6}$/.test(v)) return v
    if (/^[0-9A-Fa-f]{6}$/.test(v)) return `#${v}`
    return null
}

const safeString = (v) => (typeof v === 'string' && v.trim() ? v.trim() : null)

export const useWhiteLabelStore = defineStore('whiteLabel', {
    state: () => ({
        tenantName: null,
        brandName: null,
        logoUrl: null,
        primaryHex: null,
        secondaryHex: null,
        isLoaded: false,
    }),

    getters: {
        displayName: (state) => state.brandName || state.tenantName || null,
        hasBranding: (state) =>
            !!(state.brandName || state.logoUrl || state.primaryHex || state.secondaryHex),
    },

    actions: {
        initFromWindow() {
            const ctx = window.FLUXION_CONTEXT || {}
            this.tenantName = safeString(ctx.tenantName)

            const cfg = (ctx.config && typeof ctx.config === 'object') ? ctx.config : {}
            this.brandName = safeString(cfg.brandName) || this.tenantName
            this.logoUrl = safeString(cfg.logoUrl)
            this.primaryHex = normalizeHex(cfg.primaryHex)
            this.secondaryHex = normalizeHex(cfg.secondaryHex)

            this.isLoaded = true
            this.applyThemeIfNeeded()
        },

        applyThemeIfNeeded() {
            // Si config vide -> on ne touche à rien (thème par défaut)
            if (!this.hasBranding) {
                const existing = document.getElementById('wl-theme')
                if (existing) existing.remove()
                return
            }

            const cssLines = []

            // Couleurs : override minimal des éléments les plus visibles
            if (this.primaryHex) {
                cssLines.push(`
:root {
  --primary: ${this.primaryHex};
  --primary-2: ${this.primaryHex};
}

.input:focus, .select:focus {
  border-color: color-mix(in srgb, ${this.primaryHex} 45%, transparent) !important;
  box-shadow: 0 0 0 4px color-mix(in srgb, ${this.primaryHex} 12%, transparent) !important;
}
        `.trim())
            }

            // Secondaire (si tu ajoutes un .btn--secondary plus tard, il est déjà prêt)
            if (this.secondaryHex) {
                cssLines.push(`
:root {
  --secondary: ${this.secondaryHex};
}
        `.trim())
            }

            // Logo (optionnel) : var CSS si tu veux l’utiliser dans un composant
            if (this.logoUrl) {
                cssLines.push(`
:root { --wl-logo-url: url("${this.logoUrl.replace(/"/g, '\\"')}"); }
        `.trim())
            }

            const css = cssLines.filter(Boolean).join('\n\n')
            if (!css.trim()) return

            let styleTag = document.getElementById('wl-theme')
            if (!styleTag) {
                styleTag = document.createElement('style')
                styleTag.id = 'wl-theme'
                document.head.appendChild(styleTag)
            }
            styleTag.textContent = css
        },
    },
})