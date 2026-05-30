<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div>
      <h1 class="text-3xl font-black text-gray-900 font-serif">Missions</h1>
      <p class="text-gray-500 font-medium mt-1">Consultez les missions disponibles et gérez les vôtres.</p>
    </div>

    <!-- Onglets -->
    <div class="flex gap-2 border-b border-gray-200 pb-4">
      <button v-for="tab in ['disponibles', 'mes-missions']" :key="tab"
        @click="ongletActif = tab"
        :class="`px-6 py-3 rounded-xl text-sm font-bold uppercase tracking-wider transition-all ${
          ongletActif === tab
            ? 'bg-[#2D5A27] text-white shadow-lg'
            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'
        }`">
        {{ tab === 'disponibles' ? 'Missions disponibles' : 'Mes missions en cours' }}
      </button>
    </div>

    <!-- Missions disponibles -->
    <div v-if="ongletActif === 'disponibles'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="mission in missionsDisponibles" :key="mission.id"
        class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-start justify-between mb-3">
          <h3 class="text-lg font-black text-gray-900">Mission #{{ mission.id }}</h3>
          <span class="text-lg font-black text-[#2D5A27]">{{ formatPrice(mission.remuneration) }}</span>
        </div>

          <div class="bg-[#F5F0E8]/50 rounded-xl p-4 mb-4 space-y-2 text-sm">
            <div class="flex items-start gap-2">
              <span class="w-2 h-2 mt-1.5 rounded-full bg-[#2D5A27] flex-shrink-0" />
              <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Départ</span>
                <p class="font-bold text-gray-900">{{ adresseDepart(mission) }}</p>
              </div>
            </div>
            <div class="flex items-start gap-2">
              <span class="w-2 h-2 mt-1.5 rounded-full bg-[#8B7340] flex-shrink-0" />
              <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Arrivée</span>
                <p class="font-bold text-gray-900">{{ mission.livraison?.adresse_livraison || '-' }}</p>
              </div>
            </div>
            <div class="flex items-start gap-2">
              <span class="w-2 h-2 mt-1.5 rounded-full bg-emerald-500 flex-shrink-0" />
              <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Produit</span>
                <p class="font-bold text-gray-900">{{ mission.noms_produits || '-' }}</p>
              </div>
            </div>
          </div>

        <button @click="ouvrirModalAcceptation(mission.id)"
          class="w-full px-4 py-2.5 bg-emerald-500 text-white rounded-xl text-xs font-bold hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-200 active:scale-95">
          Accepter
        </button>
      </div>

      <!-- Modal date livraison -->
      <div v-if="modalAcceptation" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="modalAcceptation = false">
        <div class="bg-white rounded-[2rem] p-8 max-w-md mx-4 shadow-2xl w-full">
          <h3 class="text-xl font-black text-gray-900 font-serif mb-2">Accepter la mission</h3>
          <p class="text-sm text-gray-500 mb-6">Veuillez indiquer la date de livraison prévue.</p>
          <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Date de livraison estimée</label>
          <input type="date" v-model="dateLivraisonPrevue"
            class="mt-1 block w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:border-[#2D5A27] focus:ring-1 focus:ring-[#2D5A27] outline-none mb-6" />
          <p v-if="erreurDate" class="text-xs text-red-500 -mt-4 mb-4">{{ erreurDate }}</p>
          <div class="flex gap-3">
            <button @click="modalAcceptation = false"
              class="flex-1 px-4 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 transition-all">
              Annuler
            </button>
            <button @click="accepterMission"
              class="flex-1 px-4 py-3 bg-emerald-500 text-white rounded-xl text-sm font-bold hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-200">
              Confirmer
            </button>
          </div>
        </div>
      </div>

      <div v-if="missionsDisponibles.length === 0" class="md:col-span-2 text-center py-20">
        <p class="text-gray-400 font-medium italic">Aucune mission disponible pour le moment.</p>
      </div>
    </div>

    <!-- Mes missions -->
    <div v-if="ongletActif === 'mes-missions'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="mission in mesMissions" :key="mission.id"
        class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-start justify-between mb-3">
          <div>
            <h3 class="text-lg font-black text-gray-900">Mission #{{ mission.id }}</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(mission.created_at) }}</p>
          </div>
          <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${statusStyle(mission.status)}`">{{ mission.status }}</span>
        </div>

        <div class="bg-[#F5F0E8]/50 rounded-xl p-4 mb-4 space-y-1.5 text-sm">
          <p><span class="font-bold text-gray-900">Trajet :</span> <span class="text-gray-600">{{ trajetLabel(mission) }}</span></p>
          <p><span class="font-bold text-gray-900">Produit :</span> <span class="text-gray-600">{{ mission.noms_produits || '-' }}</span></p>
          <p v-if="mission.remuneration"><span class="font-bold text-gray-900">Rémunération :</span> <span class="text-[#2D5A27] font-black">{{ formatPrice(mission.remuneration) }}</span></p>
        </div>

        <div class="flex gap-2">
          <NuxtLink :to="`/transporteur/missions/${mission.id}`"
            class="flex-1 text-center px-4 py-2.5 bg-[#2D5A27]/10 text-[#2D5A27] rounded-xl text-xs font-bold hover:bg-[#2D5A27]/20 transition-all">
            Voir détails
          </NuxtLink>
          <button @click="refuserMission(mission.id)"
            class="px-4 py-2.5 bg-red-500 text-white rounded-xl text-xs font-bold hover:bg-red-600 transition-all shadow-lg shadow-red-200 active:scale-95">
            Refuser
          </button>
        </div>
      </div>

      <div v-if="mesMissions.length === 0" class="md:col-span-2 text-center py-20">
        <p class="text-gray-400 font-medium italic">Vous n'avez aucune mission en cours.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const missions = ref([])
