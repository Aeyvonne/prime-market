<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <NuxtLink to="/transporteur/missions" class="inline-flex items-center gap-2 text-sm font-bold text-[#2D5A27] hover:underline">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Retour aux missions
    </NuxtLink>

    <div v-if="mission" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
      <div class="flex items-start justify-between mb-8">
        <div>
          <h1 class="text-3xl font-black text-gray-900 font-serif">Mission #{{ mission.id }}</h1>
          <p class="text-gray-500 font-medium mt-1">Créée le {{ formatDate(mission.created_at) }}</p>
        </div>
        <span :class="`inline-flex px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider ${statusStyle(mission.status)}`">{{ mission.status }}</span>
      </div>

      <!-- Trajet -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-[#F5F0E8]/50 rounded-2xl p-6">
          <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Adresse de départ</h3>
          <p class="text-sm font-bold text-gray-900">{{ adresseDepart }}</p>
          <p v-if="vendeurNom" class="text-xs text-gray-500 mt-1">{{ vendeurNom }}</p>
        </div>

        <div class="bg-[#F5F0E8]/50 rounded-2xl p-6">
          <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Adresse d'arrivée</h3>
          <p class="text-sm font-bold text-gray-900">{{ mission.livraison?.adresse_livraison || '-' }}</p>
        </div>
      </div>

      <!-- Infos produit et rémunération -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-[#F5F0E8]/30 rounded-2xl p-5 border border-[#F5F0E8]">
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Produit</p>
          <p class="text-sm font-bold text-gray-900">{{ mission.noms_produits || '-' }}</p>
        </div>
        <div class="bg-[#2D5A27]/5 rounded-2xl p-5 border border-[#2D5A27]/10">
          <p class="text-[10px] font-black text-[#2D5A27] uppercase tracking-[0.2em] mb-2">Rémunération</p>
          <p class="text-xl font-black text-[#2D5A27]">{{ formatPrice(mission.remuneration) }}</p>
        </div>
      </div>

      <!-- Liste des articles -->
      <div v-if="mission.livraison?.commande?.lignes_commande?.length" class="bg-white border border-gray-200 rounded-2xl p-6 mb-8">
        <h3 class="text-lg font-black text-gray-900 mb-4">Articles à livrer</h3>
        <div class="divide-y divide-gray-100">
          <div v-for="ligne in mission.livraison.commande.lignes_commande" :key="ligne.id" class="flex items-center justify-between py-3">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#F5F0E8] flex items-center justify-center text-lg overflow-hidden">
                <img v-if="ligne.produit?.photo" :src="`http://127.0.0.1:8000/storage/products/${ligne.produit.photo}`" class="w-full h-full object-cover" />
                <span v-else class="text-base">📦</span>
              </div>
              <div>
                <p class="text-sm font-bold text-gray-900">{{ ligne.produit?.nom || 'Produit #' + ligne.produit_id }}</p>
                <p class="text-xs text-gray-500">× {{ ligne.quantite }}</p>
              </div>
            </div>
            <span class="text-sm font-bold text-gray-900">{{ formatPrice(ligne.prix_unitaire * ligne.quantite) }}</span>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex gap-3 flex-wrap" v-if="mission.transporteur_id === null">
        <div class="w-full mb-2">
          <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Date de livraison prévue <span class="text-red-500">*</span></label>
          <input type="date" v-model="dateLivraisonPrevue" required
            class="mt-1 block w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:border-[#2D5A27] focus:ring-1 focus:ring-[#2D5A27] outline-none" />
          <p v-if="erreurDate" class="text-xs text-red-500 mt-1">{{ erreurDate }}</p>
        </div>
        <button @click="accepterMission"
          class="px-8 py-3.5 bg-emerald-500 text-white rounded-2xl font-bold text-sm hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-200 active:scale-95">
          Accepter la mission
        </button>
      </div>
      <div v-else-if="mission.transporteur_id === userId" class="space-y-3">
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-center">
          <p class="text-sm font-bold text-amber-700">Vous avez accepté cette mission.</p>
        </div>
        <button @click="refuserMission"
          class="w-full px-8 py-3.5 bg-red-500 text-white rounded-2xl font-bold text-sm hover:bg-red-600 transition-all shadow-lg shadow-red-200 active:scale-95">
          Libérer la mission
        </button>
      </div>
    </div>

    <div v-if="!mission && !loading" class="text-center py-20">
      <p class="text-gray-400 font-medium italic">Mission non trouvée.</p>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const route = useRoute()
const mission = ref(null)
const loading = ref(true)
const userId = ref(null)
const dateLivraisonPrevue = ref('')
const erreurDate = ref('')

const adresseDepart = computed(() => {
  const v = mission.value?.livraison?.commande?.vendeur
  return v?.adresse || v?.nom || 'Non spécifiée'
})

const vendeurNom = computed(() => {
  const v = mission.value?.livraison?.commande?.vendeur
  return v ? `${v.prenom || ''} ${v.nom || ''}`.trim() || null : null
})

const nomsProduits = computed(() => {
  const lignes = mission.value?.livraison?.commande?.lignes_commande
  if (!lignes || lignes.length === 0) return null
  const noms = [...new Set(lignes.map(l => l.produit?.nom || '').filter(Boolean))]
  return noms.join(', ') || null
})


async function fetchMission() {
  const token = localStorage.getItem('token')
  if (!token) return navigateTo('/login')
  try {
    mission.value = await $fetch(`http://127.0.0.1:8000/api/transporteur/missions/${route.params.id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
  } catch (err) {
    console.error('Erreur chargement mission:', err)
  } finally {
    loading.value = false
  }
}

async function accepterMission() {
  if (!dateLivraisonPrevue.value) {
    erreurDate.value = 'Veuillez choisir une date de livraison.'
    return
  }
  erreurDate.value = ''
  const token = localStorage.getItem('token')
  try {
    const res = await $fetch(`http://127.0.0.1:8000/api/transporteur/missions/${route.params.id}/accepter`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` },
      body: { date_livraison_estimee: dateLivraisonPrevue.value },
    })
    mission.value = res.mission
  } catch (err) {
    const msg = err.data?.message || "Erreur lors de l'acceptation."
    erreurDate.value = msg
  }
}

async function refuserMission() {
  const token = localStorage.getItem('token')
  try {
    const res = await $fetch(`http://127.0.0.1:8000/api/transporteur/missions/${route.params.id}/refuser`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` }
    })
    mission.value = res.mission
    await navigateTo('/transporteur/missions')
  } catch (err) {
    console.error('Erreur refus:', err)
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
  fetchMission()
})
</script>
