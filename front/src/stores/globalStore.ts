import { defineStore } from 'pinia'

export const globalStore = defineStore('global', {
  state: () => ({
    counter: 0,
    user: {},
    permisos: (() => {
      try {
        return JSON.parse(localStorage.getItem('permisosReclamo') || '[]')
      } catch {
        return []
      }
    })(),
    permisoIds: (() => {
      try {
        return JSON.parse(localStorage.getItem('permisoIdsReclamo') || '[]')
      } catch {
        return []
      }
    })(),
    permisosCatalog: (() => {
      try {
        return JSON.parse(localStorage.getItem('permisosCatalogReclamo') || '{}')
      } catch {
        return {}
      }
    })(),
    isLoggedIn: !!localStorage.getItem('tokenReclamo'),



    }),
  getters: {
    doubleCount: (state) => state.counter * 2,
    hasPerm: (state) => (codigo: string) => {
      const user: any = (state as any)?.user || {}
      const rol = user?.rol || user?.role?.nombre || user?.role?.name
      if ((rol || '').toString().toUpperCase() === 'ADMINISTRADOR') return true
      const c = (codigo || '').toString().toLowerCase()

      if (Array.isArray((state as any).permisos) && (state as any).permisos.includes(c)) return true

      const catalog: any = (state as any).permisosCatalog || {}
      const permisoId = catalog?.[c]
      if (!permisoId) return false
      return Array.isArray((state as any).permisoIds) && (state as any).permisoIds.includes(Number(permisoId))
    }
  },
  actions: {
    increment () {
      this.counter++
    },
    setAuth (user: any, permisos: any[]) {
      this.user = user || {}
      const codigos: string[] = []
      const ids: number[] = []

      ;(Array.isArray(permisos) ? permisos : []).forEach((p: any) => {
        if (p && typeof p === 'object' && !Array.isArray(p)) {
          const maybeId = (p as any).id ?? (p as any).permiso_id ?? (p as any).permisoId
          const maybeCodigo = (p as any).codigo ?? (p as any).code ?? (p as any).permiso

          const idNum = Number(maybeId)
          if (Number.isFinite(idNum) && idNum > 0) ids.push(idNum)

          const codigoStr = (maybeCodigo ?? '').toString().trim()
          if (codigoStr) codigos.push(codigoStr.toLowerCase())
          return
        }
        if (typeof p === 'number' && Number.isFinite(p)) {
          ids.push(p)
          return
        }
        const s = (p ?? '').toString().trim()
        if (!s) return
        if (/^\d+$/.test(s)) {
          ids.push(parseInt(s, 10))
          return
        }
        codigos.push(s.toLowerCase())
      })

      this.permisos = Array.from(new Set(codigos))
      this.permisoIds = Array.from(new Set(ids.filter((x) => Number.isFinite(x) && x > 0)))
      localStorage.setItem('permisosReclamo', JSON.stringify(this.permisos))
      localStorage.setItem('permisoIdsReclamo', JSON.stringify(this.permisoIds))
    },
    setPermisosCatalog (permisos: any[]) {
      const catalog: Record<string, number> = {}
      ;(Array.isArray(permisos) ? permisos : []).forEach((p: any) => {
        const codigo = (p?.codigo || '').toString().toLowerCase().trim()
        const id = Number(p?.id)
        if (!codigo || !Number.isFinite(id) || id <= 0) return
        catalog[codigo] = id
      })
      this.permisosCatalog = catalog
      localStorage.setItem('permisosCatalogReclamo', JSON.stringify(this.permisosCatalog))
    },
    clearAuth () {
      this.user = {}
      this.permisos = []
      this.permisoIds = []
      localStorage.removeItem('permisosReclamo')
      localStorage.removeItem('permisoIdsReclamo')
    }
  }
})
