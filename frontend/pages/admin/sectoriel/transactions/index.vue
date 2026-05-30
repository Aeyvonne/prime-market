<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header Page -->
    <div class="bg-gradient-to-br from-[#2D5A27] to-[#1E3F1A] rounded-[2.5rem] p-8 md:p-12 text-white relative overflow-hidden shadow-xl shadow-[#2D5A27]/10">
      <div class="absolute -right-24 -bottom-24 w-96 h-96 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
      <div class="absolute -left-12 -top-12 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
      
      <div class="relative z-10 max-w-3xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
          <span class="px-4 py-1.5 rounded-full bg-[#8B7340] text-white text-[10px] font-black uppercase tracking-[0.2em] mb-4 inline-block">
            Secteur {{ currentSector }}
          </span>
          <h1 class="text-3xl md:text-4xl font-black font-serif leading-tight mb-2">
            Supervision des Transactions
          </h1>
          <p class="text-white/80 text-sm md:text-base font-medium">
            Consultez et filtrez tous les paiements et ventes enregistrés dans votre secteur.
          </p>
        </div>
      </div>
    </div>

    <!-- Total des transactions affiché en haut -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Total du Volume Financier (Validé) -->
      <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Volume Global Validé</p>
          <h3 class="text-3xl font-black text-emerald-600">{{ formatPrice(volumeValide) }}</h3>
          <p class="text-[10px] text-gray-400 font-medium mt-1">Total des paiements validés</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>

      <!-- Nombre total de transactions -->
      <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Nombre de Transactions</p>
          <h3 class="text-3xl font-black text-gray-900">{{ transactions.length }}</h3>
          <p class="text-[10px] text-gray-400 font-medium mt-1">Paiements totaux enregistrés</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-[#F5F0E8] text-[#8B7340] flex items-center justify-center">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>

      <!-- Taux de succès -->
      <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Taux de Complétion</p>
          <h3 class="text-3xl font-black text-blue-600">{{ successRate }}%</h3>
          <p class="text-[10px] text-gray-400 font-medium mt-1">Pourcentage de réussite</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
      </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col xl:flex-row xl:items-center justify-between gap-4">
      <div class="relative flex-grow max-w-lg group">
        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
          <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#2D5A27] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Rechercher par acheteur, vendeur, montant..." 
          class="w-full pl-12 pr-4 py-3 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-[#2D5A27] outline-none transition-all text-sm font-medium"
        >
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <!-- Filtre par statut -->
        <div class="relative min-w-[150px]">
          <select 
            v-model="selectedStatus"
            class="w-full bg-[#F5F0E8] text-gray-700 font-bold px-4 py-3 rounded-2xl border-none outline-none text-xs appearance-none cursor-pointer focus:ring-2 focus:ring-[#2D5A27]"
          >
            <option value="all">Tous les Statuts</option>
            <option value="Valider">Validé</option>
            <option value="EnCours">En cours</option>
            <option value="Annuler">Annulé</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
          </div>
        </div>

        <!-- Filtre par méthode de paiement -->
        <div class="relative min-w-[150px]">
          <select 
            v-model="selectedMethod"
            class="w-full bg-[#F5F0E8] text-gray-700 font-bold px-4 py-3 rounded-2xl border-none outline-none text-xs appearance-none cursor-pointer focus:ring-2 focus:ring-[#2D5A27]"
          >
            <option value="all">Toutes les Méthodes</option>
            <option value="Wave">Wave</option>
            <option value="OrangeMoney">Orange Money</option>
            <option value="FreeMoney">Free Money</option>
            <option value="Carte">Carte Bancaire</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 overflow-hidden shadow-sm">
      <div v-if="loading" class="p-12 space-y-4">
        <!-- Skeleton Loading -->
        <div class="animate-pulse flex items-center space-x-4 p-4 border-b border-gray-50" v-for="i in 3" :key="i">
          <div class="flex-1 space-y-2 py-1">
            <div class="h-4 bg-gray-200 rounded w-1/4"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
          </div>
          <div class="h-8 bg-gray-200 rounded w-20"></div>
        </div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date & Heure</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Acheteur</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Vendeur</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Méthode</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Montant</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="tx in filteredTransactions" :key="tx.id" class="hover:bg-[#F5F0E8]/20 transition-all duration-300">
              <!-- Date -->
              <td class="px-8 py-5">
                <span class="text-xs font-medium text-gray-500">{{ formatDateTime(tx.created_at) }}</span>
              </td>
              <!-- Acheteur -->
              <td class="px-8 py-5">
                <div class="flex flex-col">
                  <span class="text-xs font-bold text-gray-800">{{ tx.commande?.acheteur?.prenom }} {{ tx.commande?.acheteur?.nom }}</span>
                  <span class="text-[10px] text-gray-400 font-semibold">{{ tx.commande?.acheteur?.email }}</span>
                </div>
              </td>
              <!-- Vendeur -->
              <td class="px-8 py-5">
                <div class="flex flex-col">
                  <span class="text-xs font-bold text-gray-850">{{ tx.commande?.vendeur?.prenom }} {{ tx.commande?.vendeur?.nom }}</span>
                  <span class="text-[10px] text-gray-400 font-semibold">{{ tx.commande?.vendeur?.email }}</span>
                </div>
              </td>
              <!-- Méthode -->
              <td class="px-8 py-5 text-center">
                <span class="text-xs font-bold bg-[#F5F0E8] text-[#8B7340] px-3.5 py-1.5 rounded-full">{{ tx.methode_paiement }}</span>
              </td>
              <!-- Statut -->
              <td class="px-8 py-5 text-center">
                <span 
                  class="inline-flex px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider"
                  :class="statusStyle(tx.status)"
                >
                  {{ tx.status }}
                </span>
              </td>
              <!-- Montant -->
              <td class="px-8 py-5 text-right">
                <span class="text-sm font-black text-gray-950">{{ formatPrice(tx.montant) }}</span>
              </td>
              <!-- Actions -->
              <td class="px-8 py-5 text-center">
                <button 
                  @click="openDetailsModal(tx)"
                  class="text-xs font-bold text-[#2D5A27] hover:underline bg-[#2D5A27]/5 hover:bg-[#2D5A27]/10 px-3.5 py-2 rounded-xl transition-all"
                >
                  Détails
                </button>
              </td>
            </tr>
            <tr v-if="filteredTransactions.length === 0">
              <td colspan="7" class="px-8 py-16 text-center">
                <div class="max-w-md mx-auto space-y-3">
                  <div class="w-16 h-16 bg-[#F5F0E8] rounded-full flex items-center justify-center mx-auto text-[#8B7340]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </div>
                  <h3 class="text-base font-black text-gray-800 font-serif">Aucune transaction trouvée</h3>
                  <p class="text-xs text-gray-400 font-medium leading-relaxed">
                    Aucune transaction correspondante.
                  </p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Details Order Modal -->
    <div v-if="detailsModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-[2.5rem] p-8 max-w-2xl w-full border border-gray-100 shadow-2xl animate-in zoom-in duration-300 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-5">
          <div>
            <h3 class="text-xl font-black text-gray-900 font-serif">
              Détails du Paiement TX-{{ 1000 + selectedTx?.id }}
            </h3>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">
              Méthode : {{ selectedTx?.methode_paiement }} | Statut : {{ selectedTx?.status }}
            </p>
          </div>
          <button @click="detailsModalOpen = false" class="p-2.5 bg-gray-50 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-3xl border border-gray-100 text-xs">
          <div class="space-y-3">
            <h4 class="font-black text-gray-400 uppercase tracking-widest text-[9px]">Acheteur</h4>
            <div class="space-y-1">
              <p class="font-bold text-gray-800 text-sm">{{ selectedTx?.commande?.acheteur?.prenom }} {{ selectedTx?.commande?.acheteur?.nom }}</p>
              <p class="text-gray-500">{{ selectedTx?.commande?.acheteur?.email }}</p>
            </div>
          </div>
          <div class="space-y-3">
            <h4 class="font-black text-gray-400 uppercase tracking-widest text-[9px]">Vendeur (Producteur)</h4>
            <div class="space-y-1">
              <p class="font-bold text-gray-800 text-sm">{{ selectedTx?.commande?.vendeur?.prenom }} {{ selectedTx?.commande?.vendeur?.nom }}</p>
              <p class="text-gray-500">{{ selectedTx?.commande?.vendeur?.email }}</p>
            </div>
          </div>
        </div>

        <!-- Products List -->
        <div class="space-y-3">
          <h4 class="font-black text-gray-400 uppercase tracking-widest text-[9px]">Articles commandés</h4>
          <div class="border border-gray-100 rounded-3xl overflow-hidden">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="bg-gray-55/30 border-b border-gray-100">
                  <th class="px-5 py-3.5 font-bold text-gray-500">Produit</th>
                  <th class="px-5 py-3.5 font-bold text-gray-500">Sous-Catégorie</th>
                  <th class="px-5 py-3.5 font-bold text-gray-500 text-center">Quantité</th>
                  <th class="px-5 py-3.5 font-bold text-gray-500 text-right">Prix Unitaire</th>
                  <th class="px-5 py-3.5 font-bold text-gray-500 text-right">Total</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="line in selectedTx?.commande?.lignes_commande" :key="line.id" class="hover:bg-gray-50/50">
                  <td class="px-5 py-3.5 font-black text-gray-800">{{ line.produit?.nom }}</td>
                  <td class="px-5 py-3.5 font-medium text-gray-400">{{ line.produit?.sous_categorie?.nom }}</td>
                  <td class="px-5 py-3.5 text-center font-bold text-gray-600">{{ line.quantite }}</td>
                  <td class="px-5 py-3.5 text-right text-gray-600">{{ formatPrice(line.prix_unitaire) }}</td>
                  <td class="px-5 py-3.5 text-right font-black text-[#2D5A27]">
                    {{ formatPrice(line.quantite * line.prix_unitaire) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Footer Pricing details -->
        <div class="flex items-center justify-between border-t border-gray-100 pt-6">
          <div class="text-xs text-gray-400">
            Enregistré le : <span class="font-bold text-gray-600">{{ formatDateTime(selectedTx?.created_at) }}</span>
          </div>
          <div class="text-right">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Montant Payé</span>
            <span class="text-2xl font-black text-[#2D5A27] font-serif">{{ formatPrice(selectedTx?.montant) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

import { ref, onMounted, computed } from 'vue'

const currentSector = ref('')
const transactions = ref([])
const totalSectoriel = ref(0)
const loading = ref(true)
const searchQuery = ref('')
const selectedStatus = ref('all')
const selectedMethod = ref('all')

const detailsModalOpen = ref(false)
const selectedTx = ref(null)

function formatPrice(price) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}

function formatDateTime(dateStr) {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  const formattedDate = date.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
  const formattedTime = date.toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  })
  return `${formattedDate} à ${formattedTime}`
}

function statusStyle(status) {
  switch (status) {
    case 'Valider': return 'bg-emerald-100 text-emerald-805'
    case 'Annuler': return 'bg-red-100 text-red-805'
    case 'EnCours': return 'bg-amber-100 text-amber-805'
    default: return 'bg-gray-100 text-gray-800'
  }
}

// Charger les transactions
async function loadTransactions() {
  loading.value = true
  const token = localStorage.getItem('token')
  try {
    const config = useRuntimeConfig()
    const apiUrl = config.public.apiUrl || 'http://127.0.0.1:8000/api'

    const res = await $fetch(`${apiUrl}/admin-sectoriel/transactions`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    currentSector.value = res.secteur || ''
    transactions.value = res.transactions || []
    totalSectoriel.value = res.total || 0
  } catch (err) {
    console.error('Erreur chargement transactions:', err)
  } finally {
    loading.value = false
  }
}

const volumeValide = computed(() => {
  return transactions.value
    .filter(t => t.status === 'Valider')
    .reduce((sum, curr) => sum + parseFloat(curr.montant || 0), 0)
})

const successRate = computed(() => {
  if (transactions.value.length === 0) return 0
  const validatedCount = transactions.value.filter(t => t.status === 'Valider').length
  return Math.round((validatedCount / transactions.value.length) * 100)
})

function openDetailsModal(tx) {
  selectedTx.value = tx
  detailsModalOpen.value = true
}

const filteredTransactions = computed(() => {
  let result = transactions.value

  if (selectedStatus.value !== 'all') {
    result = result.filter(t => t.status === selectedStatus.value)
  }

  if (selectedMethod.value !== 'all') {
    result = result.filter(t => t.methode_paiement === selectedMethod.value)
  }

  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    result = result.filter(t => {
      const buyerName = `${t.commande?.acheteur?.prenom || ''} ${t.commande?.acheteur?.nom || ''}`.toLowerCase()
      const sellerName = `${t.commande?.vendeur?.prenom || ''} ${t.commande?.vendeur?.nom || ''}`.toLowerCase()
      const amountStr = String(t.montant)
      
      return (
        buyerName.includes(query) ||
        sellerName.includes(query) ||
        amountStr.includes(query)
      )
    })
  }

  return result
})

onMounted(async () => {
  await loadTransactions()
})
</script>
