<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div>
      <h1 class="text-3xl font-black text-gray-900 font-serif">Mes Rémunérations</h1>
      <p class="text-gray-500 font-medium mt-1">Suivez vos gains par mois.</p>
    </div>

    <!-- Total du mois en grand -->
    <div class="bg-gradient-to-br from-[#2D5A27] to-[#1e3d1a] text-white px-8 py-8 rounded-[2.5rem] shadow-xl">
      <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-70">Total du mois</p>
      <p class="text-4xl md:text-5xl font-black mt-2">{{ formatPrice(totalMois) }}</p>
      <p class="text-sm opacity-60 mt-2">{{ moisLabel }}</p>
    </div>

    <!-- Filtre par mois -->
    <div class="flex flex-wrap gap-2">
      <button v-for="m in moisDisponibles" :key="m.value"
        @click="moisActif = m.value"
        :class="`px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all ${
          moisActif === m.value
            ? 'bg-[#2D5A27] text-white shadow-lg'
            : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-200'
        }`">
        {{ m.label }}
      </button>
    </div>

    <!-- Liste des missions terminées -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-8 border-b border-gray-50">
        <h3 class="text-xl font-black text-gray-900">Missions terminées</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Trajet</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Montant</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in filteredListe" :key="item.id" class="hover:bg-[#F5F0E8]/30 transition-colors">
              <td class="px-8 py-5"><span class="text-sm text-gray-500 font-medium">{{ formatDate(item.updated_at) }}</span></td>
              <td class="px-8 py-5">
                <span class="text-sm text-gray-600">{{ trajetLabel(item) }}</span>
              </td>
              <td class="px-8 py-5"><span class="text-sm font-black text-[#2D5A27]">{{ formatPrice(item.remuneration) }}</span></td>
              <td class="px-8 py-5 text-center">
                <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${statusStyle(item.status)}`">{{ item.status }}</span>
              </td>
            </tr>
            <tr v-if="filteredListe.length === 0">
              <td colspan="4" class="px-8 py-20 text-center text-gray-400 font-medium italic">Aucune rémunération pour cette période.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const totalMois = ref(0)
const totalGlobal = ref(0)
const liste = ref([])
const moisActif = ref('all')
const userId = ref(null)

const moisDisponibles = computed(() => {
  const mois = []
  if (liste.value.length > 0) {
    const dates = [...new Set(liste.value.map(i => {
      if (!i.updated_at) return null
      const d = new Date(i.updated_at)
      return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`
    }).filter(Boolean))]
    dates.sort().reverse()
    mois.push({ value: 'all', label: 'Tout' })
    dates.forEach(m => {
      const [year, month] = m.split('-')
      const label = new Date(year, month - 1).toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' })
      mois.push({ value: m, label })
    })
  } else {
    mois.push({ value: 'all', label: 'Tout' })
  }
  return mois
})

const filteredListe = computed(() => {
  if (moisActif.value === 'all') return liste.value
  return liste.value.filter(item => {
    if (!item.updated_at) return false
    const d = new Date(item.updated_at)
    const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`
    return key === moisActif.value
  })
})

watch(moisActif, () => {
  const items = filteredListe.value.filter(i => i.status === 'Valider')
  totalMois.value = items.reduce((sum, i) => sum + parseFloat(i.remuneration || 0), 0)
})

const moisLabel = computed(() => {
  if (moisActif.value === 'all') return 'Toutes les missions terminées'
  const [year, month] = moisActif.value.split('-')
  return new Date(year, month - 1).toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' })
})

function trajetLabel(m) {
  const v = m.livraison?.commande?.vendeur
  const depart = v?.adresse || v?.nom || 'Départ'
  const arrivee = m.livraison?.adresse_livraison || 'Arrivée'
  return `${depart} → ${arrivee}`
}

async function fetchRemunerations() {
  const token = localStorage.getItem('token')
  if (!token) return navigateTo('/login')
  try {
    const res = await $fetch('http://127.0.0.1:8000/api/transporteur/remunerations', {
      headers: { Authorization: `Bearer ${token}` }
    })
    totalGlobal.value = res.total || 0
    liste.value = res.liste || []
    const valides = liste.value.filter(i => i.status === 'Valider')
    totalMois.value = valides.reduce((sum, i) => sum + parseFloat(i.remuneration || 0), 0)
  } catch (err) {
    console.error('Erreur chargement rémunérations:', err)
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
    userId.value = user.id
  }
  fetchRemunerations()
})
</script>
