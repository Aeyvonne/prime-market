<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">Historique des <span class="text-[#2D5A27]">Paiements</span></h1>
        <p class="text-gray-500 font-medium mt-1">Consultez vos transactions et factures.</p>
      </div>
      <div class="flex items-center gap-4 bg-[#2D5A27] text-white px-6 py-4 rounded-3xl shadow-xl shadow-[#2D5A27]/20">
        <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-xl">💰</div>
        <div>
          <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Total payé (Mois)</p>
          <p class="text-lg font-black">{{ formatPrice(totalMonth) }}</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div v-if="pending" class="p-12 space-y-4">
        <div v-for="i in 5" :key="i" class="h-12 bg-gray-50 animate-pulse rounded-xl"></div>
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Référence</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Commande</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Méthode</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Montant</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Statut</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="paiement in paiements" :key="paiement.id" class="hover:bg-[#F5F0E8]/30 transition-colors group">
              <td class="px-8 py-6"><span class="text-sm font-bold text-gray-900 uppercase">#PAY-{{ paiement.id }}</span></td>
              <td class="px-8 py-6">
                <NuxtLink :to="`/consommateur/commandes/${paiement.commande_id}`" class="text-sm font-bold text-[#2D5A27] hover:underline">Cmd #{{ paiement.commande_id }}</NuxtLink>
              </td>
              <td class="px-8 py-6"><span class="text-sm text-gray-500 font-medium">{{ formatDate(paiement.date_transaction) }}</span></td>
              <td class="px-8 py-6">
                <span class="text-xs font-bold text-gray-700 bg-gray-100 px-3 py-1 rounded-lg">{{ paiement.methode_paiement || 'Mobile Money' }}</span>
              </td>
              <td class="px-8 py-6"><span class="text-sm font-black text-gray-900">{{ formatPrice(paiement.montant) }}</span></td>
              <td class="px-8 py-6 text-center">
                <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-center ${paiement.status === 'Payé' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}`">{{ paiement.status }}</span>
              </td>
            </tr>
            <tr v-if="!pending && paiements.length === 0">
              <td colspan="6" class="px-8 py-20 text-center text-gray-400 font-medium italic">Aucun historique de paiement disponible.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'dashboard' })

const totalMonth = ref(0)

const { data: paiements, pending } = await useFetch('http://127.0.0.1:8000/api/consommateur/paiements', {
  headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
})

watch(paiements, (val) => {
  if (!val) return
  const currentMonth = new Date().getMonth()
  totalMonth.value = val
    .filter(p => new Date(p.date_transaction).getMonth() === currentMonth)
    .reduce((sum, p) => sum + parseFloat(p.montant || 0), 0)
}, { immediate: true })

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatPrice(price) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}
</script>
