<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">
          Bonjour, <span class="text-[#2D5A27]">{{ userName }}</span>
        </h1>
        <p class="text-gray-500 font-medium mt-1">Trouvez les meilleurs produits frais du Sénégal.</p>
      </div>
      <NuxtLink to="/catalogue"
        class="px-6 py-3 bg-[#2D5A27] text-white rounded-2xl font-bold text-sm shadow-lg hover:shadow-[#2D5A27]/20 transition-all hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        Voir le catalogue
      </NuxtLink>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="(stat, index) in stats" :key="index"
        class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-500 group relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#F5F0E8] rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-0 group-hover:scale-100"></div>
        <div class="relative z-10">
          <div :class="`w-12 h-12 rounded-2xl ${stat.bgColor} ${stat.textColor} flex items-center justify-center mb-4 shadow-inner`">
            <component :is="stat.icon" class="w-6 h-6" />
          </div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">{{ stat.label }}</p>
          <h3 class="text-2xl font-black text-gray-900">{{ stat.value }}</h3>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-8 border-b border-gray-50 flex items-center justify-between">
        <h3 class="text-xl font-black text-gray-900">Dernières Commandes</h3>
        <NuxtLink to="/consommateur/commandes" class="text-xs font-bold text-[#2D5A27] hover:underline">Voir tout →</NuxtLink>
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
              <td class="px-8 py-5"><span class="text-sm font-bold text-gray-900">#{{ order.id }}</span></td>
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-[#2D5A27] flex items-center justify-center text-white text-[10px] font-black">{{ order.vendeur?.nom?.[0] || 'V' }}</div>
                  <span class="text-sm font-bold text-gray-900">{{ order.vendeur?.nom }}</span>
                </div>
              </td>
              <td class="px-8 py-5"><span class="text-sm text-gray-500 font-medium">{{ formatDate(order.date_commande) }}</span></td>
              <td class="px-8 py-5"><span class="text-sm font-black text-gray-900">{{ formatPrice(order.montant_total) }}</span></td>
              <td class="px-8 py-5 text-center">
                <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${statusStyle(order.status)}`">{{ order.status }}</span>
              </td>
            </tr>
            <tr v-if="recentOrders.length === 0">
              <td colspan="5" class="px-8 py-20 text-center text-gray-400 font-medium italic">Aucune commande pour le moment.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const userName = ref('')
const recentOrders = ref([])
const stats = ref([
  { label: 'Commandes en cours', value: '0', icon: IconOrders, bgColor: 'bg-amber-50', textColor: 'text-amber-600' },
  { label: 'Livraisons en attente', value: '0', icon: IconTruck, bgColor: 'bg-indigo-50', textColor: 'text-indigo-600' },
  { label: 'Paiements effectués', value: '0', icon: IconWallet, bgColor: 'bg-emerald-50', textColor: 'text-emerald-600' },
  { label: 'Évaluations données', value: '0', icon: IconStar, bgColor: 'bg-blue-50', textColor: 'text-blue-600' },
])

function IconOrders() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' })])
}
function IconTruck() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 011 1v2a1 1 0 01-1 1h-1m-4-14H5m4 0v3a2 2 0 11-4 0V7a2 2 0 114 0zM19 17h1a1 1 0 001-1v-3a1 1 0 00-1-1h-1m-1 5a2 2 0 11-4 0 2 2 0 014 0z' })])
}
function IconWallet() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' })])
}
function IconStar() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z' })])
}

async function fetchDashboardData() {
  const token = localStorage.getItem('token')
  if (!token) return navigateTo('/login')
  try {
    const [orders, evaluations] = await Promise.all([
      $fetch('http://127.0.0.1:8000/api/consommateur/commandes', { headers: { Authorization: `Bearer ${token}` } }),
      $fetch('http://127.0.0.1:8000/api/consommateur/evaluations', { headers: { Authorization: `Bearer ${token}` } })
    ])
    stats.value[0].value = orders.filter(o => o.status === 'EnCours').length
    stats.value[1].value = orders.filter(o => o.status !== 'Livrer' && o.status !== 'Annuler' && o.status !== 'Valider').length
    stats.value[2].value = orders.filter(o => o.paiement).length
    stats.value[3].value = evaluations.length
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
    case 'Livrer': return 'bg-blue-100 text-blue-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
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
