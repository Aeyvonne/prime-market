<template>
  <div class="space-y-8 animate-in fade-in duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <h1 class="text-3xl font-black text-gray-900 font-serif">Mes <span class="text-[#2D5A27]">Commandes</span></h1>
        <p class="text-gray-500 font-medium mt-1">Suivez vos achats auprès des producteurs.</p>
      </div>
      
      <!-- Filtres -->
      <div class="flex items-center gap-2 p-1 bg-white rounded-2xl shadow-sm border border-gray-100">
        <button 
          v-for="status in statusFilters" 
          :key="status.value"
          @click="selectedStatus = status.value"
          class="px-5 py-2.5 rounded-xl text-xs font-black transition-all"
          :class="[selectedStatus === status.value ? 'bg-[#2D5A27] text-white shadow-lg shadow-[#2D5A27]/20' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50']"
        >
          {{ status.label }}
        </button>
      </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
      <div v-if="pending" class="p-12 space-y-4">
        <div v-for="i in 5" :key="i" class="h-12 bg-gray-50 animate-pulse rounded-xl"></div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Vendeur</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Montant</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Statut</th>
              <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-[#F5F0E8]/30 transition-colors group">
              <td class="px-8 py-6">
                <span class="text-sm font-bold text-gray-900">#{{ order.id }}</span>
              </td>
              <td class="px-8 py-6">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-[#2D5A27] flex items-center justify-center text-white text-[10px] font-black">
                    {{ order.vendeur?.nom?.[0] || 'V' }}
                  </div>
                  <div>
                    <p class="text-sm font-bold text-gray-900 leading-none mb-1">{{ order.vendeur?.nom }}</p>
                    <p class="text-[10px] text-gray-400 font-bold">Producteur</p>
                  </div>
                </div>
              </td>
              <td class="px-8 py-6">
                <span class="text-sm text-gray-500 font-medium">{{ formatDate(order.date_commande) }}</span>
              </td>
              <td class="px-8 py-6">
                <span class="text-sm font-black text-gray-900">{{ formatPrice(order.montant_total) }}</span>
              </td>
              <td class="px-8 py-6">
                <span :class="`inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-center ${statusStyle(order.status)}`">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-8 py-6 text-right">
                <NuxtLink :to="`/distributeur/commandes/${order.id}`" class="p-2.5 bg-gray-100 text-gray-600 hover:bg-[#2D5A27] hover:text-white rounded-xl transition-all inline-flex items-center justify-center">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </NuxtLink>
              </td>
            </tr>
            <tr v-if="!pending && filteredOrders.length === 0">
              <td colspan="6" class="px-8 py-20 text-center text-gray-400 font-medium italic">
                Aucune commande trouvée.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard'
})

const selectedStatus = ref('All')
const statusFilters = [
  { label: 'Tous', value: 'All' },
  { label: 'En Cours', value: 'EnCours' },
  { label: 'Validées', value: 'Valider' },
  { label: 'Annulées', value: 'Annuler' }
]

const { data: orders, pending } = await useFetch('http://127.0.0.1:8000/api/distributeur/commandes', {
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`
  }
})

const filteredOrders = computed(() => {
  if (!orders.value) return []
  if (selectedStatus.value === 'All') return orders.value
  return orders.value.filter(o => o.status === selectedStatus.value)
})

function statusStyle(status) {
  switch (status) {
    case 'Valider': return 'bg-emerald-100 text-emerald-700'
    case 'Annuler': return 'bg-red-100 text-red-700'
    case 'EnCours': return 'bg-amber-100 text-amber-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

function formatPrice(price) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}
</script>
