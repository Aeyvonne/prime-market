<template>
  <div class="min-h-screen flex flex-col font-sans" style="background-color: #F5F0E8;">
    <!-- Navbar Premium -->
    <header 
      class="fixed w-full z-50 transition-all duration-500"
      :class="[isScrolled ? 'bg-white/90 backdrop-blur-md py-3 shadow-lg' : 'bg-transparent py-6']"
    >
      <div class="container mx-auto px-4 md:px-8">
        <div class="flex items-center justify-between">
          
          <!-- Logo & Branding -->
          <NuxtLink to="/" class="flex items-center gap-4 group">
            <div class="relative">
              <div class="absolute -inset-2 bg-gradient-to-tr from-[#2D5A27] to-[#8B7340] rounded-full opacity-0 group-hover:opacity-20 transition-opacity blur-lg"></div>
              <img src="/images/logo.png" alt="Prime Market" class="h-12 md:h-14 w-auto relative z-10 transition-transform duration-500 group-hover:scale-110">
            </div>
            <div class="flex flex-col">
              <h1 class="text-xl md:text-2xl font-black tracking-tighter leading-none" style="font-family: 'Playfair Display', serif;">
                <span style="color: #2D5A27;">PRIME</span>
                <span style="color: #8B7340;" class="ml-1">MARKET</span>
              </h1>
              <span class="text-[9px] md:text-[10px] font-bold uppercase tracking-[0.2em] mt-1 opacity-80" style="color: #2D5A27; font-family: 'DM Sans', sans-serif;">
                De la Terre à Votre Table
              </span>
            </div>
          </NuxtLink>
  
          <!-- Navigation desktop -->
          <div class="hidden md:flex items-center gap-6 lg:gap-8">
            <NuxtLink
              v-for="link in navLinks"
              :key="link.to"
              :to="link.to"
              class="nav-link text-sm font-semibold tracking-wide transition-all duration-200 hover:text-[#2D5A27] relative group py-1 whitespace-nowrap"
              style="color: #4A5D45; font-family: 'DM Sans', sans-serif;"
            >
              {{ link.label }}
              <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#2D5A27] transition-all duration-300 group-hover:w-full"></span>
            </NuxtLink>
          </div>
  
          <!-- Actions (CTA) -->
          <div class="hidden md:flex items-center gap-3 lg:gap-4">
            <ClientOnly>
              <template v-if="!isLoggedIn">
                <NuxtLink
                  to="/login"
                  class="px-6 py-2.5 rounded-full text-[#2D5A27] text-sm font-bold border-2 border-[#2D5A27] transition-all duration-300 hover:bg-[#2D5A27] hover:text-white active:scale-95 shadow-sm whitespace-nowrap"
                  style="font-family: 'DM Sans', sans-serif;"
                >
                  Se connecter
                </NuxtLink>
                <NuxtLink
                  to="/register"
                  class="px-6 py-2.5 rounded-full text-white text-sm font-bold shadow-md transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5 active:scale-95 whitespace-nowrap"
                  style="background-color: #2D5A27; font-family: 'DM Sans', sans-serif;"
                >
                  S'inscrire
                </NuxtLink>
              </template>
              <template v-else>
                <NuxtLink
                  :to="dashboardRoute"
                  class="px-8 py-2.5 rounded-full text-white text-sm font-bold shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5 active:scale-95 flex items-center gap-3 whitespace-nowrap"
                  style="background-color: #2D5A27; font-family: 'DM Sans', sans-serif;"
                >
                  <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                  Tableau de bord
                </NuxtLink>
              </template>
            </ClientOnly>
          </div>
  
          <!-- Mobile Menu Button -->
          <div class="md:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-[#2D5A27]">
              <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
              <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>
      </div>
  
      <!-- Mobile Navigation Overlay -->
      <Transition 
        enter-active-class="transition duration-300 ease-out" 
        enter-from-class="opacity-0 -translate-y-10" 
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-10"
      >
        <div v-if="mobileMenuOpen" class="md:hidden bg-white shadow-2xl border-t border-gray-100 absolute top-full left-0 w-full py-8 px-4 flex flex-col gap-6">
          <NuxtLink 
            v-for="link in navLinks" 
            :key="link.to" 
            :to="link.to"
            class="text-xl font-bold text-gray-800 text-center py-2"
            @click="mobileMenuOpen = false"
          >
            {{ link.label }}
          </NuxtLink>
          <div class="pt-4 px-3 space-y-3">
            <ClientOnly>
              <template v-if="!isLoggedIn">
                <NuxtLink
                  to="/login"
                  class="block w-full text-center px-8 py-3 rounded-full border-2 border-[#2D5A27] text-[#2D5A27] text-base font-bold transition-all active:scale-95"
                  @click="mobileMenuOpen = false"
                >
                  Se connecter
                </NuxtLink>
                <NuxtLink
                  to="/register"
                  class="block w-full text-center px-8 py-3 rounded-full text-white text-base font-bold shadow-md active:scale-95 transition-all"
                  style="background-color: #2D5A27;"
                  @click="mobileMenuOpen = false"
                >
                  S'inscrire
                </NuxtLink>
              </template>
              <NuxtLink
                v-else
                :to="dashboardRoute"
                class="block w-full text-center px-8 py-4 rounded-full text-white text-base font-bold shadow-md active:scale-95 transition-all"
                style="background-color: #2D5A27;"
                @click="mobileMenuOpen = false"
              >
                Tableau de bord
              </NuxtLink>
            </ClientOnly>
          </div>
        </div>
      </Transition>
    </header>
  
    <!-- Main Content Slot -->
    <main class="flex-grow pt-24">
      <slot />
    </main>
  
    <!-- Footer Premium -->
    <footer class="bg-[#1A2E18] text-[#D4E8CC] pt-20 pb-10">
      <div class="container mx-auto px-6 md:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
          <!-- Colonne 1 — Brand -->
          <div class="space-y-6">
            <NuxtLink to="/" class="flex items-center gap-3 group">
              <img src="/images/logo.png" alt="Logo" class="h-12 w-auto bg-white p-1 rounded-xl">
              <div class="flex flex-col">
                <span class="text-xl font-black tracking-tighter leading-none text-white font-serif">PRIME MARKET</span>
                <span class="text-[9px] font-bold text-[#8B7340] uppercase tracking-widest mt-1">De la Terre à Votre Table</span>
              </div>
            </NuxtLink>
            <p class="text-[#8BAF85] text-sm leading-relaxed max-w-xs font-medium">
              La plateforme leader connectant directement les producteurs locaux aux consommateurs et distributeurs du Sénégal.
            </p>
            <!-- Social Icons -->
            <div class="flex items-center gap-4 pt-2">
              <a href="#" class="w-10 h-10 rounded-full bg-[#2D5A27]/30 flex items-center justify-center text-[#8BAF85] hover:bg-[#8B7340] hover:text-white transition-all duration-300">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              </a>
              <a href="#" class="w-10 h-10 rounded-full bg-[#2D5A27]/30 flex items-center justify-center text-[#8BAF85] hover:bg-[#8B7340] hover:text-white transition-all duration-300">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
              </a>
              <a href="#" class="w-10 h-10 rounded-full bg-[#2D5A27]/30 flex items-center justify-center text-[#8BAF85] hover:bg-[#8B7340] hover:text-white transition-all duration-300">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4s1.791-4 4-4 4 1.791 4 4-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
              </a>
            </div>
          </div>

          <!-- Colonne 2 — Plateforme -->
          <div>
            <h4 class="text-[#8B7340] font-black uppercase tracking-widest text-sm mb-8">Plateforme</h4>
            <ul class="space-y-4">
              <li><NuxtLink to="/" class="text-[#8BAF85] hover:text-white transition-colors font-bold text-sm">Accueil</NuxtLink></li>
              <li><NuxtLink to="/catalogue" class="text-[#8BAF85] hover:text-white transition-colors font-bold text-sm">Catalogue produits</NuxtLink></li>
              <li><NuxtLink to="/how-it-works" class="text-[#8BAF85] hover:text-white transition-colors font-bold text-sm">Comment ça marche</NuxtLink></li>
              <li><NuxtLink to="/about" class="text-[#8BAF85] hover:text-white transition-colors font-bold text-sm">À propos</NuxtLink></li>
            </ul>
          </div>

          <!-- Colonne 3 — Rejoindre -->
          <div>
            <h4 class="text-[#8B7340] font-black uppercase tracking-widest text-sm mb-8">Rejoindre</h4>
            <ul class="space-y-4">
              <li><NuxtLink to="/register?role=producteur" class="text-[#8BAF85] hover:text-white transition-colors font-bold text-sm">Devenir Producteur</NuxtLink></li>
              <li><NuxtLink to="/register?role=distributeur" class="text-[#8BAF85] hover:text-white transition-colors font-bold text-sm">Devenir Distributeur</NuxtLink></li>
              <li><NuxtLink to="/register?role=transporteur" class="text-[#8BAF85] hover:text-white transition-colors font-bold text-sm">Devenir Transporteur</NuxtLink></li>
              <li><NuxtLink to="/register?role=consommateur" class="text-[#8BAF85] hover:text-white transition-colors font-bold text-sm">Créer un compte Consommateur</NuxtLink></li>
            </ul>
          </div>

          <!-- Colonne 4 — Support -->
          <div>
            <h4 class="text-[#8B7340] font-black uppercase tracking-widest text-sm mb-8">Support</h4>
            <ul class="space-y-4">
              <li><NuxtLink to="/help" class="text-[#8BAF85] hover:text-white transition-colors font-bold text-sm">Centre d'aide</NuxtLink></li>
              <li><NuxtLink to="/contact" class="text-[#8BAF85] hover:text-white transition-colors font-bold text-sm">Nous contacter</NuxtLink></li>
              <li><NuxtLink to="/report-issue" class="text-[#8BAF85] hover:text-white transition-colors font-bold text-sm">Signaler un problème</NuxtLink></li>
            </ul>
          </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-[#2D5A27] pt-8 flex flex-col md:flex-row justify-between items-center gap-6">
          <div class="text-xs text-[#8BAF85] font-bold">
            © 2026 Prime Market — Groupe 8, UCAD École Supérieure Polytechnique
          </div>
          <div class="flex items-center gap-8 text-xs font-black uppercase tracking-widest">
            <NuxtLink to="/privacy" class="text-[#8BAF85] hover:text-[#D4E8CC] transition-colors">Politique de confidentialité</NuxtLink>
            <NuxtLink to="/terms" class="text-[#8BAF85] hover:text-[#D4E8CC] transition-colors">Conditions d'utilisation</NuxtLink>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
const isScrolled = ref(false)
const mobileMenuOpen = ref(false)
const isLoggedIn = ref(false)
const dashboardRoute = ref('/login')

const navLinks = [
  { label: 'Accueil', to: '/' },
  { label: 'Catalogue', to: '/catalogue' },
  { label: 'Comment ça marche', to: '/how-it-works' },
  { label: 'À propos', to: '/about' },
]

onMounted(() => {
  window.addEventListener('scroll', () => {
    isScrolled.value = window.scrollY > 20
  })

  // Vérifier la session
  const user = localStorage.getItem('user')
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role')

  if (user && token) {
    isLoggedIn.value = true
    dashboardRoute.value = `/${role}`
  }
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;700&display=swap');

body {
  font-family: 'DM Sans', sans-serif;
}

h1, h2, h3, h4 {
  font-family: 'Playfair Display', serif;
}

/* Active link color - only for text links with .nav-link class */
.nav-link.router-link-active {
  color: #2D5A27 !important;
}

/* Ensure buttons stay white even when active */
.rounded-full.router-link-active {
  color: white !important;
}

/* Special case for the outline button (Se connecter) - becomes solid green when active */
.rounded-full.border-2.router-link-active {
  background-color: #2D5A27 !important;
  color: white !important;
}
</style>