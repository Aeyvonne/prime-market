<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- En-tête de bienvenue -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">
          Bonjour, <span class="text-[#2D5A27]">{{ userName }}</span> 👋
        </h1>
        <p class="text-gray-500 font-medium mt-1">Voici ce qui se passe sur votre compte aujourd'hui.</p>
      </div>
      <div class="flex items-center gap-3">
        <NuxtLink to="/catalogue" class="px-6 py-3 bg-[#2D5A27] text-white rounded-2xl font-bold text-sm shadow-lg hover:shadow-[#2D5A27]/20 transition-all hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
          Commander des produits
        </NuxtLink>
      </div>
    </div>

    <!-- Grille de Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="(stat, index) in stats" :key="index" 
        class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-500 group relative overflow-hidden"
      >
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#F5F0E8] rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-0 group-hover:scale-100"></div>
        
        <div class="relative z-10">
          <div :class="`w-12 h-12 rounded-2xl ${stat.bgColor} ${stat.textColor} flex items-center justify-center mb-4 shadow-inner`">
            <component :is="stat.icon" class="w-6 h-6" />
          </div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">{{ stat.label }}</p>
          <div class="flex items-baseline gap-2">
            <h3 class="text-2xl font-black text-gray-900">{{ stat.value }}</h3>
            <span v-if="stat.trend" :class="`text-[10px] font-bold ${stat.trendColor}`">{{ stat.trend }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Tableau des dernières commandes -->
      <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
          <h3 class="text-xl font-black text-gray-900">Dernières Commandes</h3>
          <NuxtLink to="/distributeur/commandes" class="text-xs font-bold text-[#2D5A27] hover:underline">Voir tout →</NuxtLink>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50/50">
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID</th>
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Vendeur</th>
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</th>
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Montant</th>
                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-[#F5F0E8]/30 transition-colors group">
                <td class="px-8 py-5">
                  <span class="text-sm font-bold text-gray-900">#{{ order.id }}</span>
                </td>
                <td class="px-8 py-5">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#2D5A27]/10 flex items-center justify-center text-[#2D5A27] text-xs font-black">
                      {{ order.vendeur?.nom?.[0] || 'V' }}
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ order.vendeur?.nom }} {{ order.vendeur?.prenom }}</span>
                  </div>
                </td>
                <td class="px-8 py-5">
                  <span class="text-sm text-gray-500 font-medium">{{ formatDate(order.date_commande) }}</span>
                </td>
                <td class="px-8 py-5">
                  <span class="text-sm font-black text-gray-900">{{ formatPrice(order.montant_total) }}</span>
                </td>
                <td class="px-8 py-5 text-center">
                  <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-center ${statusStyle(order.status)}`">
                    {{ order.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="recentOrders.length === 0">
                <td colspan="5" class="px-8 py-12 text-center text-gray-400 font-medium italic">
                  Aucune commande récente.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Produits en vedette ou Activité récente -->
      <div class="bg-[#2D5A27] rounded-[2.5rem] shadow-xl p-8 text-white relative overflow-hidden flex flex-col justify-between">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
        
        <div>
          <h3 class="text-2xl font-black font-serif leading-tight mb-6">Prêt à développer votre stock ?</h3>
          <p class="text-white/70 text-sm font-medium leading-relaxed mb-8">
            Explorez les nouveaux arrivages de nos producteurs partenaires et profitez des meilleurs tarifs de saison.
          </p>
          
          <div class="space-y-4 mb-8">
            <div class="flex items-center gap-4 p-4 bg-white/10 rounded-2xl border border-white/10 group hover:bg-white/15 transition-all">
              <div class="w-10 h-10 rounded-xl bg-[#8B7340] flex items-center justify-center text-white text-xl">🌱</div>
              <div>
                <p class="text-xs font-bold">Nouveaux arrivages</p>
                <p class="text-[10px] text-white/50">Fruits et légumes frais</p>
              </div>
              <svg class="ml-auto w-4 h-4 opacity-0 group-hover:opacity-100 -translate-x-2 group-hover:translate-x-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
          </div>
        </div>

        <NuxtLink to="/catalogue" class="w-full py-4 bg-[#8B7340] text-white rounded-2xl font-bold text-sm shadow-xl hover:shadow-[#8B7340]/30 transition-all text-center hover:bg-[#A68B4D]">
          Voir le Catalogue
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

const userName = ref('')
const recentOrders = ref([])
const stats = ref([
  { label: 'Commandes en cours', value: '0', icon: IconOrders, bgColor: 'bg-amber-50', textColor: 'text-amber-600' },
  { label: 'Produits en vente', value: '0', icon: IconProducts, bgColor: 'bg-emerald-50', textColor: 'text-emerald-600' },
  { label: 'Montant total (Mois)', value: '0 FCFA', icon: IconWallet, bgColor: 'bg-blue-50', textColor: 'text-blue-600' },
  { label: 'Livraisons en attente', value: '0', icon: IconTruck, bgColor: 'bg-indigo-50', textColor: 'text-indigo-600' }
])

// Icônes
function IconOrders() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' })])
}
function IconProducts() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' })])
}
function IconWallet() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' })])
}
function IconTruck() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 011 1v2a1 1 0 01-1 1h-1m-4-14H5m4 0v3a2 2 0 11-4 0V7a2 2 0 114 0zM19 17h1a1 1 0 001-1v-3a1 1 0 00-1-1h-1m-1 5a2 2 0 11-4 0 2 2 0 014 0z' })])
}

async function fetchDashboardData() {
  const token = localStorage.getItem('token')
  if (!token) return navigateTo('/login')

  try {
    const [orders, products, deliveries] = await Promise.all([
      $fetch('http://127.0.0.1:8000/api/distributeur/commandes', { headers: { Authorization: `Bearer ${token}` } }),
      $fetch('http://127.0.0.1:8000/api/distributeur/produits', { headers: { Authorization: `Bearer ${token}` } }),
      $fetch('http://127.0.0.1:8000/api/distributeur/livraisons', { headers: { Authorization: `Bearer ${token}` } })
    ])

    // Update Stats
    const currentMonth = new Date().getMonth()
    const totalMonth = orders
      .filter(o => new Date(o.date_commande).getMonth() === currentMonth)
      .reduce((sum, o) => sum + parseFloat(o.montant_total), 0)

    stats.value[0].value = orders.filter(o => o.status === 'EnCours').length
    stats.value[1].value = products.length
    stats.value[2].value = formatPrice(totalMonth)
    stats.value[3].value = deliveries.filter(d => d.status !== 'Livrée').length

    recentOrders.value = orders.slice(0, 5)
  } catch (err) {
    console.error('Erreur chargement dashboard:', err)
  }
}

function statusStyle(status) {
  switch (status) {
    case 'Valider': return 'bg-emerald-100 text-emerald-700'
    case 'Annuler': return 'bg-red-100 text-red-700'
    case 'EnCours': return 'bg-amber-100 text-amber-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

function formatPrice(price) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}

onMounted(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  userName.value = user.prenom || 'Utilisateur'
  fetchDashboardData()
})
</script>