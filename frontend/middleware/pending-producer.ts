export default defineNuxtRouteMiddleware(async (to) => {
  if (process.client) {
    try {
      const token = localStorage.getItem('token')
      if (!token) return

      const config = useRuntimeConfig()
      const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'

      let user
      try {
        const res = await fetch(`${apiUrl}/profil`, {
          headers: { Authorization: `Bearer ${token}` }
        })
        if (res.ok) {
          const data = await res.json()
          user = data.user || data
          localStorage.setItem('user', JSON.stringify(user))
        }
      } catch {
        user = JSON.parse(localStorage.getItem('user') || '{}')
      }

      if (user?.role === 'producteur' && user?.statut === 'en_attente') {
        const allowed = ['/producteur', '/producteur/documents']
        if (!allowed.includes(to.path)) {
          return navigateTo('/producteur/documents')
        }
      }
    } catch {}
  }
})
