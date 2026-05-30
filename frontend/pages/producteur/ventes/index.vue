<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div>
      <h1 class="text-3xl font-black text-gray-900 font-serif">Mes <span class="text-[#2D5A27]">Ventes</span></h1>
      <p class="text-gray-500 font-medium mt-1">Historique de vos ventes validées.</p>
    </div>

    <!-- Stats -->
    <div v-if="!pending" class="grid grid-cols-1 md:grid-cols-3 gap-5">
      <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
        <p class="text-xs font-black text-gray-400 uppercase tracking-wider mb-1">Revenu total</p>
        <p class="text-3xl font-black text-[#2D5A27]">{{ formatPrice(data?.total_revenu || 0) }}</p>
      </div>
      <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
        <p class="text-xs font-black text-gray-400 uppercase tracking-wider mb-1">Ventes totales</p>
        <p class="text-3xl font-black text-gray-900">{{ data?.total_ventes || 0 }}</p>
      </div>
      <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
        <p class="text-xs font-black text-gray-400 uppercase tracking-wider mb-1">Prix moyen</p>
        <p class="text-3xl font-black text-gray-900">{{ formatPrice(data?.total_ventes ? (data.total_revenu / data.total_ventes) : 0) }}</p>
      </div>
    </div>
    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-5">
      <div v-for="i in 3" :key="i" class="h-28 bg-gray-50 animate-pulse rounded-[2rem]"></div>
    </div>

    <!-- Liste des ventes -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-6 md:px-8 pt-6 pb-3 border-b border-gray-50">
        <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider">Détail des ventes</h2>
      </div>

      <div v-if="pending" class="p-12 space-y-6">
        <div v-for="i in 3" :key="i" class="h-20 bg-gray-50 animate-pulse rounded-2xl"></div>
      </div>

      <div v-else-if="data?.commandes?.length > 0">
        <div v-for="cmd in data.commandes" :key="cmd.id" class="p-6 md:p-8 border-b border-gray-50 last:border-b-0 hover:bg-[#F5F0E8]/30 transition-colors">
          <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#2D5A27] flex items-center justify-center text-white text-xs font-black">#{{ cmd.id }}</div>
              <div>
                <p class="text-sm font-bold text-gray-900">{{ cmd.acheteur?.prenom }} {{ cmd.acheteur?.nom }}</p>
                <p class="text-[10px] text-gray-400 font-medium">{{ formatDate(cmd.date_commande) }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm font-black text-[#2D5A27]">{{ formatPrice(cmd.montant_total) }}</p>
              <span class="text-[10px] font-bold" :class="statutClass(cmd.status)">{{ statutLabel(cmd.status) }}</span>
            </div>
          </div>
          <div class="flex flex-wrap gap-2">
            <span v-for="p in cmd.produits" :key="p.nom"
              class="px-3 py-1.5 bg-gray-50 rounded-xl text-xs text-gray-600 font-medium">
              {{ p.nom }} <span class="text-gray-400">×{{ p.quantite }}</span>
            </span>
          </div>
        </div>
      </div>

      <div v-else class="py-16 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <h3 class="text-lg font-black text-gray-900">Aucune vente pour le moment</h3>
        <p class="text-sm text-gray-500 mt-1">Les ventes validées apparaîtront ici.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const token = process.client ? localStorage.getItem('token') : null

const { data, pending } = await useFetch('http://127.0.0.1:8000/api/producteur/ventes', {
  headers: token ? { Authorization: `Bearer ${token}` } : {},
  server: false,
})

function statutClass(status) {
  return status === 'Valider' ? 'text-emerald-600' : 'text-blue-600'
}

function statutLabel(status) {
  return status === 'Valider' ? 'Validée' : 'Livrée'
}

function formatPrice(price) {
  if (!price) return '0 FCFA'
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>
