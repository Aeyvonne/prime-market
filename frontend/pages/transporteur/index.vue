<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">
          Bonjour, <span class="text-[#2D5A27]">{{ userName }}</span>
        </h1>
        <p class="text-gray-500 font-medium mt-1">Gérez vos missions et livraisons en temps réel.</p>
      </div>
      <NuxtLink to="/transporteur/missions"
        class="px-6 py-3 bg-[#2D5A27] text-white rounded-2xl font-bold text-sm shadow-lg hover:shadow-[#2D5A27]/20 transition-all hover:-translate-y-0.5 active:scale-95">
        Voir les missions disponibles
      </NuxtLink>
    </div>

    <!-- Toggle Disponibilité -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 flex items-center justify-between">
      <div>
        <p class="text-sm font-bold text-gray-900">Disponibilité</p>
        <p class="text-xs text-gray-500 mt-0.5">Les transporteurs disponibles reçoivent plus de missions.</p>
      </div>
      <button @click="toggleDisponibilite"
        :class="`relative w-16 h-8 rounded-full transition-all duration-300 ${
          disponible ? 'bg-emerald-500 shadow-lg shadow-emerald-200' : 'bg-red-400 shadow-lg shadow-red-200'
        }`">
        <span class="absolute top-1 left-1 w-6 h-6 bg-white rounded-full shadow transition-transform duration-300"
          :class="disponible ? 'translate-x-8' : 'translate-x-0'" />
        <span class="absolute inset-0 flex items-center justify-center text-[9px] font-black text-white uppercase tracking-wider pointer-events-none">
          {{ disponible ? 'Oui' : 'Non' }}
        </span>
      </button>
    </div>

    <!-- 4 Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="(stat, i) in stats" :key="i"
        class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-500 group relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#F5F0E8] rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-0 group-hover:scale-100"></div>
        <div class="relative z-10">
          <div :class="`w-12 h-12 rounded-2xl ${stat.bg} ${stat.color} flex items-center justify-center mb-4 shadow-inner`">
            <component :is="stat.icon" class="w-6 h-6" />
          </div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">{{ stat.label }}</p>
          <h3 class="text-2xl font-black text-gray-900">{{ stat.value }}</h3>
        </div>
      </div>
    </div>

    <!-- Missions récentes -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-8 border-b border-gray-50 flex items-center justify-between">
        <h3 class="text-xl font-black text-gray-900">Missions récentes</h3>
        <NuxtLink to="/transporteur/missions" class="text-xs font-bold text-[#2D5A27] hover:underline">Voir tout →</NuxtLink>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Mission</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Trajet</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Rémunération</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="m in recentMissions" :key="m.id" class="hover:bg-[#F5F0E8]/30 transition-colors cursor-pointer" @click="navigateTo(`/transporteur/missions/${m.id}`)">
              <td class="px-8 py-5"><span class="text-sm font-bold text-gray-900">#{{ m.id }}</span></td>
              <td class="px-8 py-5">
                <span class="text-sm text-gray-500 font-medium">{{ trajetLabel(m) }}</span>
              </td>
              <td class="px-8 py-5"><span class="text-sm font-black text-[#2D5A27]">{{ formatPrice(m.remuneration) }}</span></td>
              <td class="px-8 py-5 text-center">
                <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${statusStyle(m.status)}`">{{ m.status }}</span>
              </td>
            </tr>
            <tr v-if="recentMissions.length === 0">
              <td colspan="4" class="px-8 py-20 text-center text-gray-400 font-medium italic">Aucune mission pour le moment.</td>
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
const userId = ref(null)
const disponible = ref(true)
const recentMissions = ref([])
const stats = ref([
  { label: 'Missions en cours', value: '0', icon: IconTruck, bg: 'bg-amber-50', color: 'text-amber-600' },
  { label: 'Missions terminées', value: '0', icon: IconCheck, bg: 'bg-emerald-50', color: 'text-emerald-600' },
  { label: 'Livraisons du jour', value: '0', icon: IconOrders, bg: 'bg-indigo-50', color: 'text-indigo-600' },
  { label: 'Rémunération du mois', value: '0 F', icon: IconWallet, bg: 'bg-blue-50', color: 'text-blue-600' },
])

function IconTruck() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 011 1v2a1 1 0 01-1 1h-1m-4-14H5m4 0v3a2 2 0 11-4 0V7a2 2 0 114 0zM19 17h1a1 1 0 001-1v-3a1 1 0 00-1-1h-1m-1 5a2 2 0 11-4 0 2 2 0 014 0z' })])
}
function IconCheck() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' })])
}
function IconOrders() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' })])
}
function IconWallet() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' })])
}
function IconToggle() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' })])
}

function trajetLabel(m) {
  const vendeur = m.livraison?.commande?.vendeur
  const adr = m.livraison?.adresse_livraison
  const depart = vendeur?.adresse || vendeur?.nom || 'Départ'
  const arrivee = adr || 'Arrivée'
  return `${depart} → ${arrivee}`
}

async function toggleDisponibilite() {
  const token = localStorage.getItem('token')
  try {
    const res = await $fetch('http://127.0.0.1:8000/api/transporteur/disponibilite', {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` },
      body: { disponible: !disponible.value },
    })
    disponible.value = res.disponibilite
  } catch (err) {
    console.error('Erreur disponibilité:', err)
  }
}

async function fetchDashboardData() {
  const token = localStorage.getItem('token')
  if (!token) return navigateTo('/login')
  try {
    const [missions, remunerations, livraisons] = await Promise.all([
      $fetch('http://127.0.0.1:8000/api/transporteur/missions', { headers: { Authorization: `Bearer ${token}` } }),
      $fetch('http://127.0.0.1:8000/api/transporteur/remunerations', { headers: { Authorization: `Bearer ${token}` } }),
      $fetch('http://127.0.0.1:8000/api/transporteur/livraisons', { headers: { Authorization: `Bearer ${token}` } }),
    ])
    const mesMissions = missions.filter(m => m.transporteur_id === userId.value)
    stats.value[0].value = mesMissions.filter(m => m.status === 'EnCours').length
    stats.value[1].value = mesMissions.filter(m => m.status === 'Valider').length
    const today = new Date().toISOString().split('T')[0]
    stats.value[2].value = livraisons.filter(l => {
      const d = l.date_livraison_estimee
      return d === today
    }).length || '0'
    stats.value[3].value = formatPrice(remunerations.total_mois || 0)
    recentMissions.value = mesMissions.slice(0, 5)
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

function formatPrice(price) {
  if (!price) return '0 F'
  return new Intl.NumberFormat('fr-FR').format(price) + ' F'
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(() => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    const user = JSON.parse(userStr)
    userName.value = user.prenom || user.nom || 'Transporteur'
    userId.value = user.id
  }
  fetchDashboardData()
})
</script>
