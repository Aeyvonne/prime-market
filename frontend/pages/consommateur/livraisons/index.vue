<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">Suivi des <span class="text-[#2D5A27]">Livraisons</span></h1>
        <p class="text-gray-500 font-medium mt-1">Suivez l'état d'expédition de vos commandes en temps réel.</p>
      </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div v-if="pending" class="p-12 space-y-4">
        <div v-for="i in 3" :key="i" class="h-20 bg-gray-50 animate-pulse rounded-2xl"></div>
      </div>
      <div v-else-if="livraisons.length > 0" class="divide-y divide-gray-50">
        <div v-for="liv in livraisons" :key="liv.id" class="p-6 md:p-8 hover:bg-[#F5F0E8]/30 transition-colors group">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-4">
              <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center text-xl shadow-inner', liv.status === 'Valider' ? 'bg-emerald-50 text-emerald-600' : liv.status === 'Annuler' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600']">
                {{ liv.status === 'Valider' ? '✅' : liv.status === 'Annuler' ? '❌' : '🚚' }}
              </div>
              <div>
                <div class="flex items-center gap-3 mb-1">
                  <span class="text-sm font-black text-gray-900">Livraison #{{ liv.id }}</span>
                  <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider', liv.status === 'Valider' ? 'bg-emerald-100 text-emerald-700' : liv.status === 'Annuler' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700']">{{ statusLabel(liv.status) }}</span>
                </div>
                <p class="text-xs text-gray-500 font-medium">Commande #{{ liv.commande_id }}</p>
              </div>
            </div>
            <NuxtLink :to="`/consommateur/livraisons/${liv.id}`" class="px-5 py-2.5 bg-[#F5F0E8] text-[#2D5A27] rounded-xl text-xs font-black hover:bg-[#2D5A27] hover:text-white transition-all text-center whitespace-nowrap">Détails</NuxtLink>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
              <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Adresse</p>
              <p class="font-medium text-gray-700 truncate">{{ liv.commande?.adresse_livraison || 'Non spécifiée' }}</p>
            </div>
            <div>
              <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Livraison prévue</p>
              <p class="font-medium text-gray-700">{{ formatDate(liv.date_livraison_estimee) || '—' }}</p>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="py-20 text-center">
        <div class="text-6xl mb-4 opacity-20">📦</div>
        <h3 class="text-xl font-black text-gray-900">Aucune livraison</h3>
        <p class="text-gray-500 font-medium mt-2">Vos expéditions apparaîtront ici dès qu'elles seront initiées.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const { data: livraisons, pending } = await useFetch('http://127.0.0.1:8000/api/consommateur/livraisons', {
  headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
})

function formatDate(dateString) {
  if (!dateString) return null
  return new Date(dateString).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}

function statusLabel(status) {
  const labels = { 'EnCours': 'En cours', 'Valider': 'Livrée', 'Annuler': 'Annulée' }
  return labels[status] || status
}
</script>
