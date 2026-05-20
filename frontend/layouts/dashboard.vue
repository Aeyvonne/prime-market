<template>
  <div class="min-h-screen bg-[#F5F0E8] flex">
    <!-- Sidebar -->
    <ClientOnly>
      <aside 
        class="fixed inset-y-0 left-0 z-50 w-72 bg-[#2D5A27] text-white transform transition-transform duration-300 lg:translate-x-0 shadow-2xl"
        :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full']"
      >
        <!-- Sidebar Header (Logo) -->
        <div class="flex items-center h-24 px-6 border-b border-white/10">
          <NuxtLink to="/" class="flex items-center gap-3 group">
            <div class="bg-white p-2 rounded-xl transition-transform duration-500 group-hover:rotate-12">
              <img src="/images/logo.png" alt="Logo" class="h-10 w-auto">
            </div>
            <div class="flex flex-col">
              <span class="text-xl font-black tracking-tighter leading-none font-serif">PRIME</span>
              <span class="text-[10px] uppercase tracking-widest opacity-60 font-bold text-[#8B7340]">MARKET</span>
            </div>
          </NuxtLink>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-grow py-8 px-4 overflow-y-auto space-y-2 h-[calc(100vh-160px)] custom-scrollbar">
          <div v-for="item in currentNav" :key="item.to">
            <NuxtLink 
              :to="item.to"
              class="flex items-center justify-between px-4 py-3.5 rounded-xl transition-all duration-200 group relative"
              :class="[isActive(item.to) ? 'bg-white text-[#2D5A27] shadow-lg' : 'hover:bg-white/5 text-white/80']"
            >
              <div class="flex items-center gap-4">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg transition-colors"
                  :class="[isActive(item.to) ? 'bg-[#2D5A27]/10' : 'bg-white/5 group-hover:bg-white/10']"
                >
                  <component :is="item.icon" class="w-5 h-5" />
                </span>
                <span class="text-sm font-bold tracking-wide">{{ item.label }}</span>
              </div>
              
              <!-- Badge optionnel -->
              <span v-if="item.badge" class="px-2 py-0.5 rounded-full bg-[#8B7340] text-white text-[10px] font-black">
                {{ item.badge }}
              </span>

              <!-- Indicateur actif -->
              <div v-if="isActive(item.to)" class="absolute right-0 w-1 h-6 bg-[#2D5A27] rounded-l-full"></div>
            </NuxtLink>
          </div>
        </nav>

        <!-- Logout (Optionnel en bas de sidebar aussi si nécessaire) -->
        <div class="p-6 border-t border-white/10">
          <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
            <p class="text-[10px] font-bold text-[#8B7340] uppercase tracking-widest mb-1">Aide & Support</p>
            <p class="text-[11px] text-white/60 leading-relaxed mb-3">Besoin d'aide ? Consultez notre guide ou contactez-nous.</p>
            <NuxtLink to="/help" class="text-xs font-bold text-white hover:underline">Accéder au centre d'aide →</NuxtLink>
          </div>
        </div>
      </aside>
    </ClientOnly>

    <!-- Main Content -->
    <main class="flex-grow lg:ml-72 flex flex-col min-h-screen">
      <!-- Top Navbar (Minimaliste après connexion) -->
      <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-40 px-4 sm:px-8 flex items-center justify-between shadow-sm">
        
        <!-- Left: Toggle & Breadcrumb -->
        <div class="flex items-center gap-6">
          <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-xl transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
          </button>
          
          <div class="hidden sm:flex flex-col">
            <nav class="flex text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">
              <span>Prime Market</span>
              <span class="mx-2">/</span>
              <span class="text-[#2D5A27]">{{ currentPageTitle }}</span>
            </nav>
            <h2 class="text-xl font-black text-gray-800 leading-none">{{ currentPageTitle }}</h2>
          </div>
        </div>

        <!-- Center: Search Bar -->
        <div class="hidden md:flex flex-grow max-w-md mx-8">
          <div class="relative w-full group">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
              <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#2D5A27] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input 
              type="text" 
              placeholder="Rechercher un produit, une commande..." 
              class="w-full pl-12 pr-4 py-2.5 bg-gray-100/50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-[#2D5A27] outline-none transition-all text-sm font-medium"
            >
          </div>
        </div>

        <!-- Right: Notifications & Profile -->
        <div class="flex items-center gap-3 sm:gap-6">
          <ClientOnly>
            <!-- Notifications -->
            <button class="relative p-2.5 text-gray-500 hover:bg-gray-100 rounded-2xl transition-colors group">
              <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
              <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
            </button>
            
            <!-- Profile Dropdown -->
            <div class="relative group">
              <button 
                @click="profileMenuOpen = !profileMenuOpen"
                class="flex items-center gap-3 pl-3 py-1.5 pr-1.5 bg-[#F5F0E8]/50 hover:bg-[#F5F0E8] rounded-2xl border border-gray-100 transition-all active:scale-95"
              >
                <div class="text-right hidden sm:block">
                  <p class="text-xs font-black text-gray-900 leading-none">{{ userName }} {{ userPrenom }}</p>
                  <p class="text-[9px] font-bold text-[#8B7340] uppercase tracking-tighter mt-1">{{ userRoleDisplay }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-[#2D5A27] flex items-center justify-center text-white font-black text-lg shadow-lg border-2 border-white">
                  {{ userInitials }}
                </div>
              </button>

              <!-- Dropdown Menu -->
              <div 
                v-if="profileMenuOpen"
                class="absolute right-0 mt-3 w-56 bg-white rounded-3xl shadow-2xl border border-gray-100 py-3 z-50 overflow-hidden"
                @click.away="profileMenuOpen = false"
              >
                <div class="px-6 py-4 border-b border-gray-50">
                  <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Session</p>
                  <p class="text-sm font-black text-gray-800 truncate">{{ userEmail }}</p>
                </div>
                <div class="p-2">
                  <NuxtLink to="/profil" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-700 hover:bg-[#F5F0E8] hover:text-[#2D5A27] rounded-2xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Mon profil
                  </NuxtLink>
                </div>
                <div class="mt-2 p-2 border-t border-gray-50">
                  <button @click="handleLogout" class="flex items-center gap-3 w-full px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-50 rounded-2xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Se déconnecter
                  </button>
                </div>
              </div>
            </div>
          </ClientOnly>
        </div>
      </header>

      <!-- Page Content -->
      <div class="p-4 sm:p-8 flex-grow">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
const sidebarOpen = ref(false)
const profileMenuOpen = ref(false)
const route = useRoute()
const userName = ref('Utilisateur')
const userPrenom = ref('')
const userEmail = ref('')
const userRole = ref('consommateur')

const userRoleDisplay = computed(() => {
  const roles = {
    producteur: 'Producteur',
    distributeur: 'Distributeur',
    consommateur: 'Consommateur',
    transporteur: 'Transporteur',
    admin_sectoriel: 'Admin Sectoriel',
    super_administrateur: 'Super Admin'
  }
  return roles[userRole.value] || 'Utilisateur'
})

const userInitials = computed(() => {
  return (userName.value?.[0] || 'U').toUpperCase()
})

const currentPageTitle = computed(() => {
  const path = route.path.split('/').pop()
  if (!path || path === userRole.value) return 'Tableau de bord'
  
  const titles = {
    produits: 'Mes Produits',
    catalogue: 'Catalogue',
    commandes: 'Commandes',
    ventes: 'Mes Ventes',
    paiements: 'Paiements',
    profil: 'Mon Profil',
    parametres: 'Paramètres',
    missions: 'Missions',
    carte: 'Carte & Itinéraires',
    livraisons: 'Livraisons',
    remunerations: 'Rémunérations',
    favoris: 'Favoris'
  }
  return titles[path] || 'Espace Personnel'
})

function isActive(to) {
  return route.path === to
}

// Icônes SVG
const IconDashboard = h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z' })])
const IconProducts = h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' })])
const IconOrders = h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' })])
const IconStats = h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' })])
const IconSettings = h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }), h('circle', { cx: '12', cy: '12', r: '3', 'stroke-width': '2' })])
const IconProfile = h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' })])
const IconTruck = h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4' })])
const IconMap = h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 4L9 7' })])
const IconWallet = h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' })])

