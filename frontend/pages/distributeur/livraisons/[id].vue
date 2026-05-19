<template>
  <div class="animate-in slide-in-from-bottom duration-700">
    <div v-if="pending" class="flex items-center justify-center h-96">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#2D5A27]"></div>
    </div>

    <div v-else-if="livraison" class="max-w-4xl mx-auto space-y-8">
      <!-- Back -->
      <NuxtLink to="/distributeur/livraisons" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#2D5A27] transition-colors group">
        <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour aux livraisons
      </NuxtLink>

      <div class="bg-white p-8 md:p-12 rounded-[3rem] shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
          <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Suivi Expédition</p>
            <h1 class="text-3xl font-black text-gray-900">#{{ livraison.id }}</h1>
          </div>
          <div :class="`px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest ${livraison.statut === 'Livrée' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700 animate-pulse'}`">
            {{ livraison.statut }}
          </div>
        </div>

        <!-- Tracking Steps -->
        <div class="relative mb-16 px-4">
          <div class="absolute top-5 left-8 right-8 h-1 bg-gray-100 rounded-full">
            <div :class="`h-full bg-[#2D5A27] transition-all duration-1000 ${livraison.statut === 'Livrée' ? 'w-full' : 'w-1/2'}`"></div>
          </div>
          
          <div class="relative flex justify-between">
            <div v-for="(step, idx) in steps" :key="idx" class="flex flex-col items-center gap-3">
              <div :class="`w-10 h-10 rounded-full flex items-center justify-center text-lg z-10 border-4 border-white ${isStepDone(step.status) ? 'bg-[#2D5A27] text-white shadow-lg' : 'bg-gray-100 text-gray-400'}`">
                {{ step.icon }}
              </div>
              <p class="text-[10px] font-black uppercase tracking-wider text-center" :class="isStepDone(step.status) ? 'text-gray-900' : 'text-gray-400'">
                {{ step.label }}
              </p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 pt-8 border-t border-gray-50">
          <!-- Logistics Info -->
          <div class="space-y-6">
            <h3 class="text-lg font-black text-gray-900">Informations Logistiques</h3>
            <div class="space-y-4">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-[#2D5A27]">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                  <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Transporteur</p>
                  <p class="text-sm font-bold text-gray-700">{{ livraison.mission?.transporteur?.user?.nom || 'En cours d\'affectation...' }}</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-[#2D5A27]">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                  <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date de départ estimée</p>
                  <p class="text-sm font-bold text-gray-700">{{ formatDate(livraison.date_livraison) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="space-y-6">
            <h3 class="text-lg font-black text-gray-900">Résumé de la Commande</h3>
            <div class="bg-[#F5F0E8]/50 p-6 rounded-2xl border border-[#2D5A27]/10">
              <p class="text-sm font-bold text-gray-700 mb-1">Commande #{{ livraison.commande_id }}</p>
              <p class="text-xs text-gray-500 font-medium mb-4">{{ livraison.commande?.lignes_commande?.length }} articles</p>
              <NuxtLink :to="`/distributeur/commandes/${livraison.commande_id}`" class="text-xs font-black text-[#2D5A27] hover:underline">Voir la commande originale →</NuxtLink>
            </div>
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

const route = useRoute()
const steps = [
  { label: 'Préparée', status: 'En préparation', icon: '📦' },
  { label: 'En Transit', status: 'En cours', icon: '🚚' },
  { label: 'Livrée', status: 'Livrée', icon: '🏁' }
]

const { data: livraison, pending } = await useFetch(`http://127.0.0.1:8000/api/distributeur/livraisons/${route.params.id}`, {
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`
  }
})

function isStepDone(stepStatus) {
  if (!livraison.value) return false
  if (livraison.value.statut === 'Livrée') return true
  if (stepStatus === 'En préparation') return true
  if (stepStatus === 'En cours' && livraison.value.statut === 'En cours') return true
  return false
}

function formatDate(dateString) {
  if (!dateString) return 'À définir'
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}
</script>
