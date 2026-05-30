<template>
  <div class="min-h-screen bg-[#F5F0E8] flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl p-10 max-w-md w-full text-center">
      <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </div>
      <h1 class="text-3xl font-black text-gray-900 font-serif mb-2">Paiement échoué</h1>
      <p class="text-gray-500 font-medium mb-2">Le paiement n'a pas pu aboutir. Veuillez réessayer.</p>
      <p v-if="commandeId" class="text-sm text-gray-400">Commande #{{ commandeId }}</p>
      <div class="flex gap-3 justify-center mt-8">
        <NuxtLink :to="commandeId ? `/consommateur/commandes/${commandeId}` : '/consommateur/commandes'"
          class="px-6 py-3 bg-[#2D5A27] text-white rounded-2xl font-bold text-sm shadow-lg hover:shadow-xl transition-all">
          Réessayer
        </NuxtLink>
        <NuxtLink to="/catalogue"
          class="px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-200 transition-all">
          Continuer mes achats
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
const route = useRoute()
const commandeId = route.query.commande_id || null
</script>
