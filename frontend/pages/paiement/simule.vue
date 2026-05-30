<template>
  <div class="min-h-screen bg-[#F5F0E8] flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl p-10 max-w-md w-full text-center">
      <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <h1 class="text-3xl font-black text-gray-900 font-serif mb-2">Paiement simulé</h1>
      <p class="text-gray-500 font-medium mb-4">Mode simulation — aucune transaction réelle.</p>

      <div class="bg-gray-50 rounded-2xl p-5 mb-6 text-left space-y-2">
        <div class="flex justify-between text-sm"><span class="text-gray-500">Commande</span><span class="font-bold">#{{ commandeData?.commande_id || '—' }}</span></div>
        <div class="flex justify-between text-sm"><span class="text-gray-500">Méthode</span><span class="font-bold">{{ commandeData?.methode || '—' }}</span></div>
        <div class="flex justify-between text-sm"><span class="text-gray-500">Montant</span><span class="font-bold text-[#2D5A27]">{{ formatPrice(commandeData?.montant) }}</span></div>
      </div>

      <p v-if="errorMsg" class="text-red-500 text-sm font-medium mb-4">{{ errorMsg }}</p>

      <button @click="confirmer" :disabled="submitting"
        class="w-full py-4 bg-[#2D5A27] text-white rounded-2xl font-bold text-lg shadow-xl hover:-translate-y-1 transition-all active:scale-95 disabled:opacity-50">
        {{ submitting ? 'Confirmation...' : 'Confirmer le paiement simulé' }}
      </button>
    </div>
  </div>
</template>

<script setup>
const route = useRoute()
const submitting = ref(false)
const errorMsg = ref('')
const commandeData = ref(null)

onMounted(() => {
  const token = route.query.token
  if (!token) {
    errorMsg.value = 'Token de paiement manquant'
    return
  }
  try {
    commandeData.value = JSON.parse(atob(token))
  } catch {
    errorMsg.value = 'Token invalide'
  }
})

async function confirmer() {
  if (!commandeData.value) return
  submitting.value = true
  errorMsg.value = ''
  try {
    await $fetch('http://127.0.0.1:8000/api/paiements/simuler/confirmer', {
      method: 'POST',
      body: {
        commande_id: commandeData.value.commande_id,
        methode: commandeData.value.methode,
      },
    })
    navigateTo(`/paiement/succes?commande_id=${commandeData.value.commande_id}`)
  } catch (e) {
    errorMsg.value = e.data?.message || 'Erreur lors de la confirmation'
  } finally {
    submitting.value = false
  }
}

function formatPrice(price) {
  if (!price) return '0 FCFA'
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}
</script>
