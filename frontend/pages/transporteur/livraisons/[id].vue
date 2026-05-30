<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <NuxtLink to="/transporteur/livraisons" class="inline-flex items-center gap-2 text-sm font-bold text-[#2D5A27] hover:underline">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Retour aux livraisons
    </NuxtLink>

    <div v-if="livraison" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
      <div class="flex items-start justify-between mb-8">
        <div>
          <h1 class="text-3xl font-black text-gray-900 font-serif">Livraison #{{ livraison.id }}</h1>
          <p class="text-gray-500 font-medium mt-1">Mission #{{ livraison.mission?.id }} — Commande #{{ livraison.commande_id }}</p>
        </div>
        <span :class="`inline-flex px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider ${statusStyle(livraison.status)}`">{{ livraison.status }}</span>
      </div>

      <!-- Infos livraison -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-[#F5F0E8]/50 rounded-2xl p-6">
          <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Détails livraison</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Adresse</span>
              <span class="text-sm font-bold text-gray-900 text-right max-w-[200px]">{{ livraison.adresse_livraison }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Livraison prévue</span>
              <span class="text-sm font-bold text-gray-900">{{ formatDate(livraison.date_livraison_estimee) || '-' }}</span>
            </div>
          </div>
        </div>

        <div class="bg-[#F5F0E8]/50 rounded-2xl p-6">
          <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Destinataire</h3>
          <div v-if="destinataire" class="space-y-3">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#2D5A27] flex items-center justify-center text-white text-sm font-black">
                {{ (destinataire.prenom?.[0] || destinataire.nom?.[0] || '?').toUpperCase() }}
              </div>
              <div>
                <p class="text-sm font-bold text-gray-900">{{ destinataire.prenom }} {{ destinataire.nom }}</p>
                <p class="text-xs text-gray-500">{{ destinataire.email }}</p>
              </div>
            </div>
            <p v-if="destinataire.telephone" class="text-sm text-gray-600">
              <span class="font-bold text-gray-900">Tél :</span> {{ destinataire.telephone }}
            </p>
            <p v-if="destinataire.adresse" class="text-sm text-gray-600">
              <span class="font-bold text-gray-900">Adresse :</span> {{ destinataire.adresse }}
            </p>
          </div>
          <p v-else class="text-sm text-gray-400 italic">Aucune information disponible.</p>
        </div>
      </div>

      <!-- Infos commande (produits, poids, rémunération) -->
      <div class="bg-[#F5F0E8]/50 rounded-2xl p-6 mb-8">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Détails de la commande</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Type de produit</p>
            <p class="text-sm font-bold text-gray-900">{{ typesProduits || '-' }}</p>
          </div>
          <div>
            <p class="text-[10px] font-black text-[#2D5A27] uppercase tracking-[0.2em] mb-1">Rémunération</p>
            <p class="text-xl font-black text-[#2D5A27]">{{ formatPrice(livraison.mission?.remuneration) }}</p>
          </div>
        </div>
        <div v-if="livraison.commande?.lignes_commande" class="mt-4 border-t border-[#2D5A27]/10 pt-4">
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Articles</p>
          <div v-for="ligne in livraison.commande.lignes_commande" :key="ligne.id" class="flex items-center justify-between py-1.5 text-sm">
            <span class="text-gray-700 font-medium">{{ ligne.produit?.nom || 'Produit #' + ligne.produit_id }} × {{ ligne.quantite }}</span>
            <span class="font-bold text-gray-900">{{ formatPrice(ligne.prix_unitaire * ligne.quantite) }}</span>
          </div>
        </div>
      </div>

      <!-- Historique des mises à jour -->
      <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-8">
        <h3 class="text-lg font-black text-gray-900 mb-4">Historique des statuts</h3>
        <div class="space-y-4">
          <div class="flex items-start gap-4">
            <div class="flex flex-col items-center">
              <div class="w-3 h-3 rounded-full bg-emerald-500 shadow shadow-emerald-200" />
              <div class="w-0.5 h-8 bg-gray-200" />
            </div>
            <div>
              <p class="text-sm font-bold text-gray-900">Livraison créée</p>
              <p class="text-xs text-gray-500">{{ formatDateTime(livraison.created_at) }}</p>
            </div>
          </div>
          <div v-if="livraison.status === 'Valider'" class="flex items-start gap-4">
            <div class="flex flex-col items-center">
              <div class="w-3 h-3 rounded-full bg-emerald-500 shadow shadow-emerald-200" />
            </div>
            <div>
              <p class="text-sm font-bold text-gray-900">Livraison effectuée</p>
              <p class="text-xs text-gray-500">{{ formatDate(livraison.date_livraison_estimee) }}</p>
            </div>
          </div>
          <div v-if="livraison.status === 'Annuler'" class="flex items-start gap-4">
            <div class="flex flex-col items-center">
              <div class="w-3 h-3 rounded-full bg-red-500 shadow shadow-red-200" />
            </div>
            <div>
              <p class="text-sm font-bold text-gray-900">Livraison annulée</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Mise à jour statut -->
      <div class="bg-[#F5F0E8]/50 rounded-2xl p-6">
        <h3 class="text-lg font-black text-gray-900 mb-4">Mettre à jour le statut</h3>
        <div class="flex flex-wrap gap-3">
          <button v-if="livraison.status !== 'Valider' && livraison.status !== 'Annuler'"
            @click="updateStatut('Valider')"
            class="px-6 py-3 bg-emerald-500 text-white rounded-2xl font-bold text-sm hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-200 active:scale-95">
            Livraison effectuée
          </button>
          <button v-if="livraison.status !== 'Annuler' && livraison.status !== 'Valider'"
            @click="updateStatut('Annuler')"
            class="px-6 py-3 bg-red-500 text-white rounded-2xl font-bold text-sm hover:bg-red-600 transition-all shadow-lg shadow-red-200 active:scale-95">
            Annuler la livraison
          </button>
        </div>
      </div>
    </div>

    <div v-if="!livraison && !loading" class="text-center py-20">
      <p class="text-gray-400 font-medium italic">Livraison non trouvée.</p>
    </div>

    <!-- Toast -->
    <div v-if="toast.show" :class="`fixed bottom-8 right-8 z-50 px-6 py-4 rounded-2xl shadow-2xl text-sm font-bold animate-in slide-in-from-right duration-300 ${toast.type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-500 text-white'}`">
      {{ toast.message }}
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const route = useRoute()
const livraison = ref(null)
const loading = ref(true)

const destinataire = computed(() => {
  return livraison.value?.commande?.acheteur || null
})

const typesProduits = computed(() => {
  const lignes = livraison.value?.commande?.lignes_commande
  if (!lignes || lignes.length === 0) return null
  const types = [...new Set(lignes.map(l => l.produit?.sous_categorie?.categorie?.nom || l.produit?.sousCategorie?.categorie?.nom || '').filter(Boolean))]
  return types.join(', ') || null
})


async function fetchLivraison() {
  const token = localStorage.getItem('token')
  if (!token) return navigateTo('/login')
  try {
    livraison.value = await $fetch(`http://127.0.0.1:8000/api/transporteur/livraisons/${route.params.id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
  } catch (err) {
    console.error('Erreur chargement livraison:', err)
  } finally {
    loading.value = false
  }
}

const toast = reactive({ show: false, type: 'success', message: '' })

function showToast(type, message) {
  toast.type = type
  toast.message = message
  toast.show = true
  setTimeout(() => toast.show = false, 4000)
}

async function updateStatut(value) {
  const token = localStorage.getItem('token')
  try {
    const data = {}
    if (value === 'Valider') {
      data.status = 'Valider'
    } else if (value === 'Annuler') {
      data.status = 'Annuler'
    }
    const res = await $fetch(`http://127.0.0.1:8000/api/transporteur/livraisons/${route.params.id}/statut`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token}` },
      body: data,
    })
    livraison.value = res.livraison
    showToast('success', res.message || 'Statut mis à jour avec succès.')
  } catch (err) {
    console.error('Erreur mise à jour statut:', err)
    showToast('error', err.data?.message || 'Erreur lors de la mise à jour du statut.')
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

function formatDate(date) {
  if (!date) return null
  return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatPrice(price) {
  if (!price) return '0 F'
  return new Intl.NumberFormat('fr-FR').format(price) + ' F'
}

function formatDateTime(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

onMounted(fetchLivraison)
</script>
