<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">
          Suivi des <span class="text-[#2D5A27]">Transactions</span> 💳
        </h1>
        <p class="text-gray-500 font-medium mt-1">Historique des flux financiers sur la plateforme.</p>
      </div>
    </div>

    <!-- Revenue Header card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Volume de ventes (Mois)</p>
          <h3 class="text-2xl font-black text-gray-900">{{ formatPrice(totals.overall) }}</h3>
        </div>
        <div class="w-12 h-12 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center font-black">
          💸
        </div>
      </div>

      <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Paiements Wave</p>
          <h3 class="text-2xl font-black text-gray-900">{{ formatPrice(totals.wave) }}</h3>
        </div>
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center font-black text-xs">
          🌊 Wave
        </div>
      </div>

      <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Paiements Orange Money</p>
          <h3 class="text-2xl font-black text-gray-900">{{ formatPrice(totals.om) }}</h3>
        </div>
        <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center font-black text-xs">
          🍊 OM
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 items-center justify-between">
      <h3 class="text-lg font-black text-gray-900">Historique des transactions</h3>
      
      <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
        <!-- Method filter -->
        <select v-model="filters.method" class="px-6 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-bold text-gray-700 text-xs">
          <option value="">Toutes les méthodes</option>
          <option value="Wave">Wave</option>
          <option value="OrangeMoney">Orange Money</option>
        </select>

        <!-- Status filter -->
        <select v-model="filters.status" class="px-6 py-3 bg-[#F5F0E8]/40 border-2 border-transparent rounded-2xl focus:border-[#2D5A27] focus:bg-white outline-none transition-all font-bold text-gray-700 text-xs">
          <option value="">Tous les statuts</option>
          <option value="Valider">Valider</option>
          <option value="EnCours">En cours</option>
          <option value="Annuler">Annuler</option>
        </select>
      </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div v-if="loading" class="p-12 space-y-4">
        <div v-for="i in 5" :key="i" class="h-14 bg-gray-50 animate-pulse rounded-xl"></div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Transaction ID</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Acheteur</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Vendeur</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Méthode</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Montant</th>
              <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="tx in filteredTransactions" :key="tx.id" class="hover:bg-[#F5F0E8]/30 transition-colors">
              <td class="px-8 py-5">
                <span class="text-sm font-bold text-gray-900">#TX-{{ tx.id }}</span>
              </td>
              <td class="px-8 py-5">
                <div>
                  <span class="text-sm font-bold text-gray-800 block">{{ tx.commande?.acheteur?.prenom }} {{ tx.commande?.acheteur?.nom }}</span>
                  <span class="text-[10px] text-gray-400 uppercase tracking-widest font-black">{{ tx.commande?.acheteur?.role }}</span>
                </div>
              </td>
              <td class="px-8 py-5">
                <div>
                  <span class="text-sm font-medium text-gray-700 block">{{ tx.commande?.vendeur?.prenom }} {{ tx.commande?.vendeur?.nom }}</span>
                  <span class="text-[10px] text-gray-400 uppercase tracking-widest font-black">{{ tx.commande?.vendeur?.role }}</span>
                </div>
              </td>
              <td class="px-8 py-5 text-sm text-gray-500 font-medium">{{ formatDate(tx.created_at) }}</td>
              <td class="px-8 py-5 text-center">
                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">{{ tx.methode_paiement }}</span>
              </td>
              <td class="px-8 py-5 text-sm font-black text-gray-900">{{ formatPrice(tx.montant) }}</td>
              <td class="px-8 py-5 text-center">
                <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${statusStyle(tx.status)}`">
                  {{ tx.status }}
                </span>
              </td>
            </tr>
            <tr v-if="filteredTransactions.length === 0">
              <td colspan="7" class="px-8 py-12 text-center text-gray-400 italic">
                Aucune transaction trouvée.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

import { ref, reactive, computed, onMounted } from 'vue'

const transactions = ref([])
const loading = ref(true)
const filters = reactive({ method: '', status: '' })

async function fetchTransactions() {
  loading.value = true
  try {
    const data = await $fetch('http://127.0.0.1:8000/api/super-admin/transactions', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    transactions.value = data || []
  } catch (err) {
    console.error('Erreur chargement transactions:', err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchTransactions)

const filteredTransactions = computed(() => {
  return transactions.value.filter(t => {
    const methodMatch = !filters.method || t.methode_paiement === filters.method
    const statusMatch = !filters.status || t.status === filters.status
    return methodMatch && statusMatch
  })
})

const totals = computed(() => {
  let overall = 0
  let wave = 0
  let om = 0

  filteredTransactions.value.forEach(t => {
    if (t.status === 'Valider') {
      const amt = parseFloat(t.montant) || 0
      overall += amt
      if (t.methode_paiement === 'Wave') wave += amt
      if (t.methode_paiement === 'OrangeMoney') om += amt
    }
  })

  return { overall, wave, om }
})

function statusStyle(status) {
  switch (status) {
    case 'Valider': return 'bg-green-100 text-green-700'
    case 'Annuler': return 'bg-red-100 text-red-700'
    case 'EnCours': return 'bg-amber-100 text-amber-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function formatPrice(price) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}
</script>
