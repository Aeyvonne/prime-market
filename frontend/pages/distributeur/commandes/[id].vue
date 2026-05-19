<template>
  <div class="animate-in slide-in-from-bottom duration-700">
    <div v-if="pending" class="flex items-center justify-center h-96">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#2D5A27]"></div>
    </div>

    <div v-else-if="order" class="max-w-5xl mx-auto space-y-8">
      <!-- Top Bar -->
      <div class="flex items-center justify-between">
        <NuxtLink to="/distributeur/commandes" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#2D5A27] transition-colors group">
          <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          Mes commandes
        </NuxtLink>
        
        <div v-if="order.status === 'EnCours'" class="flex gap-4">
          <button @click="handleCancel" :disabled="cancelling" class="px-6 py-2.5 bg-red-50 text-red-600 rounded-xl text-xs font-black hover:bg-red-100 transition-all active:scale-95 disabled:opacity-50">
            {{ cancelling ? 'Annulation...' : 'Annuler la commande' }}
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Order Header Card -->
          <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Commande</p>
                <h1 class="text-3xl font-black text-gray-900">#{{ order.id }}</h1>
              </div>
              <div :class="`px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest text-center ${statusStyle(order.status)}`">
                {{ order.status }}
              </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Date</p>
                <p class="text-sm font-black text-gray-800">{{ formatDate(order.date_commande) }}</p>
              </div>
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Articles</p>
                <p class="text-sm font-black text-gray-800">{{ order.lignes_commande?.length || 0 }} produits</p>
              </div>
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Livraison</p>
                <p class="text-sm font-black text-gray-800">{{ order.mode_livraison || 'Standard' }}</p>
              </div>
              <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total</p>
                <p class="text-sm font-black text-[#2D5A27]">{{ formatPrice(order.montant_total) }}</p>
              </div>
            </div>
          </div>

          <!-- Items Table -->
          <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50">
              <h3 class="text-lg font-black text-gray-900">Détails des articles</h3>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                  <tr>
                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Produit</th>
                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Qté</th>
                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Prix Unit.</th>
                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Sous-total</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                  <tr v-for="item in order.lignes_commande" :key="item.id">
                    <td class="px-8 py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#F5F0E8] rounded-xl flex items-center justify-center text-xl">📦</div>
                        <span class="text-sm font-bold text-gray-800">{{ item.produit?.nom }}</span>
                      </div>
                    </td>
                    <td class="px-8 py-4 text-center font-bold text-gray-700">{{ item.quantite }}</td>
                    <td class="px-8 py-4 text-right text-sm text-gray-500 font-medium">{{ formatPrice(item.prix_unitaire) }}</td>
                    <td class="px-8 py-4 text-right font-black text-gray-900">{{ formatPrice(item.prix_total) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-8">
          <!-- Vendeur Card -->
          <div class="bg-[#F5F0E8] p-8 rounded-[2.5rem] border border-[#2D5A27]/10">
            <h3 class="text-[10px] font-black text-[#8B7340] uppercase tracking-[0.2em] mb-6">Producteur</h3>
            <div class="flex items-center gap-4 mb-6">
              <div class="w-14 h-14 bg-[#2D5A27] rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg">
                {{ order.vendeur?.nom?.[0] || 'V' }}
              </div>
              <div>
                <p class="text-sm font-black text-gray-900">{{ order.vendeur?.nom }} {{ order.vendeur?.prenom }}</p>
                <p class="text-xs text-gray-500 font-medium">Partenaire certifié</p>
              </div>
            </div>
            <button class="w-full py-3 bg-white text-[#2D5A27] rounded-xl text-xs font-bold border border-[#2D5A27]/20 hover:bg-[#2D5A27] hover:text-white transition-all">
              Contacter le vendeur
            </button>
          </div>

          <!-- Adresse Card -->
          <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Adresse de livraison</h3>
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-[#2D5A27] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <p class="text-sm font-medium text-gray-600 leading-relaxed">
                {{ order.adresse_livraison || 'Aucune adresse spécifiée' }}
              </p>
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
const cancelling = ref(false)

const { data: order, pending, refresh } = await useFetch(`http://127.0.0.1:8000/api/distributeur/commandes/${route.params.id}`, {
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`
  }
})

async function handleCancel() {
  if (!confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) return
  
  cancelling.value = true
  try {
    await $fetch(`http://127.0.0.1:8000/api/distributeur/commandes/${route.params.id}/annuler`, {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    alert('Commande annulée avec succès.')
    refresh()
  } catch (err) {
    console.error('Erreur annulation:', err)
    alert('Impossible d\'annuler la commande.')
  } finally {
    cancelling.value = false
  }
}

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
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function formatPrice(price) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price).replace('XOF', 'FCFA')
}
</script>
