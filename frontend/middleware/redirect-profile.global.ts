export default defineNuxtRouteMiddleware((to) => {
  const path = to.path
  // Si le chemin se termine par /profil mais n'est pas exactement /profil, on redirige vers /profil
  if (path.endsWith('/profil') && path !== '/profil') {
    return navigateTo('/profil')
  }
})