const navByRole = {
  producteur: [
    { label: 'Tableau de bord', to: '/producteur', icon: IconDashboard },
    { label: 'Mes produits', to: '/producteur/produits', icon: IconProducts },
    { label: 'Commandes reçues', to: '/producteur/commandes', icon: IconOrders, badge: '5' },
    { label: 'Mes ventes', to: '/producteur/ventes', icon: IconStats },
    { label: 'Paiements reçus', to: '/producteur/paiements', icon: IconWallet },
    { label: 'Mon profil', to: '/profil', icon: IconProfile },
  ],
  distributeur: [
    { label: 'Tableau de bord', to: '/distributeur', icon: IconDashboard },
    { label: 'Catalogue', to: '/catalogue', icon: IconMap },
    { label: 'Mes produits', to: '/distributeur/produits', icon: IconProducts },
    { label: 'Commandes', to: '/distributeur/commandes', icon: IconOrders },
    { label: 'Livraisons', to: '/distributeur/livraisons', icon: IconTruck },
    { label: 'Paiements', to: '/distributeur/paiements', icon: IconWallet },
    { label: 'Mon profil', to: '/profil', icon: IconProfile },
  ],
  consommateur: [
    { label: 'Tableau de bord', to: '/consommateur', icon: IconDashboard },
    { label: 'Catalogue', to: '/catalogue', icon: IconMap },
    { label: 'Mes commandes', to: '/consommateur/commandes', icon: IconOrders },
    { label: 'Suivi livraison', to: '/consommateur/livraisons', icon: IconTruck },
    { label: 'Mes paiements', to: '/consommateur/paiements', icon: IconWallet },
    { label: 'Mon profil', to: '/profil', icon: IconProfile },
  ],
  transporteur: [
    { label: 'Tableau de bord', to: '/transporteur', icon: IconDashboard },
    { label: 'Missions', to: '/transporteur/missions', icon: IconTruck, badge: '3' },
    { label: 'Carte & Itinéraires', to: '/transporteur/carte', icon: IconMap },
    { label: 'Livraisons', to: '/transporteur/livraisons', icon: IconOrders },
    { label: 'Rémunérations', to: '/transporteur/remunerations', icon: IconWallet },
    { label: 'Mon profil', to: '/profil', icon: IconProfile },
  ],
  super_administrateur: [
    { label: 'Tableau de bord', to: '/admin/super', icon: IconDashboard },
    { label: 'Tous les comptes', to: '/admin/super/comptes', icon: IconProfile },
    { label: 'Catégories', to: '/admin/super/categories', icon: IconProducts },
    { label: 'Transactions', to: '/admin/super/transactions', icon: IconWallet },
    { label: 'Statistiques', to: '/admin/super/statistiques', icon: IconStats },
    { label: 'Mon profil', to: '/profil', icon: IconProfile },
  ]
}

const currentNav = computed(() => {
  return navByRole[userRole.value] || navByRole.consommateur
})

onMounted(() => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    const user = JSON.parse(userStr)
    userName.value = user.nom || 'Utilisateur'
    userPrenom.value = user.prenom || ''
    userEmail.value = user.email || ''
    userRole.value = user.role || 'consommateur'
  }
})

function handleLogout() {
  localStorage.clear()
  navigateTo('/login')
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}
</style>