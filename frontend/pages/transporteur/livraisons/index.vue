<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div>
      <h1 class="text-3xl font-black text-gray-900 font-serif">Mes Livraisons</h1>
      <p class="text-gray-500 font-medium mt-1">Suivez et mettez à jour le statut de vos livraisons.</p>
    </div>

    <!-- Filtres -->
    <div class="flex flex-wrap gap-2">
      <button v-for="filtre in ['Toutes', 'EnCours', 'Valider', 'Annuler']" :key="filtre"
        @click="filtreActif = filtre"
        :class="`px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all ${
          filtreActif === filtre
            ? 'bg-[#2D5A27] text-white shadow-lg'
            : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-200'
        }`">
        {{ filtre === 'Toutes' ? 'Toutes' : filtre }}
      </button>
    </div>

    <div class="grid grid-cols-1 gap-6">
      <div v-for="livraison in filteredLivraisons" :key="livraison.id"
        class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 hover:shadow-xl transition-all">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <h3 class="text-lg font-black text-gray-900">Livraison #{{ livraison.id }}</h3>
              <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${statusStyle(livraison.status)}`">{{ livraison.status }}</span>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-1 text-xs text-gray-600">
              <span class="font-medium"><span class="text-gray-400">Type :</span> {{ getTypesProduits(livraison) || '-' }}</span>
              <span v-if="livraison.mission?.remuneration" class="font-black text-[#2D5A27]">{{ formatPrice(livraison.mission.remuneration) }}</span>
            </div>
            <p class="text-sm text-gray-600 mt-1">{{ livraison.adresse_livraison }}</p>
            <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500">
              <span v-if="livraison.date_livraison_estimee">Livraison prévue : {{ formatDate(livraison.date_livraison_estimee) }}</span>
            </div>
          </div>

          <div class="flex gap-2 flex-shrink-0">
            <button @click="openModal(livraison)"
              class="px-5 py-2.5 bg-[#2D5A27] text-white rounded-xl text-xs font-bold hover:bg-[#2D5A27]/90 transition-all shadow-lg active:scale-95">
              Mettre à jour le statut
            </button>
            <NuxtLink :to="`/transporteur/livraisons/${livraison.id}`"
              class="px-5 py-2.5 bg-[#2D5A27]/10 text-[#2D5A27] rounded-xl text-xs font-bold hover:bg-[#2D5A27]/20 transition-all">
              Détails
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <div v-if="filteredLivraisons.length === 0" class="text-center py-20">
      <p class="text-gray-400 font-medium italic">Aucune livraison trouvée.</p>
    </div>

    <!-- Modal mise à jour statut -->
    <Teleport to="body">
      <div v-if="modalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="modalOpen = false">
        <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 w-full max-w-md mx-4 animate-in fade-in zoom-in-95 duration-200">
          <h3 class="text-xl font-black text-gray-900 mb-2">Mettre à jour le statut</h3>
          <p class="text-sm text-gray-500 mb-6">Livraison #{{ selectedLivraison?.id }} — {{ selectedLivraison?.adresse_livraison }}</p>

          <div class="space-y-3">
            <button v-for="opt in statutOptions" :key="opt.value"
              @click="updateStatut(opt.value)"
              :class="`w-full text-left px-5 py-4 rounded-2xl font-bold text-sm transition-all border-2 ${
                selectedLivraison?.status === opt.value
                  ? 'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed'
                  : opt.color + ' hover:shadow-lg active:scale-[0.98]'
              }`"
              :disabled="selectedLivraison?.status === opt.value">
              <span class="block">{{ opt.label }}</span>
              <span class="block text-[10px] font-normal opacity-70 mt-0.5">{{ opt.desc }}</span>
            </button>
          </div>

          <button @click="modalOpen = false"
            class="mt-6 w-full py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-200 transition-all">
            Annuler
          </button>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const livraisons = ref([])
const filtreActif = ref('Toutes')
const modalOpen = ref(false)
const selectedLivraison = ref(null)

const filteredLivraisons = computed(() => {
  if (filtreActif.value === 'Toutes') return livraisons.value
  return livraisons.value.filter(l => l.status === filtreActif.value)
})

const statutOptions = computed(() => {
  const current = selectedLivraison.value?.status
  const all = [
    { value: 'Chargement', label: 'Chargement effectué', desc: 'La marchandise a été chargée et est en route.', color: 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100' },
    { value: 'Valider', label: 'Livraison effectuée', desc: 'La marchandise a été livrée au destinataire.', color: 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100' },
  ]
  if (current !== 'Valider' && current !== 'Annuler') {
    all.push({ value: 'Annuler', label: 'Annuler la livraison', desc: 'Vous ne pouvez plus assurer cette livraison.', color: 'bg-red-50 border-red-200 text-red-700 hover:bg-red-100' })
  }
  return all
})

function openModal(livraison) {
  selectedLivraison.value = livraison
  modalOpen.value = true
}

async function fetchLivraisons() {
  const token = localStorage.getItem('token')
  if (!token) return navigateTo('/login')
  try {
    livraisons.value = await $fetch('http://127.0.0.1:8000/api/transporteur/livraisons', {
      headers: { Authorization: `Bearer ${token}` }
    })
  } catch (err) {
    console.error('Erreur chargement livraisons:', err)
  }
}

async function updateStatut(value) {
  const token = localStorage.getItem('token')
  const id = selectedLivraison.value?.id
  if (!id) return
  try {
    const data = {}
    if (value === 'Valider') {
      data.status = 'Valider'
    } else if (value === 'Annuler') {
      data.status = 'Annuler'
    }
    await $fetch(`http://127.0.0.1:8000/api/transporteur/livraisons/${id}/statut`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` },
      body: data,
    })
    modalOpen.value = false
    await fetchLivraisons()
  } catch (err) {
    console.error('Erreur mise à jour statut:', err)
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

function getTypesProduits(livraison) {
  const lignes = livraison.commande?.lignes_commande
  if (!lignes || lignes.length === 0) return null
  const types = [...new Set(lignes.map(l => l.produit?.sous_categorie?.categorie?.nom || l.produit?.sousCategorie?.categorie?.nom || '').filter(Boolean))]
  return types.join(', ') || null
}


function formatPrice(price) {
  if (!price) return '0 F'
  return new Intl.NumberFormat('fr-FR').format(price) + ' F'
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(fetchLivraisons)
</script>
