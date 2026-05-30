<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div>
      <h1 class="text-3xl font-black text-gray-900 font-serif">Paiements <span class="text-[#2D5A27]">reçus</span></h1>
      <p class="text-gray-500 font-medium mt-1">Suivez vos revenus perçus.</p>
    </div>

    <!-- Total -->
    <div v-if="!pending" class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
      <p class="text-xs font-black text-gray-400 uppercase tracking-wider mb-1">Total reçu</p>
      <p class="text-4xl font-black text-[#2D5A27]">{{ formatPrice(data?.total_recu || 0) }}</p>
    </div>
    <div v-else class="h-28 bg-gray-50 animate-pulse rounded-[2rem]"></div>

    <!-- Liste des paiements -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-6 md:px-8 pt-6 pb-3 border-b border-gray-50">
        <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider">Historique</h2>
      </div>

      <div v-if="pending" class="p-12 space-y-6">
        <div v-for="i in 3" :key="i" class="h-16 bg-gray-50 animate-pulse rounded-2xl"></div>
      </div>

      <div v-else-if="data?.paiements?.length > 0">
        <div v-for="p in data.paiements" :key="p.id" class="p-6 md:p-8 border-b border-gray-50 last:border-b-0 hover:bg-[#F5F0E8]/30 transition-colors">
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              </div>
              <div>
                <p class="text-sm font-bold text-gray-900">{{ p.commande?.acheteur?.prenom }} {{ p.commande?.acheteur?.nom }}</p>
                <p class="text-xs text-gray-500">Commande #{{ p.commande_id }} · {{ p.methode_paiement }}</p>
                <p class="text-[10px] text-gray-400 font-medium">{{ formatDate(p.date_transaction || p.created_at) }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-base font-black text-emerald-600">{{ formatPrice(p.montant) }}</p>
              <span v-if="p.status === 'Valider'" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full">Reçu</span>
              <span v-else class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-full">{{ p.status }}</span>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="py-16 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <h3 class="text-lg font-black text-gray-900">Aucun paiement reçu</h3>
        <p class="text-sm text-gray-500 mt-1">Les paiements pour vos ventes apparaîtront ici.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const token = process.client ? localStorage.getItem('token') : null

const { data, pending } = await useFetch('http://127.0.0.1:8000/api/producteur/paiements', {
  headers: token ? { Authorization: `Bearer ${token}` } : {},
  server: false,
})

function formatPrice(price) {
  if (!price) return '0 FCFA'
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>
