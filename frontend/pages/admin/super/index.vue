<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">
          Console <span class="text-[#2D5A27]">Super Admin</span> 👑
        </h1>
        <p class="text-gray-500 font-medium mt-1">Supervision globale de la plateforme Prime Market.</p>
      </div>
      <div class="flex items-center gap-3">
        <NuxtLink to="/admin/super/comptes" class="px-6 py-3 bg-[#2D5A27] text-white rounded-2xl font-bold text-sm shadow-lg hover:shadow-[#2D5A27]/20 transition-all hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
          Gérer les utilisateurs
        </NuxtLink>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="(stat, index) in statsList" :key="index"
        class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-500 group relative overflow-hidden"
      >
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

    <!-- Graph and Recent transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Graph (User roles distribution) -->
      <div class="lg:col-span-1 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-between">
        <div>
          <h3 class="text-xl font-black text-gray-900 mb-6">Répartition des rôles</h3>
          
          <!-- Bar Graph CSS -->
          <div class="space-y-6">
            <div v-for="role in rolesChartData" :key="role.name" class="space-y-2">
              <div class="flex justify-between text-xs font-bold text-gray-600">
                <span class="capitalize">{{ role.name }}</span>
                <span>{{ role.count }} ({{ getPercentage(role.count) }}%)</span>
              </div>
              <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                <div 
                  class="h-full rounded-full transition-all duration-1000 ease-out" 
                  :class="role.color"
                  :style="{ width: `${getPercentage(role.count)}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-50">
          <NuxtLink to="/admin/super/statistiques" class="text-xs font-black text-[#2D5A27] hover:underline flex items-center gap-1">
            Voir les analyses détaillées →
          </NuxtLink>
        </div>
      </div>

      <!-- Transactions -->
      <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between">
        <div>
          <div class="p-8 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-xl font-black text-gray-900">Dernières Transactions</h3>
            <NuxtLink to="/admin/super/transactions" class="text-xs font-bold text-[#2D5A27] hover:underline">Voir tout →</NuxtLink>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-gray-50/50">
                  <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Acheteur</th>
                  <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Vendeur</th>
                  <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Montant</th>
                  <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Méthode</th>
                  <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="tx in recentTransactions" :key="tx.id" class="hover:bg-[#F5F0E8]/30 transition-colors">
                  <td class="px-8 py-4">
                    <span class="text-sm font-bold text-gray-800">{{ tx.commande?.acheteur?.prenom }} {{ tx.commande?.acheteur?.nom }}</span>
                  </td>
                  <td class="px-8 py-4">
                    <span class="text-sm font-medium text-gray-600">{{ tx.commande?.vendeur?.prenom }} {{ tx.commande?.vendeur?.nom }}</span>
                  </td>
                  <td class="px-8 py-4">
                    <span class="text-sm font-black text-gray-900">{{ formatPrice(tx.montant) }}</span>
                  </td>
                  <td class="px-8 py-4 text-center">
                    <span class="text-xs font-bold bg-[#F5F0E8] text-[#8B7340] px-3 py-1 rounded-full">{{ tx.methode_paiement }}</span>
                  </td>
                  <td class="px-8 py-4 text-center">
                    <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${statusStyle(tx.status)}`">
                      {{ tx.status }}
                    </span>
                  </td>
                </tr>
                <tr v-if="recentTransactions.length === 0">
                  <td colspan="5" class="px-8 py-10 text-center text-gray-400 italic">
                    Aucune transaction récente.
                  </td>
                </tr>
              </tbody>
            </table>
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

import { ref, computed, onMounted, h } from 'vue'

const rawStats = ref({
  total_comptes: 0,
  total_producteurs: 0,
  total_distributeurs: 0,
  total_consommateurs: 0,
  total_transporteurs: 0,
  total_produits: 0,
  total_commandes: 0,
  total_transactions: 0,
  revenu_global: 0
})

const recentTransactions = ref([])

const statsList = computed(() => [
  { label: 'Utilisateurs', value: rawStats.value.total_comptes.toString(), icon: IconUsers, bgColor: 'bg-emerald-50', textColor: 'text-emerald-600' },
  { label: 'Produits', value: rawStats.value.total_produits.toString(), icon: IconProducts, bgColor: 'bg-blue-50', textColor: 'text-blue-600' },
  { label: 'Commandes', value: rawStats.value.total_commandes.toString(), icon: IconOrders, bgColor: 'bg-amber-50', textColor: 'text-amber-600' },
  { label: 'Transactions', value: formatPrice(rawStats.value.revenu_global), icon: IconWallet, bgColor: 'bg-purple-50', textColor: 'text-purple-600' }
])

const rolesChartData = computed(() => [
  { name: 'producteurs', count: rawStats.value.total_producteurs, color: 'bg-green-600' },
  { name: 'distributeurs', count: rawStats.value.total_distributeurs, color: 'bg-amber-500' },
  { name: 'consommateurs', count: rawStats.value.total_consommateurs, color: 'bg-blue-500' },
  { name: 'transporteurs', count: rawStats.value.total_transporteurs, color: 'bg-purple-500' }
])

function getPercentage(count) {
  const total = rawStats.value.total_comptes || 1
  return Math.round((count / total) * 100)
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
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}

// Icons
function IconUsers() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })])
}
function IconProducts() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' })])
}
function IconOrders() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' })])
}
function IconWallet() {
  return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-width': '2', d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })])
}

onMounted(async () => {
  const token = localStorage.getItem('token')
  try {
    const [statsResponse, transactionsResponse] = await Promise.all([
      $fetch('http://127.0.0.1:8000/api/super-admin/statistiques', { headers: { Authorization: `Bearer ${token}` } }),
      $fetch('http://127.0.0.1:8000/api/super-admin/transactions', { headers: { Authorization: `Bearer ${token}` } })
    ])
    if (statsResponse) rawStats.value = statsResponse
    if (transactionsResponse) recentTransactions.value = transactionsResponse.slice(0, 5)
  } catch (err) {
    console.error('Erreur chargement dashboard:', err)
  }
})
</script>