const ongletActif = ref('disponibles')
const userId = ref(null)
const modalAcceptation = ref(false)
const missionAccepteeId = ref(null)
const dateLivraisonPrevue = ref('')
const erreurDate = ref('')

const missionsDisponibles = computed(() =>
  missions.value.filter(m => m.transporteur_id === null && m.status === 'EnCours')
)

const mesMissions = computed(() =>
  missions.value.filter(m => m.transporteur_id === userId.value)
)

function trajetLabel(m) {
  const v = m.livraison?.commande?.vendeur
  const depart = v?.adresse || v?.nom || 'Départ'
  const arrivee = m.livraison?.adresse_livraison || 'Arrivée'
  return `${depart} → ${arrivee}`
}

function adresseDepart(m) {
  const v = m.livraison?.commande?.vendeur
  return v?.adresse || v?.nom || 'Non spécifiée'
}

async function fetchMissions() {
  const token = localStorage.getItem('token')
  if (!token) return navigateTo('/login')
  try {
    missions.value = await $fetch('http://127.0.0.1:8000/api/transporteur/missions', {
      headers: { Authorization: `Bearer ${token}` }
    })
  } catch (err) {
    console.error('Erreur chargement missions:', err)
  }
}

function ouvrirModalAcceptation(id) {
  missionAccepteeId.value = id
  dateLivraisonPrevue.value = ''
  erreurDate.value = ''
  modalAcceptation.value = true
}

async function accepterMission() {
  if (!dateLivraisonPrevue.value) {
    erreurDate.value = 'Veuillez choisir une date de livraison.'
    return
  }
  erreurDate.value = ''
  const token = localStorage.getItem('token')
  try {
    await $fetch(`http://127.0.0.1:8000/api/transporteur/missions/${missionAccepteeId.value}/accepter`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` },
      body: { date_livraison_estimee: dateLivraisonPrevue.value }
    })
    modalAcceptation.value = false
    await fetchMissions()
  } catch (err) {
    const msg = err.data?.message || "Erreur lors de l'acceptation."
    erreurDate.value = msg
  }
}

async function refuserMission(id) {
  const token = localStorage.getItem('token')
  try {
    await $fetch(`http://127.0.0.1:8000/api/transporteur/missions/${id}/refuser`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` }
    })
    await fetchMissions()
  } catch (err) {
    console.error('Erreur refus:', err)
  }
}

function getNomsProduits(mission) {
  const lignes = mission.livraison?.commande?.lignes_commande
  if (!lignes || lignes.length === 0) return null
  const noms = [...new Set(lignes.map(l => l.produit?.nom || '').filter(Boolean))]
  return noms.join(', ') || null
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
  fetchMissions()
})
</script>
