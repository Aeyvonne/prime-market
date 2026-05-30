export default defineNuxtRouteMiddleware((to) => {
  if (import.meta.client) {
    const userStr = localStorage.getItem('user')
    if (!userStr) return

    let role = ''
    try {
      role = JSON.parse(userStr).role
    } catch {
      return
    }

    const roleRoutes: Record<string, string> = {
      producteur: '/producteur',
      distributeur: '/distributeur',
      consommateur: '/consommateur',
      transporteur: '/transporteur',
      admin_sectoriel: '/admin/sectoriel',
      super_administrateur: '/admin/super',
    }

    const allowedPrefix = roleRoutes[role]
    if (!allowedPrefix) return

    const path = to.path
    if (path === '/profil') return

    if (path !== allowedPrefix && !path.startsWith(allowedPrefix + '/')) {
      return navigateTo(allowedPrefix)
    }
  }
})
