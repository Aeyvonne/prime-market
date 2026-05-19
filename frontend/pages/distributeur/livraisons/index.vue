<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">Mes <span class="text-[#2D5A27]">Livraisons</span></h1>
        <p class="text-gray-500 font-medium mt-1">Suivez l'état d'expédition de vos commandes en temps réel.</p>
      </div>
    </div>

    <!-- Active Deliveries -->
    <div class="grid grid-cols-1 gap-6">
      <div v-if="pending" class="space-y-4">
        <div v-for="i in 3" :key="i" class="h-32 bg-white animate-pulse rounded-[2rem]"></div>
      </div>

      <div v-else-if="livraisons.length > 0" v-for="liv in livraisons" :key="liv.id" 
        class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-8 group hover:shadow-xl transition-all duration-500"
      >
        <div class="flex items-center gap-6">
          <div :class="`w-16 h-16 rounded-[1.5rem] flex items-center justify-center text-2xl shadow-inner ${liv.statut === 'Livrée' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600 animate-pulse'}`">
            {{ liv.statut === 'Livrée' ? '✅' : '🚚' }}
          </div>
          <div>
            <div class="flex items-center gap-3 mb-1">
              <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Livraison #{{ liv.id }}</span>
              <span :class="`px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-wider ${liv.statut === 'Livrée' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}`">
                {{ liv.statut }}
              </span>
            </div>
            <h3 class="text-lg font-black text-gray-900 leading-tight">Commande #{{ liv.commande_id }}</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">Destination: {{ liv.commande?.adresse_livraison || 'Non spécifiée' }}</p>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="flex-grow max-w-md hidden lg:block">
          <div class="flex justify-between text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
            <span>Préparation</span>
            <span>En Route</span>
            <span>Livré</span>
          </div>
          <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div :class="`h-full transition-all duration-1000 ${liv.statut === 'Livrée' ? 'w-full bg-emerald-500' : 'w-1/2 bg-amber-500'}`"></div>
          </div>
        </div>

        <NuxtLink :to="`/distributeur/livraisons/${liv.id}`" class="px-6 py-3 bg-[#F5F0E8] text-[#2D5A27] rounded-xl text-xs font-black hover:bg-[#2D5A27] hover:text-white transition-all text-center">
          Détails du suivi
        </NuxtLink>
      </div>

      <!-- Empty State -->
      <div v-else-if="!pending" class="py-20 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
        <div class="text-6xl mb-4 opacity-20">📦</div>
        <h3 class="text-xl font-black text-gray-900">Aucune livraison</h3>
        <p class="text-gray-500 font-medium mt-2">Vos expéditions apparaîtront ici dès qu'elles seront initiées.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

const { data: livraisons, pending } = await useFetch('http://127.0.0.1:8000/api/distributeur/livraisons', {
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`
  }
})
</script>
