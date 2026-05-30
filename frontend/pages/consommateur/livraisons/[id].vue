<template>
  <div class="animate-in slide-in-from-bottom duration-700">
    <div v-if="pending" class="flex items-center justify-center h-96">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#2D5A27]"></div>
    </div>
    <div v-else-if="livraison" class="max-w-4xl mx-auto space-y-8">
      <NuxtLink to="/consommateur/livraisons" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#2D5A27] transition-colors group">
        <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Mes livraisons
      </NuxtLink>

      <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-8">
          <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Livraison</p>
            <h1 class="text-3xl font-black text-gray-900">#{{ livraison.id }}</h1>
          </div>
          <span :class="`px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest ${livraison.status === 'Valider' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}`">{{ statusLabel(livraison.status) }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Livraison estimée</p>
            <p class="text-sm font-black text-gray-800">{{ formatDate(livraison.date_livraison_estimee) || 'Non définie' }}</p>
          </div>
          <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Transporteur</p>
            <p class="text-sm font-black text-gray-800">{{ livraison.mission?.transporteur?.user?.nom || 'Non assigné' }}</p>
          </div>
        </div>

        <div class="bg-gray-50 rounded-2xl p-6">
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Adresse de livraison</p>
          <p class="text-sm font-bold text-gray-800">{{ livraison.adresse_livraison || livraison.commande?.adresse_livraison || 'Non spécifiée' }}</p>
        </div>
      </div>

      <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
        <h3 class="text-lg font-black text-gray-900 mb-6">Articles de la commande</h3>
        <div class="divide-y divide-gray-50">
          <div v-for="ligne in livraison.commande?.lignes_commande" :key="ligne.id" class="py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 rounded-lg bg-[#F5F0E8] flex items-center justify-center text-[#2D5A27] font-black">{{ ligne.produit?.nom?.[0] || 'P' }}</div>
              <div>
                <p class="text-sm font-bold text-gray-900">{{ ligne.produit?.nom }}</p>
                <p class="text-xs text-gray-400 font-medium">x{{ ligne.quantite }}</p>
              </div>
            </div>
            <NuxtLink v-if="ligne.produit" :to="`/catalogue/${ligne.produit.id}`" class="text-xs font-bold text-[#2D5A27] hover:underline">Voir la traçabilité</NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const route = useRoute()

const { data: livraison, pending } = await useFetch(`http://127.0.0.1:8000/api/consommateur/livraisons/${route.params.id}`, {
  headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
})

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}

function statusLabel(status) {
  const labels = { 'EnCours': 'En cours', 'Valider': 'Livrée', 'Annuler': 'Annulée' }
  return labels[status] || status
}
</script>
